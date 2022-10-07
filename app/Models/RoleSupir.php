<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleSupir extends Model
{
    use HasFactory;

    public function scopeSupirGetAktifPetani($query)
    {
        $query = DB::connection('mysql')->select("SELECT a.petani_id, a.petani_kd , a.nama_lengkap ,
        a.pembiayaan_id FROM pembiayaan_petani a
        JOIN pembiayaan_rab b ON a.pembiayaan_id = b.pembiayaan_id
        JOIN pembiayaan_rab_mingguan c
        ON c.pembiayaan_rab_id = b.pembiayaan_rab_id
        WHERE c.proses_tanam_id = 18
        AND c.rencana_kegiatan_st = 'done' GROUP BY a.petani_id");
        return $query;
    }


    public function scopeSupirGetDataDashboard($query, $supirID)
    {
        $query = DB::connection('mysql')->select(
            "SELECT SUM(b.fee_distribusi) AS total_fee
                FROM panen_penimbangan a JOIN panen_penimbangan_hasil b
                ON a.penimbangan_id = b.penimbangan_id JOIN panen_pengangkutan c ON a.pengangkutan_id = c.pengangkutan_id
                WHERE MONTH(pengangkutan_tgl) = MONTH(NOW())
                AND YEAR(pengangkutan_tgl) = YEAR(NOW()) AND supir_id = :supir_id",
            [
                'supir_id' => $supirID,
            ]
        );
        return $query;
    }

    public function scopeSupirGetPengangkutanAktif($query, $supirID)
    {
        $query = DB::connection('mysql')->select(
            "SELECT pengangkutan_id FROM panen_pengangkutan WHERE pengangkutan_st =
            'process' AND supir_id = :supir_id",
            [
                'supir_id' => $supirID,
            ]

        );
        return $query;
    }


    public function scopeSupirGetDataAkun($query, $userID)
    {
        $query = DB::connection('mysql')->select(
            "SELECT a.user_id, b.supir_id , b.supir_nama, b.foto_path, b.foto_name
            FROM com_user_supir a JOIN master_supir b ON a.supir_id = b.supir_id WHERE
            user_id = :user_id",
            [
                'user_id' => $userID,
            ]
        );
        return $query;
    }


    public function scopeSupirGetPengangkutanTerkirim($query, $supirID)
    {
        $query = DB::connection('mysql')->select(
            "SELECT pengangkutan_id, pengangkutan_tgl, pengangkutan_st FROM panen_pengangkutan
            WHERE pengangkutan_st = 'done' AND supir_id = :supir_id ",
            [
                'supir_id' => $supirID,
            ]

        );
        return $query;
    }

    public function scopesupirGetDetailPengangkutanTerkirim($query, $pengangkutanID)
    {
        $query = DB::connection('mysql')->select(
            "SELECT * FROM panen_pengangkutan a
            JOIN master_supir b ON a.supir_id = b.supir_id WHERE pengangkutan_id = :pengangkutan_id",
            [
                'pengangkutan_id' => $pengangkutanID,
            ]
        );
        return $query;
    }

    public function scopeSupirGetDataTruk($query)
    {
        $query = DB::connection('mysql')->select(
            "SELECT * FROM master_supir"
        );
        return $query;
    }
}
