<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKios extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_kios';
    protected $guarded = ['kios_id'];
    protected $primaryKey = 'kios_id';
    public $incrementing = false;
    public $timestamps = false;

    public function wilayah_kelurahan()
    {
        return $this->belongsTo(WilayahKelurahan::class, 'kel_id', 'kios_id');
    }
}
