<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Pembiayaan;
use App\Models\PembiayaanFotoRekomendasi;
use App\Models\PembiayaanKunjungan;
use App\Models\PembiayaanRabMingguan;
use Illuminate\Http\Request;

class PembiayaanController extends Controller
{
    public function getLahanBysubclusterBysubcluster()
    {
        // $pembiayaan = Pembiayaan::all();
        // return ResponseFormatter::success($pembiayaan,'Data pembiayaan berhasil didapatkan');
        $user = request()->user();
        return $user;
    }

    public function getRencanaKegiatan()
    {
        $rencana = PembiayaanRabMingguan::all();
        return ResponseFormatter::success($rencana, 'Data rencana berhasil didapatkan');
    }
}
