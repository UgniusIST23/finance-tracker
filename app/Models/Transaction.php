<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'currency',
        'description',
        'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        // Naudojame withTrashed(), kad parodytų ir laikinai ištrintas kategorijas
        return $this->belongsTo(Category::class)->withTrashed();
    }
}
