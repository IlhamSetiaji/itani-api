<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComMenu extends Model
{
    use HasFactory;
    protected $connection = 'mysql_third';
    protected $table = 'com_menu';
    protected $guarded = ['nav_id'];
    protected $primaryKey = 'nav_id';
    public $incrementing = false;
    public $timestamps = false;

    public function menu()
    {
        return $this->setConnection('mysql_third')->belongsToMany(ComMenu::class,'com_role_menu','nav_id','role_id')->withPivot('role_tp');
    }
}
