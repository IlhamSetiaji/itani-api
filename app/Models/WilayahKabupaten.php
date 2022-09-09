<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahKabupaten extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'wilayah_kabupaten';
    protected $guarded = ['kab_id'];
    protected $primaryKey = 'kab_id';
    public $incrementing = false;
    public $timestamps = false;
}
