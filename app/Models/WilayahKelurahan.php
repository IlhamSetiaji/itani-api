<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahKelurahan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'wilayah_kelurahan';
    protected $guarded = ['kel_id'];
    protected $primaryKey = 'kel_id';
    public $incrementing = false;
    public $timestamps = false;

    public function master_kios()
    {
        return $this->hasMany(MasterKios::class, 'kios_id', 'kel_id');
    }
}
