<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\PembiayaanRab;
use App\Models\TaskPengajuan;
use App\Interfaces\PembiayaanRabInterface;

class PembiayaanRabRepository implements PembiayaanRabInterface
{
    public function update($payload, $pembiayaanID, $itemRabID)
    {
        $user = request()->user();
        $pembiayaan = PembiayaanRab::where('pembiayaan_id', $pembiayaanID)->where('item_rab_id', $itemRabID)->first();
        $pembiayaan->update([
            'harga' => request('harga'),
            'mdb' => $user->user_id,
            'mdb_name' => $user->user_name,
            'mdd' => Carbon::now(),
        ]);
        return $pembiayaan;
    }
}
