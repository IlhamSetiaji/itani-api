<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterHargaGabah extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_harga_gabah';
    protected $guarded = ['master_harga_gabah_id'];
    protected $primaryKey = 'master_harga_gabah_id';
    public $incrementing = false;
    public $timestamps = false;
}
