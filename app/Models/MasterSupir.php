<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSupir extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_supir';
    protected $guarded = ['supir_id'];
    protected $primaryKey = 'supir_id';
    public $incrementing = false;
    public $timestamps = false;

    public function com_user_supir()
    {
        return $this->belongsToMany(ComUser::class, 'com_user_supir', 'supir_id', 'user_id');
    }
}
