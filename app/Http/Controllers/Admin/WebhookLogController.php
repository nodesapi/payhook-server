<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class WebhookLogController extends Controller
{
    public function index(Request $request)
    {
        $sortColumns = [
            'created_at' => 'payment_logs.created_at',
            'source' => 'payment_logs.source',
            'notification_title' => 'payment_logs.notification_title',
            'amount' => 'payment_logs.amount',
            'tenant' => 'payment_logs.tenant_id',
            'status' => 'payment_logs.status',
        ];

        $sort = array_key_exists($request->query('sort', 'created_at'), $sortColumns)
            ? $request->query('sort', 'created_at')
            : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = in_array((int) $request->query('per_page', 25), [10, 25, 50, 100], true)
            ? (int) $request->query('per_page', 25)
            : 25;

        $query = PaymentLog::with('tenant', 'invoice');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('package_name', 'like', '%' . $request->search . '%')
                  ->orWhere('notification_title', 'like', '%' . $request->search . '%')
                  ->orWhere('source', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query
            ->orderBy($sortColumns[$sort], $direction)
            ->orderByDesc('payment_logs.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.webhook-logs.index', compact('logs', 'sort', 'direction', 'perPage'));
    }

    public function show(PaymentLog $log)
    {
        $log->load('tenant', 'invoice');
        return view('admin.webhook-logs.show', compact('log'));
    }
}
