<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanenPengangkutanHasil extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'panen_pengangkutan_hasil';
    protected $guarded = [];
    protected $primaryKey = 'pengangkutan_hasil_id';
    public $incrementing = false;
    public $timestamps = false;

    public function panen_pengangkutan()
    {
        return $this->belongsTo(PanenPengangkutan::class, 'pengangkutan_id');
    }
}
