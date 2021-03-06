<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'tb_member';
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
