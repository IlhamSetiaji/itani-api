<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanPetani extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_petani';
    protected $guarded = ['pembiayaan_petani_id'];
    protected $primaryKey = 'pembiayaan_petani_id';
    public $incrementing = false;
    public $timestamps = false;
}
