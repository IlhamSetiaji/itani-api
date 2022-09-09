<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendataanLahan extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'pendataan_lahan';
    protected $guarded = [];
    protected $primaryKey = 'lahan_id';
    public $incrementing = false;
    public $timestamps = false;
}
