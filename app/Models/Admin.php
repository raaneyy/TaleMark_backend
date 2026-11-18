<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

    protected $fillable = ['username', 'name', 'nama', 'email', 'password', 'foto'];
    protected $hidden = ['password'];

    public $timestamps = false;
}