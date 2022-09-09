<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPengajuanProcess extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'task_pengajuan_process';
    protected $guarded = [];
    protected $primaryKey = 'process_id';
    public $incrementing = false;
    public $timestamps = false;
}
