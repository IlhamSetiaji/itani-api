<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComRole extends Model
{
    use HasFactory;
    protected $connection = 'mysql_third';
    protected $table = 'com_role';
    protected $guarded = ['role_id'];
    protected $primaryKey = 'role_id';
    public $incrementing = false;
    public $timestamps = false;

    public function users()
    {
        return $this->setConnection('mysql_third')->belongsToMany(ComUser::class,'com_role_user','role_id','user_id')->withPivot('role_default','role_display');
    }

    public function menu()
    {
        return $this->setConnection('mysql_third')->belongsToMany(ComMenu::class,'com_role_menu','role_id','nav_id')->withPivot('role_tp');
    }
}
