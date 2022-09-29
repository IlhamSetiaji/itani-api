<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Rab;
use App\Models\AuthLogin;
use App\Models\RabDetail;
use App\Models\PembiayaanRab;
use App\Interfaces\TestInterface;
use App\Models\RabDetailMingguan;
use App\Interfaces\LoginInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LoginResource;

class TestRepository implements TestInterface
{
    public function insertBulkRab($payload)
    {
        $rabDetailIDs = array();
        $rab = Rab::create($payload);
        $rabDetails = RabDetail::where('rab_id', 1)->get()->toArray();
        foreach ($rabDetails as $rabDetail) {
            $rabDetailResult = RabDetail::create([
                'rab_id' => $rab->rab_id,
                'item_rab_id' => $rabDetail['item_rab_id'],
                'paket_rab_id' => $rabDetail['paket_rab_id'],
                'jumlah' => $rabDetail['jumlah'],
                'harga' => $rabDetail['harga'],
                'mdb' => $rabDetail['mdb'],
                'mdb_name' => $rabDetail['mdb_name'],
                'mdd' => Carbon::now(),
            ]);
            array_push($rabDetailIDs, $rabDetailResult->rab_detail_id);
        }
        $rabMingguans = RabDetailMingguan::whereBetween('rab_detail_id', [1, 607])->get()->toArray();
        $result = array();
        $j = 0;
        for ($i = 0; $i < sizeof($rabMingguans); $i++) {
            set_time_limit(100);
            if ($rabDetails[$j]['rab_detail_id'] == $rabMingguans[$i]['rab_detail_id']) {
                $rabMingguanResult = RabDetailMingguan::create([
                    'rab_detail_id' => $rabDetailIDs[$j],
                    'proses_tanam_id' => $rabMingguans[$i]['proses_tanam_id'],
                    'jumlah' => $rabMingguans[$i]['jumlah'],
                    'mdb' => $rabMingguans[$i]['mdb'],
                    'mdb_name' => $rabMingguans[$i]['mdb_name'],
                    'mdd' => Carbon::now(),
                ]);
                array_push($result, $rabMingguanResult);
                $j++;
            } else {
                $rabMingguanResult = RabDetailMingguan::create([
                    'rab_detail_id' => $rabDetailIDs[$j],
                    'proses_tanam_id' => $rabMingguans[$i]['proses_tanam_id'],
                    'jumlah' => $rabMingguans[$i]['jumlah'],
                    'mdb' => $rabMingguans[$i]['mdb'],
                    'mdb_name' => $rabMingguans[$i]['mdb_name'],
                    'mdd' => Carbon::now(),
                ]);
                array_push($result, $rabMingguanResult);
            }
        }
        return $result;
    }
}
