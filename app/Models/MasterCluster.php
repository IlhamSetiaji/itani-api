<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCluster extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_cluster';
    protected $guarded = ['cluster_id'];
    protected $primaryKey = 'cluster_id';
    public $incrementing = false;
    public $timestamps = false;
}
