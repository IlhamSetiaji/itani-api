<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanLahan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_lahan';
    protected $guarded = ['pembiayaan_lahan_id'];
    protected $primaryKey = 'pembiayaan_lahan_id';
    public $incrementing = false;
    public $timestamps = false;
}
