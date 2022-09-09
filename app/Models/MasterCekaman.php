<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCekaman extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_cekaman';
    protected $guarded = ['cekaman_id'];
    protected $primaryKey = 'cekaman_id';
    public $incrementing = false;
    public $timestamps = false;
}
