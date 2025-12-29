<?php

// app/Http/Controllers/Admin/PolicyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\PolicyVersion;
use App\Models\PolicyAcknowledgment;  // ← Important
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies
     */
    public function index(Request $request)
    {
        $query = Policy::with(['creator', 'acknowledgments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $policies = $query->latest()->paginate(15);

        // Calculate stats
        $stats = [
            'total' => Policy::count(),
            'active' => Policy::active()->count(),
            'draft' => Policy::draft()->count(),
            'archived' => Policy::archived()->count(),
        ];

        return view('admin.policies.index', compact('policies', 'stats'));
    }

    /**
     * Show the form for creating a new policy
     */
    public function create()
    {
        $categories = Policy::getCategories();
        return view('admin.policies.create', compact('categories'));
    }

    /**
     * Store a newly created policy
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:policies,code',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Policy::getCategories())),
            'version' => 'required|string|max:20',
            'document' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'status' => 'required|in:draft,active,archived',
            'requires_acknowledgment' => 'boolean',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date|after:effective_date',
        ]);

        try {
            // Upload document
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = Str::slug($request->code) . '_v' . $request->version . '_' . time() . '.pdf';
                $documentPath = $file->storeAs('policies', $filename, 'private');
            }

            // Create policy
            $policy = Policy::create([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'category' => $request->category,
                'version' => $request->version,
                'document_path' => $documentPath,
                'status' => $request->status,
                'requires_acknowledgment' => $request->boolean('requires_acknowledgment'),
                'effective_date' => $request->effective_date,
                'review_date' => $request->review_date,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create initial version record
            PolicyVersion::create([
                'policy_id' => $policy->id,
                'version' => $request->version,
                'changes' => 'Initial version',
                'document_path' => $documentPath,
                'created_by' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.policies.index')
                ->with('success', 'Policy created successfully!');

        } catch (\Exception $e) {
            // Delete uploaded file if policy creation fails
            if (isset($documentPath)) {
                Storage::disk('private')->delete($documentPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create policy: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified policy
     */
    public function show(Policy $policy)
    {
        $policy->load(['creator', 'updater', 'acknowledgments.employee', 'versions.creator']);

        // Get acknowledgment statistics
        $totalEmployees = Employee::where('employment_status', 'active')->count();
        $acknowledgedCount = $policy->acknowledgments()->count();
        $pendingCount = $totalEmployees - $acknowledgedCount;

        // Get employees who haven't acknowledged
        $acknowledgedEmployeeIds = $policy->acknowledgments()->pluck('employee_id');
        $pendingEmployees = Employee::where('employment_status', 'active')
            ->whereNotIn('id', $acknowledgedEmployeeIds)
            ->select('id', 'first_name', 'last_name', 'employee_number', 'email')
            ->get();

        return view('admin.policies.show', compact('policy', 'totalEmployees', 'acknowledgedCount', 'pendingCount', 'pendingEmployees'));
    }

    /**
     * Show the form for editing the policy
     */
    public function edit(Policy $policy)
    {
        $categories = Policy::getCategories();
        return view('admin.policies.edit', compact('policy', 'categories'));
    }

    /**
     * Update the specified policy
     */
    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:policies,code,' . $policy->id,
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Policy::getCategories())),
            'version' => 'required|string|max:20',
            'document' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:draft,active,archived',
            'requires_acknowledgment' => 'boolean',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date|after:effective_date',
            'version_changes' => 'nullable|string',
        ]);

        try {
            $documentPath = $policy->document_path;
            $versionChanged = $policy->version !== $request->version;

            // Upload new document if provided
            if ($request->hasFile('document')) {
                // Delete old document
                if ($policy->document_path) {
                    Storage::disk('private')->delete($policy->document_path);
                }

                $file = $request->file('document');
                $filename = Str::slug($request->code) . '_v' . $request->version . '_' . time() . '.pdf';
                $documentPath = $file->storeAs('policies', $filename, 'private');
            }

            // Update policy
            $policy->update([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'category' => $request->category,
                'version' => $request->version,
                'document_path' => $documentPath,
                'status' => $request->status,
                'requires_acknowledgment' => $request->boolean('requires_acknowledgment'),
                'effective_date' => $request->effective_date,
                'review_date' => $request->review_date,
                'updated_by' => Auth::id(),
            ]);

            // Create version record if version changed
            if ($versionChanged) {
                PolicyVersion::create([
                    'policy_id' => $policy->id,
                    'version' => $request->version,
                    'changes' => $request->version_changes ?? 'Version updated',
                    'document_path' => $documentPath,
                    'created_by' => Auth::id(),
                ]);

                // Clear acknowledgments for new version if required
                if ($policy->requires_acknowledgment) {
                    $policy->acknowledgments()->delete();
                }
            }

            return redirect()
                ->route('admin.policies.show', $policy)
                ->with('success', 'Policy updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update policy: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified policy
     */
    public function destroy(Policy $policy)
    {
        try {
            // Delete document
            if ($policy->document_path) {
                Storage::disk('private')->delete($policy->document_path);
            }

            // Delete version documents
            foreach ($policy->versions as $version) {
                if ($version->document_path) {
                    Storage::disk('private')->delete($version->document_path);
                }
            }

            $policy->delete();

            return redirect()
                ->route('admin.policies.index')
                ->with('success', 'Policy deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete policy: ' . $e->getMessage());
        }
    }

    /**
     * Download policy document
     */
    public function download(Policy $policy)
    {
        if (!$policy->document_path || !Storage::disk('private')->exists($policy->document_path)) {
            return back()->with('error', 'Policy document not found.');
        }

        return Storage::disk('private')->download(
            $policy->document_path,
            $policy->code . '_v' . $policy->version . '.pdf'
        );
    }
}