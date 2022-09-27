<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Rab;
use App\Models\MasterKios;
use Illuminate\Http\Request;
use App\Models\MasterProvinsi;
use App\Models\MasterKomoditas;
use App\Models\PendataanPetani;
use App\Models\MasterSubCluster;
use App\Models\WilayahKabupaten;
use App\Models\WilayahKecamatan;
use App\Models\WilayahKelurahan;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\PostLahanRequest;
use App\Models\MasterSubClusterSF;
use Illuminate\Support\Facades\DB;
use App\Models\SmartFarmingMobileAO;
use App\Models\MasterJenjangPendidikan;
use App\Repositories\SmartFarmingMobileAORepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class SmartFarmingMobileAOController extends Controller
{
    const PREFIX_KD_LAHAN = 'L';
    private SmartFarmingMobileAORepository $smartFarmingMobileAORepository;

    public function __construct(SmartFarmingMobileAORepository $smartFarmingMobileAORepository)
    {
        $this->smartFarmingMobileAORepository = $smartFarmingMobileAORepository;
    }

    private function arrayPaginator($array, $pageLimit)
    {
        $page = 1;
        $perPage = $pageLimit;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true),
            count($array),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function rupiah($number, $decimal = 0)
    {
        if (!$double = (float) $number) {
            return $number;
        }
        return number_format($double, $decimal, ',', '.');
    }

    public function format_hektar($number)
    {
        return $this->rupiah(round($number / 10000, 2), 2);
    }

    public function summaryGet($subClusterID)
    {
        $subCluster = PendataanPetani::where('subcluster_id', $subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data subcluster tidak ditemukan');
        }
        $params = array('subcluster_id' => $subClusterID);
        // dd($total[0]->total);
        $result = [
            'jumlah_petani' => SmartFarmingMobileAO::CountAllPetani($params),
            'jumlah_lahan' => SmartFarmingMobileAO::CountAllLahanBySubclusterID($subClusterID),
            'total_luas_lahan' => $this->format_hektar(SmartFarmingMobileAO::SumLuasLahanBySubclusterID($subClusterID)),
            'jumlah_pengajuan' => SmartFarmingMobileAO::CountAllPengajuan($params),
            'total_nilai_pengajuan' => SmartFarmingMobileAO::SumAllPembiayaan($params),
        ];
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetAll($subClusterID)
    {
        $subCluster = PendataanPetani::where('subcluster_id', $subClusterID);
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data subcluster tidak ditemukan');
        }
        $params = array('subcluster_id' => $subClusterID);
        $result = SmartFarmingMobileAO::petaniGetAll($params);
        return ResponseFormatter::success($result, 'Data petani berhasil didapatkan');
    }

    public function petaniGetDetail($petaniID)
    {
        $alamat = SmartFarmingMobileAO::GetPetaniAlamat($petaniID);
        $files = SmartFarmingMobileAO::GetPetaniFiles($petaniID);
        $result = SmartFarmingMobileAO::PetaniGetByID($petaniID);
        $data = $this->smartFarmingMobileAORepository->petaniGetDetail($alamat, $files, $result);
        return ResponseFormatter::success($data, 'Data detail petani berhasil didapatkan');
    }

    public function getListPengajuanPembiayaanBySubClusterID($subClusterID)
    {
        $params = array('subcluster_id' => $subClusterID);
        $result = SmartFarmingMobileAO::GetListPengajuanPembiayaan($params);
        $data = $this->smartFarmingMobileAORepository->transformPengajuan($result);
        return ResponseFormatter::success($data, 'Data list pengajuan pembiayaan berhasil didapatkan');
    }

    public function getJenjangPendidikan()
    {
        $jenjang = MasterJenjangPendidikan::all(['jenjang_id', 'jenjang_nama']);
        if (sizeof($jenjang) == 0) {
            return ResponseFormatter::error(null, 'Data jenjang tidak ditemukan', 404);
        }
        return ResponseFormatter::success($jenjang, 'Data jenjang ditemukan');
    }

    public function getKomoditas()
    {
        $komoditas = MasterKomoditas::where('kelompok_id', 3)->where('active_st', 'yes')->get(['komoditas_id', 'komoditas_nama']);
        if (sizeof($komoditas) == 0) {
            return ResponseFormatter::error(null, 'Data komoditas tidak ditemukan', 404);
        }
        return ResponseFormatter::success($komoditas, 'Data komoditas ditemukan');
    }

    public function getProvinsi()
    {
        $provinsi = MasterProvinsi::all(['prov_id', 'prov_nama']);
        if (sizeof($provinsi) == 0) {
            return ResponseFormatter::error(null, 'Data provinsi tidak ditemukan', 404);
        }
        return ResponseFormatter::success($provinsi, 'Data provinsi ditemukan');
    }

    public function getLahanBySubclusterID($subClusterID)
    {
        if (request()->has('active_st')) {
            $params = array(
                'subcluster_id' => $subClusterID,
                'active_st' => request('active_st'),
            );
        } else {
            $params = array('subcluster_id' => $subClusterID);
        }
        if (request()->has('per_page')) {
            $result = $this->arrayPaginator(SmartFarmingMobileAO::GetLahan($params), request('per_page'));
        } else {
            $result = SmartFarmingMobileAO::GetLahan($params);
        }
        return ResponseFormatter::success($this->smartFarmingMobileAORepository->transformLahanAnother($result), 'Data lahan berhasil didapatkan');
    }

    public function getKios()
    {
        $result = SmartFarmingMobileAO::GetKios();
        if (sizeof($result) == 0) {
            return ResponseFormatter::error(null, 'Data kios tidak ditemukan', 404);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function getVarietasByKomoditas()
    {
        $params = array(
            'active_st' => 'yes',
            'komoditas_id' => request('komoditas_id'),
        );
        $result = SmartFarmingMobileAO::VarietasKomoditasByCluster(request('cluster_id'), $params);
        return ResponseFormatter::success($this->smartFarmingMobileAORepository->transformVarietas($result), 'Data varietas berhasil didapatkan');
    }

    public function getLahanByArea()
    {
        $params = array(
            'longitude' => request('longitude'),
            'latitude'  => request('latitude'),
            'radius'    => request('radius'),
        );
    }

    public function pengajuanPembiayaanByRabLuas()
    {
        $rab = $this->smartFarmingMobileAORepository->calculateRab(request('luas_lahan'), request('cluster_id'));
        unset($rab['kegiatan_mingguan']);
        $rab['item'] = $this->smartFarmingMobileAORepository->transformsItemRab($rab['item']);
        return ResponseFormatter::success($rab, 'Data rab berhasil didapatkan');
    }

    public function getKabupatenByProvinsi()
    {
        $kabupaten = WilayahKabupaten::where('prov_id', request('prov_id'))->get(['kab_id', 'kab_nama']);
        if (sizeof($kabupaten) == 0) {
            return ResponseFormatter::error(null, 'Data kabupaten kosong', 204);
        }
        return ResponseFormatter::success($kabupaten, 'Data kabupaten berhasil didapatkan');
    }

    public function getKecamatanByKabupaten()
    {
        $kec = WilayahKecamatan::where('kab_id', request('kab_id'))->get(['kec_id', 'kec_nama']);
        if (sizeof($kec) == 0) {
            return ResponseFormatter::error(null, 'Data kecamatan kosong', 204);
        }
        return ResponseFormatter::success($kec, 'Data kecamatan berhasil didapatkan');
    }

    public function getKelurahanByKecamatan()
    {
        $kel = WilayahKelurahan::where('kec_id', request('kec_id'))->get(['kel_id', 'kel_nama']);
        if (sizeof($kel) == 0) {
            return ResponseFormatter::error(null, 'Data kelurahan kosong', 204);
        }
        return ResponseFormatter::success($kel, 'Data kelurahan berhasil didapatkan');
    }

    public function postLahan(PostLahanRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->smartFarmingMobileAORepository->postLahan($payload);
            return ResponseFormatter::success($payload, 'Data lahan berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }
}
