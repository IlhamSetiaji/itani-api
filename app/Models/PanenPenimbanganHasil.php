<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanenPenimbanganHasil extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'panen_penimbangan_hasil';
    protected $guarded = ['penimbangan_hasil_id'];
    protected $primaryKey = 'penimbangan_hasil_id';
    public $incrementing = false;
    public $timestamps = false;
}
