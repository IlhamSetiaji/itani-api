<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPajak extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_pajak';
    protected $guarded = ['pajak_id'];
    protected $primaryKey = 'pajak_id';
    public $incrementing = false;
    public $timestamps = false;
}
