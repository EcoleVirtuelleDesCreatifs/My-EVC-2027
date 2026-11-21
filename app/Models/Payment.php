<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $guarded = ['id'];

    public const METHODS = ['Orange Money', 'MTN MoMo', 'Moov Money', 'Wave', 'Espèces', 'Virement bancaire'];

    protected function casts(): array
    {
        return ['paid_on' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function nextReceiptNumber(): string
    {
        $prefix = 'REC-'.now()->year.'-';
        $last = static::where('receipt_number', 'like', $prefix.'%')->orderByDesc('receipt_number')->value('receipt_number');

        return $prefix.str_pad((string) ((int) substr((string) $last, -4) + 1), 4, '0', STR_PAD_LEFT);
    }
}
