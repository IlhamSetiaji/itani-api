<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanenPengangkutan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'panen_pengangkutan';
    protected $guarded = [];
    protected $primaryKey = 'pengangkutan_id';
    public $incrementing = false;
    public $timestamps = false;

    public function panen_pengangkutan_hasil()
    {
        return $this->hasMany(PanenPengangkutanHasil::class);
    }
}
