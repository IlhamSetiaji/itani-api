<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenjangPendidikan extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_jenjang_pendidikan';
    protected $guarded = ['jenjang_id'];
    protected $primaryKey = 'jenjang_id';
    public $incrementing = false;
    public $timestamps = false;
}
