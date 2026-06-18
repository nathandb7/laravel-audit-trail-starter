<?php

namespace NathanDeBarros\AuditTrail\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NathanDeBarros\AuditTrail\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->latest('created_at')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('event', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->input('user')))
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%' . $request->input('action') . '%'))
            ->when($request->filled('module'), fn ($query) => $query->where('module', $request->input('module')))
            ->when($request->filled('event'), fn ($query) => $query->where('event', $request->input('event')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => AuditLog::query()->count(),
            'today' => AuditLog::query()->whereDate('created_at', now()->toDateString())->count(),
            'critical' => AuditLog::query()->whereIn('event', ['deleted', 'failed_login', 'critical'])->count(),
            'users' => AuditLog::query()->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
        ];

        $modules = AuditLog::query()->whereNotNull('module')->distinct()->orderBy('module')->pluck('module');
        $events = AuditLog::query()->whereNotNull('event')->distinct()->orderBy('event')->pluck('event');

        return view('audit-trail::audit-trail.index', compact('logs', 'stats', 'modules', 'events'));
    }

    public function show(AuditLog $auditLog): View
    {
        return view('audit-trail::audit-trail.show', compact('auditLog'));
    }
}
