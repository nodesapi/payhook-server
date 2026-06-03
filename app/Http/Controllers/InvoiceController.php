<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\QrisService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $qrisTemplates = \App\Models\QrisTemplate::active()->get();
        return view('invoices.create', compact('qrisTemplates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:1000',
            'qris_template_id' => 'nullable|exists:qris_templates,id',
        ]);

        $invoice = Invoice::create($validated);

        // Auto-generate QRIS if template available
        $invoice->generateQris($request->qris_template_id);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat!');
    }

    public function show(Invoice $invoice)
    {
        $qrSvg = null;
        
        if ($invoice->qris_string) {
            $qrisService = app(QrisService::class);
            $qrSvg = $qrisService->generateQRCode($invoice->qris_string);
        }

        return view('invoices.show', compact('invoice', 'qrSvg'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function print(Invoice $invoice)
    {
        return view('invoices.print', compact('invoice'));
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return back()->with('error', 'Hanya invoice pending yang bisa dibatalkan.');
        }

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice berhasil dibatalkan.');
    }
}
