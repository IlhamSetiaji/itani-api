<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanFotoKegiatanPetani extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_foto_kegiatan_petani';
    // protected $guarded = ['pembiayaan_foto_kegiatan_id'];
    protected $primaryKey = 'pembiayaan_foto_kegiatan_id';
    public $incrementing = false;
    public $timestamps = false;
}
