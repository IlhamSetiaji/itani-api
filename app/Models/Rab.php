<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'rab';
    protected $guarded = ['rab_id'];
    protected $primaryKey = 'rab_id';
    // public $incrementing = false;
    public $timestamps = false;
}
