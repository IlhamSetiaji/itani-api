<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanRab extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_rab';
    protected $fillable = [
        'pembiayaan_rab_id',
        'pembiayaan_id',
        'item_rab_id',
        'jumlah',
        'harga',
    ];
    protected $primaryKey = 'pembiayaan_rab_id';
    public $incrementing = false;
    public $timestamps = false;

    public function master_item_rab()
    {
        return $this->belongsTo(MasterItemRab::class, 'item_rab_id');
    }

    public function pembiayaan_rab_mingguan()
    {
        return $this->hasMany(PembiayaanRabMingguan::class);
    }
}
