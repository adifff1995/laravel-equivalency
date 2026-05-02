<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquivalencyRequest;
use App\Models\EquivalencyRequest;
use App\Services\RequestService;
use Illuminate\Http\Request;

class PublicRequestController extends Controller
{
    public function __construct(protected RequestService $service) {}

    // ── Submission form ──────────────────────────────────────────────────────

    public function create()
    {
        $types = EquivalencyRequest::typeLabels();
        return view('requests.public-form', compact('types'));
    }

    public function store(StoreEquivalencyRequest $request)
    {
        $equivalencyRequest = $this->service->createFromPublicForm(
            $request->validated(),
            $request->allFiles()
        );

        // Flash the tracking code to show on confirmation page
        session()->flash('tracking_code', $equivalencyRequest->tracking_code);
        session()->flash('submitted_name', $equivalencyRequest->name);

        return redirect()->route('requests.public.submitted');
    }

    // ── Submission success / show tracking code ──────────────────────────────

    public function submitted()
    {
        if (!session()->has('tracking_code')) {
            return redirect()->route('requests.public.create');
        }

        return view('requests.submitted', [
            'trackingCode' => session('tracking_code'),
            'studentName'  => session('submitted_name'),
        ]);
    }

    // ── Track a request ──────────────────────────────────────────────────────

    public function trackForm()
    {
        return view('requests.track');
    }

    public function trackLookup(Request $request)
    {
        $request->validate([
            'tracking_code' => ['required', 'string', 'max:20'],
        ], [
            'tracking_code.required' => 'Please enter your tracking code.',
        ]);

        $code = strtoupper(trim($request->input('tracking_code')));

        $equivalencyRequest = EquivalencyRequest::byTrackingCode($code)->first();

        if (!$equivalencyRequest) {
            return back()
                ->withInput()
                ->withErrors(['tracking_code' => 'No request found with this tracking code. Please check and try again.']);
        }

        $equivalencyRequest->load('statusHistories');

        return view('requests.track-result', [
            'req' => $equivalencyRequest,
        ]);
    }
}
