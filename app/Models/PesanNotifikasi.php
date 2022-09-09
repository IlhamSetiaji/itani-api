<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanNotifikasi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pesan_notifikasi';
    protected $guarded = ['pesan_id'];
    protected $primaryKey = 'pesan_id';
    public $incrementing = false;
    public $timestamps = false;
}
