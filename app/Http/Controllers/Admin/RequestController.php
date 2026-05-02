<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StoreEquivalencyRequest;
use App\Http\Requests\UpdateEquivalencyRequest;
use App\Models\EquivalencyRequest;
use App\Services\RequestService;

class RequestController extends Controller
{
    public function __construct(protected RequestService $service)
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Access denied.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $search = request('search');
        $status = request('status');
        $type   = request('type');

        $requests = EquivalencyRequest::with('creator')
            ->search($search)
            ->filterStatus($status)
            ->filterType($type)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = EquivalencyRequest::statusLabels();
        $types    = EquivalencyRequest::typeLabels();

        return view('admin.requests.index', compact('requests', 'statuses', 'types', 'search', 'status', 'type'));
    }

    public function create()
    {
        $types = EquivalencyRequest::typeLabels();
        return view('admin.requests.create', compact('types'));
    }

    public function store(StoreEquivalencyRequest $request)
    {
        $this->service->createFromPublicForm(
            $request->validated(),
            $request->allFiles()
        );

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request created successfully.');
    }

    public function show(EquivalencyRequest $request)
    {
        $request->load(['creator', 'statusHistories.changedBy']);
        $statusBadge = EquivalencyRequest::statusBadgeClass();
        return view('admin.requests.show', compact('request', 'statusBadge'));
    }

    public function edit(EquivalencyRequest $request)
    {
        $types = EquivalencyRequest::typeLabels();
        return view('admin.requests.edit', compact('request', 'types'));
    }

    public function update(UpdateEquivalencyRequest $formRequest, EquivalencyRequest $request)
    {
        $this->service->update($request, $formRequest->validated(), $formRequest->allFiles());

        return redirect()->route('admin.requests.show', $request)
            ->with('success', 'Request updated successfully.');
    }

    public function changeStatus(ChangeStatusRequest $formRequest, EquivalencyRequest $request)
    {
        $allowedTransitions = [
            EquivalencyRequest::STATUS_NEW             => [EquivalencyRequest::STATUS_UNDER_REVIEW],
            EquivalencyRequest::STATUS_UNDER_REVIEW    => [EquivalencyRequest::STATUS_READY_FOR_ENTRY],
            EquivalencyRequest::STATUS_READY_FOR_ENTRY => [EquivalencyRequest::STATUS_ENTERED],
        ];

        $newStatus = $formRequest->input('status');

        if (!isset($allowedTransitions[$request->status]) ||
            !in_array($newStatus, $allowedTransitions[$request->status])) {
            return back()->with('error', 'Invalid status transition.');
        }

        $this->service->changeStatus($request, $newStatus, $formRequest->input('notes'));

        return back()->with('success', 'Status updated to: ' . EquivalencyRequest::statusLabels()[$newStatus]);
    }
}
