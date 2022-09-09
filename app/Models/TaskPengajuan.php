<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPengajuan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'task_pengajuan';
    protected $guarded = [];
    protected $primaryKey = 'pengajuan_id';
    public $incrementing = false;
    public $timestamps = false;
}
