<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $table = 'pendamping';
    protected $guarded = ['pendamping_id'];
    protected $primaryKey = 'pendamping_id';
    public $incrementing = false;
    public $timestamps = false;

    public function com_user_pendamping()
    {
        return $this->setConnection('mysql_second')->belongsToMany(Pendamping::class,'com_user_pendamping','pendamping_id','user_id');
    }
}
