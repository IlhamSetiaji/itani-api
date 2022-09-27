<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabDetail extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'rab_detail';
    protected $guarded = ['rab_detail_id'];
    protected $primaryKey = 'rab_detail_id';
    // public $incrementing = false;
    public $timestamps = false;
}
