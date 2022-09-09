<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItemFee extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'master_item_fee';
    protected $guarded = ['item_fee_id'];
    protected $primaryKey = 'item_fee_id';
    public $incrementing = false;
    public $timestamps = false;

    public function master_item_rab()
    {
        return $this->belongsTo(MasterItemRab::class,'item_rab_id');
    }
}
