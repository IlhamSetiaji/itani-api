<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembiayaan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan';
    protected $guarded = ['pembiayaan_id'];
    protected $primaryKey = 'pembiayaan_id';
    public $incrementing = false;
    public $timestamps = false;
}
