<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKomoditas extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_komoditas';
    protected $guarded = ['komoditas_id'];
    protected $primaryKey = 'komoditas_id';
    public $incrementing = false;
    public $timestamps = false;
}
