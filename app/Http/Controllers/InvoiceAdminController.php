<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceAdminController extends Controller
{
    // List latest invoices (with customer)
    public function index()
    {
        return Invoice::with('customer:id,name,email')
            ->orderByDesc('date')
            ->limit(50)
            ->get();
    }

    // Create invoice with items
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'   => ['required', 'exists:users,id'],
            'date'      => ['required', 'date'],
            'due_date'  => ['nullable', 'date'],
            'tax_rate'  => ['nullable', 'numeric', 'min:0'], // percent
            'notes'     => ['nullable', 'string'],
            'items'     => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
        ]);

        // Wrap in transaction so invoice + items stay in sync
        $invoice = DB::transaction(function () use ($data, $request) {
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxRate = $data['tax_rate'] ?? 0;
            $tax     = round($subtotal * ($taxRate / 100), 2);
            $total   = $subtotal + $tax;

            $invoice = Invoice::create([
                'user_id'    => $data['user_id'],
                'created_by' => $request->user()->id,
                'number'     => 'INV-' . Str::upper(Str::random(8)),
                'date'       => $data['date'],
                'due_date'   => $data['due_date'] ?? null,
                'subtotal'   => $subtotal,
                'tax'        => $tax,
                'total'      => $total,
                'status'     => 'unpaid',
                'notes'      => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $invoice->load('customer', 'items');
        });

        return response()->json($invoice, 201);
    }
}
