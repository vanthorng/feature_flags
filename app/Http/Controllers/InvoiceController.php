<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // 🔥 Return ALL invoices for ALL users (any logged-in user can see them)
    public function index(Request $request)
    {
        return Invoice::with(['items', 'customer:id,name,email'])
            ->orderByDesc('date')
            ->get();
    }

    // If you still use show(), you can either keep or relax the check
    public function show(Request $request, Invoice $invoice)
    {
        // If you also want everyone to see details, remove this line:
        // abort_unless($invoice->user_id === $request->user()->id, 403);

        return $invoice->load('items', 'customer:id,name,email');
    }
}
