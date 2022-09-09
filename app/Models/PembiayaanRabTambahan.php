<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanRabTambahan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_rab_tambahan';
    protected $guarded = [];
    protected $primaryKey = 'pembiayaan_rab_tambahan_id';
    public $incrementing = false;
    public $timestamps = false;
}
