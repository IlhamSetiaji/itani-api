<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;
    protected $connection = 'mysql_third';
    protected $table = 'pegawai';
    protected $guarded = [];
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;
}
