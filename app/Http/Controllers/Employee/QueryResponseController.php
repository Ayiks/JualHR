<?php

// app/Http/Controllers/Employee/QueryResponseController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Models\QueryResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QueryResponseController extends Controller
{
    /**
     * Display a listing of employee's queries
     */
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee record not found.');
        }

        $query = Query::where('employee_id', $employee->id)
            ->with(['issuer', 'responses']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $queries = $query->latest('issued_date')->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => Query::where('employee_id', $employee->id)->count(),
            'open' => Query::where('employee_id', $employee->id)->open()->count(),
            'responded' => Query::where('employee_id', $employee->id)->responded()->count(),
            'overdue' => Query::where('employee_id', $employee->id)
                ->open()
                ->whereNotNull('response_deadline')
                ->whereDate('response_deadline', '<', today())
                ->count(),
        ];

        return view('employee.queries.index', compact('queries', 'stats'));
    }

    /**
     * Display the specified query
     */
    public function show(Query $query)
    {
        $employee = Auth::user()->employee;

        // Ensure employee can only view their own queries
        if ($query->employee_id !== $employee->id) {
            abort(403);
        }

        $query->load(['issuer', 'closer', 'responses.employee']);

        return view('employee.queries.show', compact('query'));
    }

    /**
     * Submit response to query
     */
    public function respond(Request $request, Query $query)
    {
        $employee = Auth::user()->employee;

        // Ensure employee can only respond to their own queries
        if ($query->employee_id !== $employee->id) {
            abort(403);
        }

        // Check if query can be responded to
        if (!$query->canRespond()) {
            return back()->with('error', 'This query cannot be responded to at this time.');
        }

        $request->validate([
            'response' => 'required|string|min:10',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        try {
            // Upload document if provided
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = 'response_' . time() . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('query_responses', $filename);
            }

            // Create response
            QueryResponse::create([
                'query_id' => $query->id,
                'employee_id' => $employee->id,
                'response' => $request->response,
                'document_path' => $documentPath,
            ]);

            // Mark query as responded
            $query->markAsResponded();

            // TODO: Send notification to HR

            return redirect()
                ->route('employee.queries.show', $query)
                ->with('success', 'Response submitted successfully!');

        } catch (\Exception $e) {
            // Delete uploaded file if response creation fails
            if (isset($documentPath)) {
                Storage::delete($documentPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to submit response: ' . $e->getMessage());
        }
    }

    /**
     * Download query document
     */
    public function downloadQuery(Query $query)
    {
        $employee = Auth::user()->employee;

        // Ensure employee can only download their own query documents
        if ($query->employee_id !== $employee->id) {
            abort(403);
        }

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
        $employee = Auth::user()->employee;

        // Ensure employee can only download their own response documents
        if ($response->employee_id !== $employee->id) {
            abort(403);
        }

        if (!$response->document_path || !Storage::exists($response->document_path)) {
            return back()->with('error', 'Document not found.');
        }

        return Storage::download($response->document_path);
    }
}