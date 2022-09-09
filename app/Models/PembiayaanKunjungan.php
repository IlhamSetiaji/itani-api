<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanKunjungan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_kunjungan';
    protected $guarded = [];
    protected $primaryKey = 'pembiayaan_kunjungan_id';
    public $incrementing = false;
    public $timestamps = false;
}
