<?php

namespace Modules\Manager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Manager\Database\Factories\ManagerFactory;

class Manager extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['admin_id', 'username'];
}
