<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    protected $table = 'hobbies';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function userHobbies() {
        return $this->hasMany(UserHobby::class, 'id_users');
    }
}
