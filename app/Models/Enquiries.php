<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiries extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'gender',
        'dob',
        'phone_number',
        'alternate_phone_number',
        'email',
        'address',
        'qualification',
        'course',
        'optional_subject',
        'attempts_given',
        'referral_source',
        'counseling_satisfaction',
        'contact_preference',
        'counsellor_id',
        'status',
        'rescheduled_date',
        'remarks',
        'dp_path',
    ];
    protected $dates = ['deleted_at'];

    public function counsellor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counsellor_id');
    }
}
