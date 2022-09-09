<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRekeningPetani extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'master_rekening_petani';
    protected $guarded = ['rekening_petani_id'];
    protected $primaryKey = 'rekening_petani_id';
    public $incrementing = false;
    public $timestamps = false;
}
