<?php

namespace App\Http\Controllers;

use App\Models\QrisTemplate;
use App\Services\QrisService;
use Illuminate\Http\Request;

class QrisTemplateController extends Controller
{
    protected $qrisService;

    public function __construct(QrisService $qrisService)
    {
        $this->qrisService = $qrisService;
    }

    public function index()
    {
        $templates = QrisTemplate::all();
        return view('qris-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('qris-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet',
            'qris_string' => 'required|string',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        $template = QrisTemplate::create($validated);

        return redirect()->route('qris-templates.index')
            ->with('success', 'QRIS Template berhasil ditambahkan!');
    }

    public function edit(QrisTemplate $qrisTemplate)
    {
        return view('qris-templates.edit', ['template' => $qrisTemplate]);
    }

    public function update(Request $request, QrisTemplate $qrisTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet',
            'qris_string' => 'required|string',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        $qrisTemplate->update($validated);

        return redirect()->route('qris-templates.index')
            ->with('success', 'QRIS Template berhasil diupdate!');
    }

    public function destroy(QrisTemplate $qrisTemplate)
    {
        $qrisTemplate->delete();

        return redirect()->route('qris-templates.index')
            ->with('success', 'QRIS Template berhasil dihapus!');
    }

    public function test(Request $request, QrisTemplate $qrisTemplate)
    {
        if (empty($qrisTemplate->qris_string)) {
            return redirect()->route('qris-templates.index')
                ->with('error', 'QRIS string kosong! Silakan edit template dan tambahkan QRIS string yang valid.');
        }

        $amount = $request->input('amount', 10000);
        
        try {
            $dynamicQris = $this->qrisService->injectAmount(
                $qrisTemplate->qris_string,
                $amount
            );

            $qrSvg = $this->qrisService->generateQRCode($dynamicQris);

            return view('qris-templates.test', compact('qrisTemplate', 'amount', 'qrSvg', 'dynamicQris'));
        } catch (\Exception $e) {
            return redirect()->route('qris-templates.index')
                ->with('error', 'Error generate QRIS: ' . $e->getMessage());
        }
    }
}
