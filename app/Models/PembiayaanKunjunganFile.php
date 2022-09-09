<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanKunjunganFile extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_kunjungan_file';
    protected $guarded = ['file_id'];
    protected $primaryKey = 'file_id';
    public $incrementing = false;
    public $timestamps = false;
}
