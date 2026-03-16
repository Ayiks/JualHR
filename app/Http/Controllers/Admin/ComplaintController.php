<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['employee', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $complaints = $query->latest()->paginate(15);

        $stats = [
            'total'       => Complaint::count(),
            'open'        => Complaint::where('status', 'open')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved'    => Complaint::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'stats'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['employee', 'assignedTo', 'responses.respondedBy']);

        // Get employees with HR/admin roles for assignment dropdown
        $handlers = Employee::whereHas('user', function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->whereIn('name', ['super_admin', 'hr_admin']);
            });
        })->get();

        return view('admin.complaints.show', compact('complaint', 'handlers'));
    }

    public function assign(Request $request, Complaint $complaint)
    {
        $request->validate([
            'assigned_to' => 'required|exists:employees,id',
        ]);

        $complaint->update([
            'assigned_to' => $request->assigned_to,
            'status'      => $complaint->status === 'open' ? 'in_progress' : $complaint->status,
        ]);

        return back()->with('success', 'Complaint assigned successfully.');
    }

    public function resolve(Complaint $complaint)
    {
        if ($complaint->is_resolved) {
            return back()->with('error', 'Complaint is already resolved.');
        }

        $complaint->resolve();

        return back()->with('success', 'Complaint marked as resolved.');
    }

    public function close(Complaint $complaint)
    {
        $complaint->close();

        return back()->with('success', 'Complaint closed.');
    }

    public function respond(Request $request, Complaint $complaint)
    {
        $request->validate([
            'response'   => 'required|string|max:2000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $data = [
            'complaint_id' => $complaint->id,
            'responded_by' => Auth::user()->employee->id,
            'response'     => $request->response,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('complaint-responses', 'public');
        }

        ComplaintResponse::create($data);

        return back()->with('success', 'Response added successfully.');
    }

    // Admin does not create complaints — employees do
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function edit(Complaint $complaint) { abort(404); }
    public function update(Request $request, Complaint $complaint) { abort(404); }
    public function destroy(Complaint $complaint) { abort(404); }
}
