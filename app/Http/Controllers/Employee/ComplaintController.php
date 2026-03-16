<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;

        $complaints = Complaint::where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view('employee.complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('employee.complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string|max:5000',
            'category'     => 'required|in:harassment,discrimination,workplace_safety,misconduct,policy_violation,management,compensation,other',
            'priority'     => 'required|in:low,medium,high,urgent',
            'is_anonymous' => 'nullable|boolean',
            'attachment'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $employee = Auth::user()->employee;

        $data = [
            'employee_id'  => $employee->id,
            'subject'      => $validated['subject'],
            'description'  => $validated['description'],
            'category'     => $validated['category'],
            'priority'     => $validated['priority'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status'       => 'open',
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint = Complaint::create($data);

        return redirect()->route('employee.complaints.show', $complaint)
            ->with('success', 'Your complaint has been submitted successfully.');
    }

    public function show(Complaint $complaint)
    {
        $employee = Auth::user()->employee;

        // Employees can only view their own complaints
        if ($complaint->employee_id !== $employee->id) {
            abort(403);
        }

        $complaint->load(['responses.respondedBy']);

        return view('employee.complaints.show', compact('complaint'));
    }

    public function addResponse(Request $request, Complaint $complaint)
    {
        $employee = Auth::user()->employee;

        if ($complaint->employee_id !== $employee->id) {
            abort(403);
        }

        $request->validate([
            'response' => 'required|string|max:2000',
        ]);

        ComplaintResponse::create([
            'complaint_id' => $complaint->id,
            'responded_by' => $employee->id,
            'response'     => $request->response,
        ]);

        return back()->with('success', 'Your response has been added.');
    }

    // Employees cannot edit or delete complaints after submission
    public function edit(string $id) { abort(404); }
    public function update(Request $request, string $id) { abort(404); }
    public function destroy(string $id) { abort(404); }
}
