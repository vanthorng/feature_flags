<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'number',
        'date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'      => 'date',
        'due_date'  => 'date',
        'subtotal'  => 'decimal:2',
        'tax'       => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
