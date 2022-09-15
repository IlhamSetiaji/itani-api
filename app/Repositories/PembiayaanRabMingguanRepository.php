<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\PembiayaanRab;
use App\Models\TaskPengajuan;
use App\Interfaces\PembiayaanRabMingguanInterface;

class PembiayaanRabMingguanRepository implements PembiayaanRabMingguanInterface
{
    public function update($pembiayaanRabMingguan)
    {
        foreach ($pembiayaanRabMingguan as $key => $value) {
            $value->update([
                "kesiapan_kegiatan_st" => 'yes',
                "rencana_kegiatan_st" => "process",
                "kesiapan_kegiatan_date" => Carbon::now(),
                "rencana_kegiatan_st_date" => Carbon::now(),
                "catatan_lahan" => request('catatan_lahan'),
            ]);
        }
    }
}
