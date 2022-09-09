<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterHama extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_hama';
    protected $guarded = ['hama_id'];
    protected $primaryKey = 'hama_id';
    public $incrementing = false;
    public $timestamps = false;
}
