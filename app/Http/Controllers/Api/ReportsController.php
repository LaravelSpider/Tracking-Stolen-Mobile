<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StolenDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Get paginated reports list
     */
    public function index(Request $request)
    {
        $query = StolenDevice::with(['user:id,name,email'])
            ->select('id', 'brand', 'model', 'imei', 'status', 'description', 'location', 'reported_by', 'created_at');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Role-based access control using User model methods
        /**
         * @var User $user
         */
        $user = Auth::user();
        
        if ($user->isSecurityAgency()) {
            // Security agencies can see all reports
        } elseif ($user->isAdmin()) {
            // Admins can see all reports
        } else {
            // Regular users can only see their own reports
            $query->where('reported_by', $user->id);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        // Transform the data to include reporter info
        $reports->getCollection()->transform(function ($report) {
            return [
                'id' => $report->id,
                'brand' => $report->brand,
                'model' => $report->model,
                'imei' => $report->imei,
                'status' => $report->status,
                'description' => $report->description,
                'location' => $report->location,
                'created_at' => $report->created_at,
                'reporter' => [
                    'name' => $report->user->name,
                    'email' => $report->user->email,
                ]
            ];
        });

        return response()->json($reports);
    }

    /**
     * Get reports statistics
     */
    public function stats()
    {
        $user = Auth::user();
        $query = StolenDevice::query();

        // Apply role-based filtering for stats
        /**
         * @var User $user
         */
        if ($user->isUser()) {
            $query->where('reported_by', $user->id);
        }

        $stats = [
            'total_reports' => $query->count(),
            'pending_reports' => (clone $query)->where('status', 'stolen')->count(),
            'resolved_reports' => (clone $query)->where('status', 'recovered')->count(),
            'this_month' => (clone $query)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export reports to Excel
     */
    public function export(Request $request)
    {
        $query = StolenDevice::with(['user:id,name,email'])
            ->select('id', 'brand', 'model', 'imei', 'status', 'description', 'location', 'reported_by', 'created_at');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Role-based access control
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->isUser()) {
            $query->where('reported_by', $user->id);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // Transform data for export
        $exportData = $reports->map(function ($report) {
            return [
                'ID' => $report->id,
                'Brand' => $report->brand,
                'Model' => $report->model,
                'IMEI' => $report->imei,
                'Status' => $report->status,
                'Description' => $report->description,
                'Location' => $report->location,
                'Reporter Name' => $report->user->name,
                'Reporter Email' => $report->user->email,
                'Reported Date' => $report->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data' => $exportData,
            'filename' => 'reports-' . now()->format('Y-m-d') . '.csv'
        ]);
    }

    /**
     * Update report status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:stolen,investigating,recovered,closed'
        ]);

        $report = StolenDevice::findOrFail($id);
        
        // Check permissions using User model methods
        /**
         * @var User $user
         */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSecurityAgency()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $report->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        // Log the status change
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'status_updated',
            'description' => "Updated device status to {$request->status}",
            'device_id' => $report->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'report' => $report->fresh()
        ]);
    }
}
