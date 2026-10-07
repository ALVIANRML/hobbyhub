<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model
{
    use HasUuids;

    protected $table = 'user_hobbies';

    protected $fillable = [
        'id_users',
        'id_hobbies'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id');

    }

    public function hobby()
    {
        return $this->belongsTo(Hobby::class, 'id_hobbies', 'id');
    }
}
