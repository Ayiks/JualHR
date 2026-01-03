<?php

// app/Http/Controllers/Admin/QueryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\QueryResponse;

class QueryController extends Controller
{
    /**
     * Display a listing of queries
     */
    public function index(Request $request)
    {
        $query = Query::with(['employee', 'issuer', 'responses']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $queries = $query->latest('issued_date')->paginate(15);

        // Get all employees for filter
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => Query::count(),
            'open' => Query::open()->count(),
            'responded' => Query::responded()->count(),
            'closed' => Query::closed()->count(),
            'overdue' => Query::open()
                ->whereNotNull('response_deadline')
                ->whereDate('response_deadline', '<', today())
                ->count(),
        ];

        return view('admin.queries.index', compact('queries', 'employees', 'stats'));
    }

    /**
     * Show the form for creating a new query
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_number', 'department_id')
            ->with('department')
            ->orderBy('first_name')
            ->get();

        $types = Query::getTypes();
        $severities = Query::getSeverities();

        return view('admin.queries.create', compact('employees', 'types', 'severities'));
    }

    /**
     * Store a newly created query
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:' . implode(',', array_keys(Query::getTypes())),
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'action_required' => 'nullable|string',
            'severity' => 'required|in:' . implode(',', array_keys(Query::getSeverities())),
            'issued_date' => 'required|date',
            'response_deadline' => 'nullable|date|after:issued_date',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            // Upload document if provided
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = 'query_' . time() . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('queries', $filename);
            }

            // Generate reference number
            $referenceNumber = Query::generateReferenceNumber();

            // Create query
            $query = Query::create([
                'reference_number' => $referenceNumber,
                'employee_id' => $request->employee_id,
                'issued_by' => Auth::id(),
                'type' => $request->type,
                'subject' => $request->subject,
                'description' => $request->description,
                'action_required' => $request->action_required,
                'severity' => $request->severity,
                'status' => 'open',
                'issued_date' => $request->issued_date,
                'response_deadline' => $request->response_deadline,
                'document_path' => $documentPath,
            ]);

            // TODO: Send notification to employee

            return redirect()
                ->route('admin.queries.show', $query)
                ->with('success', 'Query issued successfully!');

        } catch (\Exception $e) {
            // Delete uploaded file if query creation fails
            if (isset($documentPath)) {
                Storage::delete($documentPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to issue query: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified query
     */
    public function show(Query $query)
    {
        $query->load(['employee.department', 'issuer', 'closer', 'responses.employee']);

        return view('admin.queries.show', compact('query'));
    }

    /**
     * Show the form for editing the query
     */
    public function edit(Query $query)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        $types = Query::getTypes();
        $severities = Query::getSeverities();

        return view('admin.queries.edit', compact('query', 'employees', 'types', 'severities'));
    }

    /**
     * Update the specified query
     */
    public function update(Request $request, Query $query)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:' . implode(',', array_keys(Query::getTypes())),
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'action_required' => 'nullable|string',
            'severity' => 'required|in:' . implode(',', array_keys(Query::getSeverities())),
            'issued_date' => 'required|date',
            'response_deadline' => 'nullable|date|after:issued_date',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            $documentPath = $query->document_path;

            // Upload new document if provided
            if ($request->hasFile('document')) {
                // Delete old document
                if ($query->document_path) {
                    Storage::delete($query->document_path);
                }

                $file = $request->file('document');
                $filename = 'query_' . time() . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('queries', $filename);
            }

            // Update query
            $query->update([
                'employee_id' => $request->employee_id,
                'type' => $request->type,
                'subject' => $request->subject,
                'description' => $request->description,
                'action_required' => $request->action_required,
                'severity' => $request->severity,
                'issued_date' => $request->issued_date,
                'response_deadline' => $request->response_deadline,
                'document_path' => $documentPath,
            ]);

            return redirect()
                ->route('admin.queries.show', $query)
                ->with('success', 'Query updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update query: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified query
     */
    public function destroy(Query $query)
    {
        try {
            // Delete document
            if ($query->document_path) {
                Storage::delete($query->document_path);
            }

            // Delete response documents
            foreach ($query->responses as $response) {
                if ($response->document_path) {
                    Storage::delete($response->document_path);
                }
            }

            $query->delete();

            return redirect()
                ->route('admin.queries.index')
                ->with('success', 'Query deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete query: ' . $e->getMessage());
        }
    }

    /**
     * Close the query
     */
    public function close(Request $request, Query $query)
    {
        $request->validate([
            'closure_notes' => 'required|string',
        ]);

        try {
            $query->close($request->closure_notes, Auth::id());

            return redirect()
                ->route('admin.queries.show', $query)
                ->with('success', 'Query closed successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to close query: ' . $e->getMessage());
        }
    }

    /**
     * Download query document
     */
    public function download(Query $query)
    {
        if (!$query->document_path || !Storage::exists($query->document_path)) {
            return back()->with('error', 'Document not found.');
        }

        return Storage::download($query->document_path);
    }

    /**
 * Download response document
 */
public function downloadResponse(QueryResponse $response)
{
    if (!$response->document_path || !Storage::exists($response->document_path)) {
        return back()->with('error', 'Document not found.');
    }

    return Storage::download($response->document_path);
}
}