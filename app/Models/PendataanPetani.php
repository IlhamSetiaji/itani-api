<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendataanPetani extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'pendataan_petani';
    protected $guarded = ['petani_id'];
    protected $primaryKey = 'petani_id';
    public $incrementing = false;
    public $timestamps = false;

    public function com_user_petani_sf()
    {
        return $this->setConnection('mysql_second')->belongsToMany(PendataanPetani::class,'com_user_petani','petani_id','user_id');
    }
}
