<?php

namespace App\Http\Controllers;

use LDAP\Result;
use App\Models\User;
use App\Models\Petani;
use App\Models\ComUser;
use App\Models\Pembiayaan;
use Illuminate\Http\Request;
use App\Models\MasterCluster;
use App\Models\PembiayaanRab;
use App\Models\AccountOfficer;
use App\Models\PesanNotifikasi;
use App\Models\MasterSubCluster;
use App\Models\PembiayaanPetani;
use App\Models\MasterProsesTanam;
use App\Helpers\ResponseFormatter;
use App\Models\PembiayaanRabMingguan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class AccountOfficerController extends Controller
{
    public function __construct()
    {
        $this->middleware('ao');
    }

    public function aoGetMonitoringPelaksanaan($petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->get();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data pembiayaan petani tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetMonitoringPelaksanaan($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data monitoring pelaksanaan berhasil didapatkan');
    }

    public function aoGetDetailKiosBySubclusterId($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetDetailKiosBySubclusterId($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data detail kios berhasil didapatkan');
    }

    public function aoGetKiosbyClusterAndSubClusterId($clusterID, $subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $cluster = MasterCluster::find($clusterID);
        if (!$cluster) {
            return ResponseFormatter::error(null, 'Data cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetKiosbyClusterAndSubClusterId($clusterID, $subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function aoGetDetailKiosbyClusterId($clusterID, $subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $cluster = MasterCluster::find($clusterID);
        if (!$cluster) {
            return ResponseFormatter::error(null, 'Data cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetDetailKiosbyClusterId($clusterID, $subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function aoGetMonitoringPencairanbyClusterAndSubCluster($clusterID, $subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $cluster = MasterCluster::find($clusterID);
        if (!$cluster) {
            return ResponseFormatter::error(null, 'Data cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetMonitoringPencairanbyClusterAndSubCluster($clusterID, $subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data monitoring berhasil didapatkan');
    }

    public function aoGetMonitoringPersetujuan($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetMonitoringPersetujuan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data monitoring berhasil didapatkan');
    }

    public function aoGetDataAoHome($userID)
    {
        $user = ComUser::find($userID);
        if (!$user) {
            return ResponseFormatter::error(null, 'Data user tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetDataAoHome($userID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data monitoring berhasil didapatkan');
    }

    public function aoGetKiosBySubclusterId($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetKiosBySubclusterId($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function petaniGetListDetailRencanaKegiatanByPetaniId($petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->get();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data pembiayaan petani tidak ditemukan', 404);
        }
        $result = AccountOfficer::PetaniGetListDetailRencanaKegiatanByPetaniId($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data rencana kegiatan berhasil didapatkan');
    }

    public function aogetMonitoring($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AogetMonitoring($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data monitoring berhasil didapatkan');
    }

    public function aoGetJumlahRencanaKegiatan($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetJumlahRencanaKegiatan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data rencana kegiatan berhasil didapatkan');
    }

    public function aoRencanaKegiatan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoRencanaKegiatan($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data rencana kegiatan berhasil didapatkan');
    }

    public function aoPostPesanSaprodi()
    {
        $validator = Validator::make(request()->all(), [
            'app_id' => 'required|numeric',
            'petani_id' => 'required|numeric',
            'pesan_judul' => 'required|string',
            'pesan_isi' => 'required|string',
            'pesan_status' => 'required|string',
            'pesan_kategori' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        $pesan = PesanNotifikasi::create([
            'petani_id' => request('petani_id'),
            'pesan_judul' => request('pesan_judul'),
            'pesan_isi' => request('pesan_isi'),
            'pesan_status' => request('pesan_status'),
            'pesan_kategori' => request('pesan_kategori'),
        ]);
        return ResponseFormatter::success($pesan, 'Data pesan berhasil ditambahkan');
    }

    public function aoGetDataKios($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $prosesTanam = PembiayaanRabMingguan::where('proses_tanam_id', $prosesTanamID)->get();
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetDataKios($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function aoPostPembiayaanTenagaKerja()
    {
        $validator = Validator::make(request()->all(), [
            'pembiayaan_rab_id' => 'required|numeric',
            'pembiayaan_id' => 'required|numeric',
            'item_rab_id' => 'required|numeric',
            'jumlah' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        $pembiayaanRab = PembiayaanRab::create([
            'pembiayaan_rab_id' => request('pembiayaan_rab_id'),
            'pembiayaan_id' => request('pembiayaan_id'),
            'item_rab_id' => request('item_rab_id'),
            'jumlah' => request('jumlah'),
            'harga' => request('harga'),
        ]);
        return ResponseFormatter::success($pembiayaanRab, 'Data pembiayaan rab berhasil ditambahkan');
    }

    public function aoGetKesiapanSaprodi($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $prosesTanam = PembiayaanRabMingguan::where('proses_tanam_id', $prosesTanamID)->get();
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetKesiapanSaprodi($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kesiapan saprodi berhasil didapatkan');
    }

    public function aoGetJumlahMonitoringPelaksanaan($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetJumlahMonitoringPelaksanaan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data jumlah monitoring berhasil didapatkan');
    }

    public function aoGetRencanaKegiatan($subClusterID)
    {
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data sub cluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetRencanaKegiatan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data rencana kegiatan berhasil didapatkan');
    }

    public function aoGetRencanaKegiatanByClusterAndSubCluster($clusterID, $subClusterID)
    {
        $cluster = MasterCluster::find($clusterID);
        if (!$cluster) {
            return ResponseFormatter::error(null, 'Data cluster tidak ditemukan', 404);
        }
        $subCluster = MasterSubCluster::find($subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data subcluster tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetRencanaKegiatanByClusterAndSubCluster($clusterID, $subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetKesiapanTenaga($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $prosesTanam = MasterProsesTanam::find($prosesTanamID);
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = Petani::AoGetKesiapanTenaga($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetJumlahKesiapanTenaga($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $prosesTanam = MasterProsesTanam::find($prosesTanamID);
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = Petani::AoGetJumlahKesiapanTenaga($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetJumlahKesiapanSaprodi($pembiayaanID, $prosesTanamID)
    {
        // $pembiayaan = PembiayaanRab::where('pembiayaan_id', $pembiayaanID)->first();
        // if (!$pembiayaan) {
        //     return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        // }
        $prosesTanam = PembiayaanRabMingguan::where('proses_tanam_id', $prosesTanamID)->get();
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetJumlahKesiapanSaprodi($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetKios($pembiayaanID, $prosesTanamID)
    {
        // $pembiayaan = PembiayaanRab::where('pembiayaan_id', $pembiayaanID)->first();
        // if (!$pembiayaan) {
        //     return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        // }
        $prosesTanam = PembiayaanRabMingguan::where('proses_tanam_id', $prosesTanamID)->get();
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = AccountOfficer::AoGetKios($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoKesiapanVerifikasi($pengajuanID, $prosesTanamID)
    {
        $pembiayaanRabMingguan = PembiayaanRabMingguan::where('pengajuan_id', $pengajuanID)->where('proses_tanam_id', $prosesTanamID)->get();
        if (sizeof($pembiayaanRabMingguan) == 0) {
            return ResponseFormatter::error(null, 'Data Rab Mingguan tidak ditemukan', 404);
        }
        try {
            foreach ($pembiayaanRabMingguan as $key => $value) {
                $value->update([
                    "kesiapan_kegiatan_st" => 'yes',
                    "rencana_kegiatan_st" => "process",
                    "kesiapan_kegiatan_date" => Carbon::now(),
                    "rencana_kegiatan_st_date" => Carbon::now(),
                    "catatan_lahan" => request('catatan_lahan'),
                ]);
            }
            return ResponseFormatter::success($pembiayaanRabMingguan, 'Data berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e, 400);
        }
    }
}
