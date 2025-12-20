<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Query;
use App\Models\Complaint;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache dashboard stats for 5 minutes
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'totalEmployees' => Employee::count(),
                'activeEmployees' => Employee::where('employment_status', 'active')->count(),
                'pendingLeaves' => Leave::where('status', 'pending')->count(),
                'openQueries' => Query::where('status', 'pending')->count(),
                'openComplaints' => Complaint::where('status', 'open')->count(),
            ];
        });

        return view('admin.dashboard', $stats);
    }
}