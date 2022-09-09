<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSubClusterSF extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_subcluster';
    protected $guarded = ['subcluster_id'];
    protected $primaryKey = 'subcluster_id';
    public $incrementing = false;
    public $timestamps = false;
}
