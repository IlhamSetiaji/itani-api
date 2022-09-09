<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSidangKomite extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_sidangkomite';
    protected $guarded = ['sidangkomite_id'];
    protected $primaryKey = 'sidangkomite_id';
    public $incrementing = false;
    public $timestamps = false;

    public function com_user_sidangkomite()
    {
        return $this->belongsToMany(ComUser::class, 'com_user_sidangkomite', 'sidangkomite_id', 'user_id');
    }
}
