<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\ComUser;
use App\Models\Agronomis;
use App\Models\MasterHama;
use App\Models\Pembiayaan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use App\Models\MasterCekaman;
use App\Models\MasterItemRab;
use App\Models\PembiayaanRab;
use App\Models\MasterPenyakit;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\PembiayaanKunjungan;
use App\Models\PembiayaanRabTambahan;
use App\Models\PembiayaanKunjunganFile;
use App\Models\PembiayaanFotoRekomendasi;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PembiayaanRabRepository;
use App\Http\Requests\UpdateDanaCadanganRequest;
use App\Http\Requests\PembiayaanKunjunganFileRequest;
use App\Http\Requests\PembiayaanKunjunganHasilRequest;
use App\Models\PembiayaanKunjunganHasil;

class AgronomisController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('agronomis');
    // }
    private PembiayaanRabRepository $pembiayaanRabRepository;

    public function __construct(PembiayaanRabRepository $pembiayaanRabRepository)
    {
        $this->pembiayaanRabRepository = $pembiayaanRabRepository;
    }

    public function agronomisGetRabTambahan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = PembiayaanRabTambahan::where('pembiayaan_id', $pembiayaanID)->sum('nilai_tambahan');
        // if (sizeof($result) == 0) {
        //     return ResponseFormatter::error(null, 'Data kosong', 204);
        // }
        return ResponseFormatter::success($result, 'Data rab tambahan berhasil ditambahkan');
    }

    public function agronomisGetImgHasilRekomendasi($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = PembiayaanFotoRekomendasi::where('pembiayaan_kunjungan_id', $pembiayaanKunjunganID)->get(['file_path', 'file_name', 'jenis_foto']);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);;
        }
        return ResponseFormatter::success($result, 'Data foto rekomendasi berhasil didapatkan');
    }

    public function agronomisGetDanaCadangan($pembiayaanID, $itemRabID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $itemRAB = PembiayaanRab::where('item_rab_id', $itemRabID)->get();
        if (!$itemRAB) {
            return ResponseFormatter::error(null, 'Data RAB tidak ditemukan', 404);
        }
        $result = PembiayaanRab::selectRaw('(harga * jumlah) as dana_cadangan')->where('pembiayaan_id', $pembiayaanID)->where('item_rab_id', $itemRabID)->get();
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);;
        }
        return ResponseFormatter::success($result, 'Data dana cadangan berhasil didapatkan');
    }

    public function agronomisGetHargaItemRab($itemRabID)
    {
        $itemRAB = PembiayaanRab::where('item_rab_id', $itemRabID)->get();
        // return $itemRAB;
        if (!$itemRAB) {
            return ResponseFormatter::error(null, 'Data RAB tidak ditemukan', 404);
        }
        $masterItemRAB = MasterItemRab::where('item_rab_id', $itemRabID)->first();
        if (!$masterItemRAB) {
            return ResponseFormatter::error(null, 'Data master RAB tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetHargaItemRab($itemRabID);
        // return $masterItemRAB;

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);;
        }
        return ResponseFormatter::success($result, 'Data harga item rab berhasil didapatkan');
    }

    public function agronomisTotalLahan($pendampingID)
    {
        $pembiayaan = PembiayaanKunjungan::where('pendamping_id', $pendampingID)->get();
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisTotalLahan($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);;
        }
        return ResponseFormatter::success($result, 'Data total lahan berhasil didapatkan');
    }

    public function agronomisGetPendampingId($userID)
    {
        $user = ComUser::find($userID);
        if (!$user) {
            return ResponseFormatter::error(null, 'Data user tidak ditemukan', 404);
        }
        $pendamping = $user->com_user_pendamping()->get();
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data user tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetPendampingId($userID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);;
        }
        return ResponseFormatter::success($result, 'Data pendamping berhasil didapatkan');
    }

    public function agronomisJadwalKegiatanMingguan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisJadwalKegiatanMingguan($pembiayaanKunjungan->pembiayaan_id);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data pendamping berhasil didapatkan');
    }

    public function agronomisGetCheckpointPermintaan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetCheckpointPermintaan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data pendamping berhasil didapatkan');
    }

    public function agronomisGetImgPermintaan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $pembiayaanKunjunganFile = PembiayaanKunjunganFile::where('pembiayaan_kunjungan_id', $pembiayaanKunjunganID)->first();
        if (!$pembiayaanKunjunganFile) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan file tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetImgPermintaan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data pendamping berhasil didapatkan');
    }

    public function agronomisGetRiwayatPenugasan($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetRiwayatPenugasan($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data riwayat berhasil didapatkan');
    }

    public function agronomisGetPenugasan($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetPenugasan($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data penugasan berhasil didapatkan');
    }

    public function agronomisGetRiwayatPermintaan($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetRiwayatPermintaan($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data riwayat permintaan berhasil didapatkan');
    }

    public function agronomisGetPermintaan($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetPermintaan($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data riwayat permintaan berhasil didapatkan');
    }

    public function agronomisGetTestimoni($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetTestimoni($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data testimoni berhasil didapatkan');
    }

    public function agronomisGetDataAkun($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetDataAkun($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data akun berhasil didapatkan');
    }

    public function agronomisDataDashboard($pendampingID)
    {
        $pendamping = Pendamping::find($pendampingID);
        if (!$pendamping) {
            return ResponseFormatter::error(null, 'Data pendamping tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisDataDashboard($pendampingID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data dashboard berhasil didapatkan');
    }

    public function agronomisLaporanPenugasanKunjunganRiwayat($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisLaporanPenugasanKunjunganRiwayat($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    public function agronomisGetSaprodiTambahanPenugasan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetSaprodiTambahanPenugasan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    public function agronomisGetImgLaporanPenugasan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetImgLaporanPenugasan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    // public function agronomisGetImgLaporanPermintaan($pembiayaanKunjunganID)
    // {
    //     $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
    //     if(!$pembiayaanKunjungan)
    //     {
    //         return ResponseFormatter::error(null,'Data pembiayaan kunjungan tidak ditemukan',404);
    //     }
    //     $result = DB::connection('mysql')->select("SELECT hasil_path, hasil_img FROM pembiayaan_kunjungan_hasil  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id",[
    //         'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
    //     ]);
    //     if(sizeof($result) == 0)
    //     {
    //         return response()->json([
    //             'data' => null,
    //             'status' => 204,
    //             'message' => 'Data Kosong',
    //         ]);
    //     }
    //     return ResponseFormatter::success($result,'Data laporan penugasan berhasil didapatkan');
    // }

    public function agronomisGetDetailPermintaan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetDetailPermintaan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    public function agronomisGetCheckpointPenugasan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetCheckpointPenugasan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    public function agronomisGetImgPenugasan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $result = Agronomis::AgronomisGetImgPenugasan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data laporan penugasan berhasil didapatkan');
    }

    public function agronomisGetDataItemSaprodi()
    {
        $masterItemRAB = MasterItemRab::where('active_st', 'yes')->get(['item_rab_id', 'nama_item', 'satuan']);
        return ResponseFormatter::success($masterItemRAB, 'Data item saprodi berhasil didapatkan');
    }

    public function agronomisGetDataBencana()
    {
        $bencana = MasterCekaman::all(['cekaman_nama']);
        return ResponseFormatter::success($bencana, 'Data bencana berhasil didapatkan');
    }

    public function agronomisGetDataHama()
    {
        $hama = MasterHama::all(['hama_nama']);
        return ResponseFormatter::success($hama, 'Data hama berhasil didapatkan');
    }

    public function agronomisGetDataPenyakit()
    {
        $penyakit = MasterPenyakit::all(['penyakit_nama']);
        return ResponseFormatter::success($penyakit, 'Data penyakit berhasil didapatkan');
    }

    public function agronomisUpdateDanaCadangan(UpdateDanaCadanganRequest $request, $pembiayaanID, $itemRabID)
    {
        $pembiayaan = PembiayaanRab::where('pembiayaan_id', $pembiayaanID)->where('item_rab_id', $itemRabID)->first();
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $payload = $request->validated();
        try {
            $result = $this->pembiayaanRabRepository->update($payload, $pembiayaanID, $itemRabID);
            return ResponseFormatter::success($result, 'Data dana cadangan berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function agronomisDelRabTambahan($pembiayaanRabTambahanID)
    {
        $pembiayaanRabTambahan = PembiayaanRabTambahan::find($pembiayaanRabTambahanID);
        if (!$pembiayaanRabTambahan) {
            return ResponseFormatter::error(null, 'Data pembiayaan rab tambahan tidak ditemukan', 404);
        }
        $pembiayaanRabTambahan->delete();
        return ResponseFormatter::success(null, 'Data rab tambahan berhasil dihapus');
    }


    public function agronomisAddImageLahan(PembiayaanKunjunganHasilRequest $request)
    {
        $user = request()->user();
        $payload = $request->validated();
        $payload['mdb'] = $user->user_id;
        $payload['mdb_name'] = $user->user_name;
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanKunjunganHasil::create($payload);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data berhasil tambahkan');
    }
}
