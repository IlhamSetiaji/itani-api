<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahKecamatan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'wilayah_kecamatan';
    protected $guarded = ['kec_id'];
    protected $primaryKey = 'kec_id';
    public $incrementing = false;
    public $timestamps = false;
}
