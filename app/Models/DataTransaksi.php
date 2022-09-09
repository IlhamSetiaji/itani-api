<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTransaksi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'data_transaksi';
    protected $guarded = ['data_transaksi_id'];
    protected $primaryKey = 'data_transaksi_id';
    public $incrementing = false;
    public $timestamps = false;
}
