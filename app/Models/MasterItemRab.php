<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItemRab extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_item_rab';
    protected $primaryKey = 'item_rab_id';
    protected $guarded = ['item_rab_id'];
    public $incrementing = false;
    public $timestamps = false;

    public function master_item_fee()
    {
        return $this->hasMany(MasterItemFee::class);
    }

    public function pembiayaan_rab()
    {
        return $this->hasMany(PembiayaanRab::class);
    }
}
