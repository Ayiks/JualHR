<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\QueryController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\OnboardingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;

// Manager Controllers
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\TeamController;
use App\Http\Controllers\Manager\LeaveApprovalController;

// Employee Controllers
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\ComplaintController as EmployeeComplaintController;
use App\Http\Controllers\Employee\PolicyViewController;
use App\Http\Controllers\Employee\QueryResponseController;
use App\Http\Controllers\Employee\SurveyResponseController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\DocumentController as EmployeeDocumentController;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================================================
// AUTHENTICATION ROUTES (must be BEFORE authenticated routes)
// ============================================================================
require __DIR__.'/auth.php';

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================
Route::middleware(['auth', 'active_employee'])->group(function () {
    
    // ========================================================================
    // ADMIN ROUTES
    // ========================================================================
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Employee Management
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::get('employees/{employee}/print', [EmployeeController::class, 'print'])->name('employees.print');
        
        // Department Management
        Route::resource('departments', DepartmentController::class);
        
        // Policy Management
        Route::resource('policies', PolicyController::class);
        Route::get('policies/{policy}/download', [PolicyController::class, 'download'])->name('policies.download');
        
        // Leave Management
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/', [AdminLeaveController::class, 'index'])->name('index');
            Route::get('/{leave}', [AdminLeaveController::class, 'show'])->name('show');
            Route::put('/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('approve');
            Route::put('/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('reject');
        });
        
        // Leave Types
        Route::resource('leave-types', LeaveTypeController::class);
        
        // Leave Balances
        Route::get('leave-balances', [AdminLeaveController::class, 'balances'])->name('leave-balances.index');
        Route::post('leave-balances/update', [AdminLeaveController::class, 'updateBalance'])->name('leave-balances.update');
        
        // Query/Warning Management
        Route::resource('queries', QueryController::class);
        Route::put('queries/{query}/close', [QueryController::class, 'close'])->name('queries.close');
        
        // Complaint Management
        Route::resource('complaints', AdminComplaintController::class);
        Route::put('complaints/{complaint}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');
        Route::put('complaints/{complaint}/resolve', [AdminComplaintController::class, 'resolve'])->name('complaints.resolve');
        Route::put('complaints/{complaint}/close', [AdminComplaintController::class, 'close'])->name('complaints.close');
        Route::post('complaints/{complaint}/respond', [AdminComplaintController::class, 'respond'])->name('complaints.respond');
        
        // Survey Management
        Route::resource('surveys', SurveyController::class);
        Route::get('surveys/{survey}/responses', [SurveyController::class, 'responses'])->name('surveys.responses');
        Route::get('surveys/{survey}/export', [SurveyController::class, 'export'])->name('surveys.export');
        
        // Onboarding Management
        Route::prefix('onboarding')->name('onboarding.')->group(function () {
            Route::get('/', [OnboardingController::class, 'index'])->name('index');
            Route::get('/{employee}', [OnboardingController::class, 'show'])->name('show');
            Route::post('/{employee}/documents', [OnboardingController::class, 'uploadDocument'])->name('documents.upload');
            Route::put('/documents/{document}', [OnboardingController::class, 'updateDocumentStatus'])->name('documents.update');
            Route::delete('/documents/{document}', [OnboardingController::class, 'deleteDocument'])->name('documents.delete');
        });
        
        // Attendance Management
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
            Route::get('/create', [AdminAttendanceController::class, 'create'])->name('create');
            Route::post('/', [AdminAttendanceController::class, 'store'])->name('store');
            Route::get('/{attendance}/edit', [AdminAttendanceController::class, 'edit'])->name('edit');
            Route::put('/{attendance}', [AdminAttendanceController::class, 'update'])->name('update');
            Route::get('/export', [AdminAttendanceController::class, 'export'])->name('export');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
            Route::get('/leaves', [ReportController::class, 'leaves'])->name('leaves');
            Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('/queries', [ReportController::class, 'queries'])->name('queries');
            Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });
    });
    
    // ========================================================================
    // MANAGER ROUTES
    // ========================================================================
    Route::middleware(['manager'])->prefix('manager')->name('manager.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
        
        // Team Management
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::get('/team/{employee}', [TeamController::class, 'show'])->name('team.show');
        Route::get('/team/attendance', [TeamController::class, 'attendance'])->name('team.attendance');
        
        // Leave Approvals
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/pending', [LeaveApprovalController::class, 'pending'])->name('pending');
            Route::get('/{leave}', [LeaveApprovalController::class, 'show'])->name('show');
            Route::put('/{leave}/approve', [LeaveApprovalController::class, 'approve'])->name('approve');
            Route::put('/{leave}/reject', [LeaveApprovalController::class, 'reject'])->name('reject');
        });
    });
    
    // ========================================================================
    // EMPLOYEE ROUTES (All authenticated users)
    // ========================================================================
    Route::prefix('employee')->name('employee.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        
        // Profile Management
        Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [EmployeeProfileController::class, 'uploadPhoto'])->name('profile.photo');
        
        // Leave Management
        Route::resource('leaves', LeaveRequestController::class)->except(['edit', 'update']);
        Route::put('leaves/{leave}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');
        Route::get('leave-balance', [LeaveRequestController::class, 'balance'])->name('leave-balance');
        
        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [EmployeeAttendanceController::class, 'index'])->name('index');
            Route::post('/check-in', [EmployeeAttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [EmployeeAttendanceController::class, 'checkOut'])->name('check-out');
            Route::get('/history', [EmployeeAttendanceController::class, 'history'])->name('history');
        });
        
        // Policies
        Route::get('policies', [PolicyViewController::class, 'index'])->name('policies.index');
        Route::get('policies/{policy}', [PolicyViewController::class, 'show'])->name('policies.show');
        Route::get('policies/{policy}/download', [PolicyViewController::class, 'download'])->name('policies.download');
        
        // Complaints
        Route::resource('complaints', EmployeeComplaintController::class)->except(['edit', 'update']);
        Route::post('complaints/{complaint}/response', [EmployeeComplaintController::class, 'addResponse'])->name('complaints.response');
        
        // Queries
        Route::get('queries', [QueryResponseController::class, 'index'])->name('queries.index');
        Route::get('queries/{query}', [QueryResponseController::class, 'show'])->name('queries.show');
        Route::post('queries/{query}/respond', [QueryResponseController::class, 'respond'])->name('queries.respond');
        
        // Surveys
        Route::get('surveys', [SurveyResponseController::class, 'index'])->name('surveys.index');
        Route::get('surveys/{survey}', [SurveyResponseController::class, 'show'])->name('surveys.show');
        Route::post('surveys/{survey}/submit', [SurveyResponseController::class, 'submit'])->name('surveys.submit');
        
        // Documents
        Route::get('documents', [EmployeeDocumentController::class, 'index'])->name('documents.index');
        Route::post('documents/upload', [EmployeeDocumentController::class, 'upload'])->name('documents.upload');
        Route::delete('documents/{document}', [EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');
    });
});

// ============================================================================
// PROFILE ROUTES (Laravel Breeze default)
// ============================================================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});