<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembiayaanRabMingguan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pembiayaan_rab_mingguan';
    protected $guarded = ['pembiayaan_rab_mingguan_id'];
    protected $primaryKey = 'pembiayaan_rab_mingguan_id';
    public $incrementing = false;
    public $timestamps = false;

    public function pembiayaan_rab()
    {
        return $this->belongsTo(PembiayaanRab::class, 'pembiayaan_rab_id');
    }
}
