<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProvinsi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'wilayah_provinsi';
    protected $guarded = ['prov_id'];
    protected $primaryKey = 'prov_id';
    public $incrementing = false;
    public $timestamps = false;
}
