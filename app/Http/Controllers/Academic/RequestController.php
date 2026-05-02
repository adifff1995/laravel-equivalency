<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeStatusRequest;
use App\Models\EquivalencyRequest;
use App\Services\RequestService;

class RequestController extends Controller
{
    public function __construct(protected RequestService $service)
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAcademic()) {
                abort(403, 'Access denied.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $search = request('search');

        $requests = EquivalencyRequest::with('creator')
            ->where('status', EquivalencyRequest::STATUS_ENTERED)
            ->search($search)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('academic.requests.index', compact('requests', 'search'));
    }

    public function show(EquivalencyRequest $request)
    {
        if ($request->status !== EquivalencyRequest::STATUS_ENTERED &&
            !in_array($request->status, [EquivalencyRequest::STATUS_APPROVED, EquivalencyRequest::STATUS_REJECTED])) {
            abort(403, 'This request is not yet available for review.');
        }

        $request->load(['creator', 'statusHistories.changedBy']);
        return view('academic.requests.show', compact('request'));
    }

    public function decide(ChangeStatusRequest $formRequest, EquivalencyRequest $request)
    {
        $newStatus = $formRequest->input('status');

        if (!in_array($newStatus, [EquivalencyRequest::STATUS_APPROVED, EquivalencyRequest::STATUS_REJECTED])) {
            return back()->with('error', 'Academic staff can only approve or reject requests.');
        }

        if ($request->status !== EquivalencyRequest::STATUS_ENTERED) {
            return back()->with('error', 'Only "Entered" requests can be decided.');
        }

        $this->service->changeStatus($request, $newStatus, $formRequest->input('notes'));

        return redirect()->route('academic.requests.index')
            ->with('success', 'Request has been ' . $newStatus . '.');
    }
}
