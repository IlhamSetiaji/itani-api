<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ComUser extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $connection = 'mysql_third';
    protected $table = 'com_user';
    protected $guarded = [];
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    public function roles()
    {
        return $this->setConnection('mysql_third')->belongsToMany(ComRole::class, 'com_role_user', 'user_id', 'role_id')->withPivot('role_default', 'role_display');
    }

    public function com_user_pendamping()
    {
        return $this->setConnection('mysql_second')->belongsToMany(Pendamping::class, 'com_user_pendamping', 'user_id', 'pendamping_id');
    }

    public function com_role_user_sf()
    {
        return $this->setConnection('mysql_second')->belongsToMany(ComRoleSf::class, 'com_role_user', 'user_id', 'role_id')->withPivot('role_default', 'role_display');
    }

    public function com_user_petani_sf()
    {
        return $this->setConnection('mysql_second')->belongsToMany(PendataanPetani::class, 'com_user_petani', 'user_id', 'petani_id');
    }

    public function com_user_sidangkomite()
    {
        return $this->belongsToMany(MasterSidangKomite::class, 'com_user_sidangkomite', 'user_id', 'sidangkomite_id');
    }

    public function com_user_supir()
    {
        return $this->belongsToMany(MasterSupir::class, 'com_user_supir', 'user_id', 'supir_id');
    }
}
