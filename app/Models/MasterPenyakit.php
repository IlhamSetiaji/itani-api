<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPenyakit extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_penyakit';
    protected $guarded = ['penyakit_id'];
    protected $primaryKey = 'penyakit_id';
    public $incrementing = false;
    public $timestamps = false; 
}
