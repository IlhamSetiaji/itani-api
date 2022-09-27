<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabDetailMingguan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'rab_detail_mingguan';
    protected $guarded = ['rab_detail_mingguan_id'];
    protected $primaryKey = 'rab_detail_mingguan_id';
    // public $incrementing = false;
    public $timestamps = false;
}
