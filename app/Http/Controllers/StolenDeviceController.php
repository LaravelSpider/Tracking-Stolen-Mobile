<?php

namespace App\Http\Controllers;

use App\Models\StolenDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeviceMatchFound;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class StolenDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = StolenDevice::with(['reporter', 'assignedAgent']);

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($devices);
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ]);

        $device = StolenDevice::create([
            ...$request->all(),
            'reported_by' => auth()->id,
        ]);

        return response()->json($device->load(['reporter', 'assignedAgent']), 201);
    }

    public function show(StolenDevice $device)
    {
        return response()->json($device->load(['reporter', 'assignedAgent']));
    }

    public function update(Request $request, StolenDevice $device)
    {
        $request->validate([
            'status' => 'sometimes|in:missing,reported,investigating,found',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'notes' => 'sometimes|nullable|string|max:1000',
        ]);

        $oldStatus = $device->status;
        $device->update($request->all());

        // Send notification if status changed to found
        if ($oldStatus !== 'found' && $request->status === 'found') {
            $device->update(['found_at' => now()]);
            Notification::send($device->reporter, new DeviceMatchFound($device));
        }

        return response()->json($device->load(['reporter', 'assignedAgent']));
    }

    public function search(Request $request)
    {
        $request->validate([
            'imei' => 'required|string|size:15',
        ]);

        $device = StolenDevice::byImei($request->imei)->with(['reporter', 'assignedAgent'])->first();

        if ($device) {
            return response()->json([
                'found' => true,
                'device' => $device,
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'No matching device found in our database.',
        ]);
    }

    public function generateReport(Request $request)
    {
        $query = StolenDevice::with(['reporter', 'assignedAgent']);

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $devices = $query->get();

        $pdf = Pdf::loadView('reports.stolen-devices', compact('devices'));

        return $pdf->download('stolen-devices-report.pdf');
    }

    public function dashboard()
    {
        $stats = [
            'total_devices' => StolenDevice::count(),
            'missing' => StolenDevice::byStatus('missing')->count(),
            'reported' => StolenDevice::byStatus('reported')->count(),
            'investigating' => StolenDevice::byStatus('investigating')->count(),
            'found' => StolenDevice::byStatus('found')->count(),
            'recent_reports' => StolenDevice::with(['reporter'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
