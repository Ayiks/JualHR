<?php

// app/Http/Controllers/Employee/PolicyViewController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\PolicyAcknowledgment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PolicyViewController extends Controller
{
    /**
     * Display a listing of active policies
     */
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;

        $query = Policy::active()->with('acknowledgments');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $policies = $query->latest('effective_date')->paginate(15);

        // Mark which policies have been acknowledged by this employee
        foreach ($policies as $policy) {
            $policy->is_acknowledged = $policy->isAcknowledgedBy($employee);
        }

        // Get statistics
        $stats = [
            'total' => Policy::active()->count(),
            'acknowledged' => PolicyAcknowledgment::where('employee_id', $employee->id)->count(),
            'pending' => Policy::active()
                ->where('requires_acknowledgment', true)
                ->whereDoesntHave('acknowledgments', function($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count(),
        ];

        $categories = Policy::getCategories();

        return view('employee.policies.index', compact('policies', 'stats', 'categories'));
    }

    /**
     * Display the specified policy
     */
    public function show(Policy $policy)
    {
        // Only show active policies to employees
        if ($policy->status !== 'active') {
            abort(404);
        }

        $employee = Auth::user()->employee;
        $policy->load('acknowledgments');

        $isAcknowledged = $policy->isAcknowledgedBy($employee);
        $acknowledgment = $policy->acknowledgments()
            ->where('employee_id', $employee->id)
            ->first();

        return view('employee.policies.show', compact('policy', 'isAcknowledged', 'acknowledgment'));
    }

    /**
     * Acknowledge a policy
     */
    public function acknowledge(Request $request, Policy $policy)
    {
        // Only active policies can be acknowledged
        if ($policy->status !== 'active') {
            return back()->with('error', 'This policy is not active.');
        }

        // Check if policy requires acknowledgment
        if (!$policy->requires_acknowledgment) {
            return back()->with('error', 'This policy does not require acknowledgment.');
        }

        $employee = Auth::user()->employee;

        // Check if already acknowledged
        if ($policy->isAcknowledgedBy($employee)) {
            return back()->with('info', 'You have already acknowledged this policy.');
        }

        try {
            PolicyAcknowledgment::create([
                'policy_id' => $policy->id,
                'employee_id' => $employee->id,
                'acknowledged_at' => now(),
                'ip_address' => $request->ip(),
                'notes' => $request->input('notes'),
            ]);

            return redirect()
                ->route('employee.policies.show', $policy)
                ->with('success', 'Policy acknowledged successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to acknowledge policy: ' . $e->getMessage());
        }
    }

    /**
     * Download policy document
     */
    public function download(Policy $policy)
    {
        // Only active policies can be downloaded by employees
        if ($policy->status !== 'active') {
            abort(404);
        }

        if (!$policy->document_path || !Storage::disk('private')->exists($policy->document_path)) {
            return back()->with('error', 'Policy document not found.');
        }

        return Storage::disk('private')->download(
            $policy->document_path,
            $policy->code . '_v' . $policy->version . '.pdf'
        );
    }
}