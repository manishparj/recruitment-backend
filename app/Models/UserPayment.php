<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    use HasFactory;

    protected $table = 'user_payments';

    protected $fillable = [
        'user_id',
        'status',
        'amount',
        'reference_number',
        'remarks',
    ];


    public function users()
    {
        return $this->hasMany(User::class);
    }

}
