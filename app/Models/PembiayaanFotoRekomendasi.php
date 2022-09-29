<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanFotoRekomendasi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_foto_rekomendasi';
    protected $guarded = [];
    protected $primaryKey = 'pembiayaan_foto_rekomendasi_id';
    public $incrementing = false;
    public $timestamps = false;
}
