<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSubCluster extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_subcluster';
    protected $guarded = ['subcluster_id'];
    protected $primaryKey = 'subcluster_id';
    public $incrementing = false;
    public $timestamps = false;
}
