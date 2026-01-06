<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    use HasFactory;

    protected $table = 'admit_card';

    protected $fillable = [
        'user_id',
        'status',
        'roll_number',
        'exam_venue',
    ];


    public function users()
    {
        return $this->hasOne(User::class);
    }

}
