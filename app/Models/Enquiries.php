<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiries extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'address', 'city', 'zip_code', 'phone_number', 'logo_path'
    ];
}
