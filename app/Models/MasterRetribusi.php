<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRetribusi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_retribusi';
    protected $guarded = ['retribusi_id'];
    protected $primaryKey = 'retribusi_id';
    public $incrementing = false;
    public $timestamps = false;
}
