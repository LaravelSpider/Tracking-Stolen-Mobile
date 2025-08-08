<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StolenDevice;
use App\Models\User;
use App\Notifications\DeviceMatchFound;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class StolenDeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StolenDevice::with(['reporter:id,name,email', 'assignedAgent:id,name,email']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // User can only see their own devices unless they're admin/security
        $user = $request->user();
        if (!$user->hasAnyRole(['admin', 'security_agency'])) {
            $query->where('reported_by', $user->id);
        }

        $devices = $query->orderBy('priority', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'imei' => 'required|string|size:15|unique:stolen_devices',
            'serial_number' => 'nullable|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'color' => 'nullable|string|max:30',
            'lost_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',
            'loss_location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'reward_amount' => 'nullable|numeric|min:0|max:999999.99',
            'contact_info' => 'nullable|string|max:500',
            'priority' => 'nullable|integer|between:1,4',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('device-images', 'public');
                $imagePaths[] = $path;
            }
        }

        $device = StolenDevice::create([
            ...$validated,
            'reported_by' => auth()->id(),
            'images' => $imagePaths,
            'priority' => $validated['priority'] ?? 1,
        ]);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($device)
            ->log('Device reported as stolen');

        return response()->json([
            'device' => $device->load(['reporter', 'assignedAgent']),
            'message' => 'Device reported successfully',
        ], 201);
    }

    public function show(StolenDevice $device): JsonResponse
    {
        $user = auth()->user();
        
        // Check permissions
        if (!$user->hasAnyRole(['admin', 'security_agency']) && $device->reported_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($device->load(['reporter', 'assignedAgent', 'activities.user']));
    }

    public function update(Request $request, StolenDevice $device): JsonResponse
    {
        $user = auth()->user();
        
        // Check permissions
        if (!$user->hasAnyRole(['admin', 'security_agency']) && $device->reported_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:missing,reported,investigating,found',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'notes' => 'sometimes|nullable|string|max:1000',
            'priority' => 'sometimes|integer|between:1,4',
        ]);

        $oldStatus = $device->status;
        $device->update($validated);

        // Handle status change to found
        if ($oldStatus !== 'found' && ($validated['status'] ?? null) === 'found') {
            $device->update(['found_at' => now()]);
            
            // Send notification
            Notification::send($device->reporter, new DeviceMatchFound($device));
            
            // Log activity
            activity()
                ->causedBy($user)
                ->performedOn($device)
                ->log('Device marked as found');
        }

        return response()->json([
            'device' => $device->load(['reporter', 'assignedAgent']),
            'message' => 'Device updated successfully',
        ]);
    }

    public function destroy(StolenDevice $device): JsonResponse
    {
        $user = auth()->user();
        
        // Only admin or device owner can delete
        if (!$user->hasRole('admin') && $device->reported_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated images
        if ($device->images) {
            foreach ($device->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $device->delete();

        // Log activity
        activity()
            ->causedBy($user)
            ->log("Device deleted: {$device->brand} {$device->model} (IMEI: {$device->imei})");

        return response()->json(['message' => 'Device deleted successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'imei' => 'required|string|size:15',
        ]);

        $device = StolenDevice::byImei($validated['imei'])
                             ->with(['reporter:id,name,email', 'assignedAgent:id,name,email'])
                             ->first();

        if ($device) {
            // Log search activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($device)
                ->log('Device searched by IMEI');

            return response()->json([
                'found' => true,
                'device' => $device,
                'message' => 'Device found in stolen database',
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'No matching device found in our database.',
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $user = auth()->user();
        
        $query = StolenDevice::query();
        
        // Filter by user role
        if (!$user->hasAnyRole(['admin', 'security_agency'])) {
            $query->where('reported_by', $user->id);
        }

        $stats = [
            'total_devices' => $query->count(),
            'missing' => $query->byStatus('missing')->count(),
            'reported' => $query->byStatus('reported')->count(),
            'investigating' => $query->byStatus('investigating')->count(),
            'found' => $query->byStatus('found')->count(),
            'high_priority' => $query->highPriority()->count(),
            'recent_reports' => $query->with(['reporter:id,name,email'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get(),
            'monthly_stats' => $this->getMonthlyStats($query),
        ];

        return response()->json($stats);
    }

    public function generateReport(Request $request): \Illuminate\Http\Response
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'security_agency'])) {
            abort(403, 'Unauthorized');
        }

        $query = StolenDevice::with(['reporter', 'assignedAgent']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $devices = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('reports.stolen-devices', [
            'devices' => $devices,
            'filters' => $request->only(['status', 'date_from', 'date_to']),
            'generated_by' => $user,
            'generated_at' => now(),
        ]);

        // Log activity
        activity()
            ->causedBy($user)
            ->log('Generated stolen devices report');

        return $pdf->download('stolen-devices-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getMonthlyStats($query): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'count' => (clone $query)->whereYear('created_at', $date->year)
                                       ->whereMonth('created_at', $date->month)
                                       ->count(),
            ];
        }
        return $months;
    }
}
