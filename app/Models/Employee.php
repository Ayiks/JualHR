<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'department_id',
        'line_manager_id',
        'job_title',
        'employment_type',
        'employment_status',
        'date_of_joining',
        'date_of_leaving',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'profile_photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'date_of_leaving' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lineManager()
    {
        return $this->belongsTo(Employee::class, 'line_manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'line_manager_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function queriesReceived()
    {
        return $this->hasMany(Query::class, 'employee_id');
    }

    public function queriesIssued()
    {
        return $this->hasMany(Query::class, 'issued_by');
    }

    public function queryResponses()
    {
        return $this->hasMany(QueryResponse::class, 'responded_by');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function complaintResponses()
    {
        return $this->hasMany(ComplaintResponse::class, 'responded_by');
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function onboardingDocuments()
    {
        return $this->hasMany(OnboardingDocument::class);
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    // public function getInitialsAttribute()
    // {
    //     return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    // }

    public function getIsActiveAttribute()
    {
        return $this->employment_status === 'active';
    }


    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeUnderManager($query, $managerId)
    {
        return $query->where('line_manager_id', $managerId);
    }

    // Helper Methods
    public function isManager()
    {
        return $this->subordinates()->exists();
    }

    public function canApproveLeave()
    {
        return $this->isManager() || $this->user?->hasRole(['super_admin', 'hr_admin']);
    }

    /**
     * Get today's attendance record
     */
    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->whereDate('date', today());
    }


    /**
 * Get employee's policy acknowledgments
 */
public function policyAcknowledgments()
{
    return $this->hasMany(PolicyAcknowledgment::class);
}

/**
 * Scope for active employees
 */
public function scopeActive($query)
{
    return $query->where('employment_status', 'active');
}

/**
 * Get full name attribute
 */
public function getFullNameAttribute()
{
    return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
}

/**
 * Get initials attribute
 */
public function getInitialsAttribute()
{
    $firstInitial = $this->first_name ? substr($this->first_name, 0, 1) : '';
    $lastInitial = $this->last_name ? substr($this->last_name, 0, 1) : '';
    return strtoupper($firstInitial . $lastInitial);
}
}
