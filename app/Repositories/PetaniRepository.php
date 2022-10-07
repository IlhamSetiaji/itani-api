<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Interfaces\PetaniInterface;

class PetaniRepository implements PetaniInterface
{
    public function pengambilanSaprodiAll($data, $dataPetani)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $temp_data = array(
                'pembiayaan_id' => $value->pembiayaan_id,
                'proses_tanam_desc' => $value->proses_tanam_desc,
                'proses_tanam_nama' => $value->proses_tanam_nama,
                'periode_kegiatan_start' => $value->periode_kegiatan_start,
                'periode_kegiatan_end' => $value->periode_kegiatan_end,
                'lahan_id' => $value->lahan_id,
                'kesiapan_kegiatan_st' => $value->kesiapan_kegiatan_st,
                'kios_nama' => $value->kios_nama,
                'alamat' => $value->alamat,
                'item' => array(),
            );
            array_push($result, $temp_data);
            foreach ($dataPetani as $k => $d) {
                if ($value->proses_tanam_nama == $d->proses_tanam_nama) {
                    $arr_item = array(
                        'nama_item' => $d->nama_item,
                        'jumlah' => $d->jumlah,
                        'pengambilan_st' => $d->pengambilan_st,
                        'kesiapan_stok_st' => $d->kesiapan_stok_st,
                    );
                    array_push($result[$key]['item'], $arr_item);
                }
            }
        }
        return $result;
    }

    public function pengambilanSaprodiGrup2($data, $dataPetani)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $temp_data = array(
                'pembiayaan_id' => $value->pembiayaan_id,
                'proses_tanam_desc' => $value->proses_tanam_desc,
                'proses_tanam_nama' => $value->proses_tanam_nama,
                'periode_kegiatan_start' => $value->periode_kegiatan_start,
                'periode_kegiatan_end' => $value->periode_kegiatan_end,
                'lahan_id' => $value->lahan_id,
                'kesiapan_kegiatan_st' => $value->kesiapan_kegiatan_st,
                'kios_nama' => $value->kios_nama,
                'alamat' => $value->alamat,
                'item' => array(),
            );
            array_push($result, $temp_data);
            foreach ($dataPetani as $k => $d) {
                if ($value->proses_tanam_nama == $d->proses_tanam_nama) {
                    $arr_item = array(
                        'pembiayaan_rab_mingguan_id' => $d->pembiayaan_rab_mingguan_id,
                        'nama_item' => $d->nama_item,
                        'jumlah' => $d->jumlah,
                        'pengambilan_st' => $d->pengambilan_st,
                        'kesiapan_stok_st' => $d->kesiapan_stok_st,
                    );
                    array_push($result[$key]['item'], $arr_item);
                }
            }
        }
        return $result;
    }

    public function pengambilanSaprodiGrup3($data, $dataPetani)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $temp_data = array(
                'pembiayaan_id' => $value->pembiayaan_id,
                'proses_tanam_desc' => $value->proses_tanam_desc,
                'proses_tanam_nama' => $value->proses_tanam_nama,
                'periode_kegiatan_start' => $value->periode_kegiatan_start,
                'periode_kegiatan_end' => $value->periode_kegiatan_end,
                'lahan_id' => $value->lahan_id,
                'kesiapan_kegiatan_st' => $value->kesiapan_kegiatan_st,
                'kios_nama' => $value->kios_nama,
                'alamat' => $value->alamat,
                'item' => array(),
            );
            array_push($result, $temp_data);
            foreach ($dataPetani as $k => $d) {
                if ($value->proses_tanam_nama == $d->proses_tanam_nama) {
                    $arr_item = array(
                        'pembiayaan_rab_mingguan_id' => $d->pembiayaan_rab_mingguan_id,
                        'nama_item' => $d->nama_item,
                        'jumlah' => $d->jumlah,
                        'pengambilan_st' => $d->pengambilan_st,
                        'kesiapan_stok_st' => $d->kesiapan_stok_st,
                    );
                    array_push($result[$key]['item'], $arr_item);
                }
            }
        }
        return $result;
    }
}
