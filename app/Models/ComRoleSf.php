<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComRoleSf extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'com_role';
    protected $guarded = ['role_id'];
    protected $primaryKey = 'role_id';
    public $incrementing = false;
    public $timestamps = false;

    public function com_role_user_sf()
    {
        return $this->setConnection('mysql_third')->belongsToMany(ComUser::class,'com_role_user','role_id','user_id')->withPivot('role_default','role_display');
    }
}
