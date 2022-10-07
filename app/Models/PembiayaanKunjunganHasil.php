<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanKunjunganHasil extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_kunjungan_hasil';
    protected $guarded = [];
    protected $primaryKey = 'hasil_id';
    public $incrementing = false;
    public $timestamps = false;
}
