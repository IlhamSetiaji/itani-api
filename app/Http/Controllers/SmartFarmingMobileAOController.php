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

    private function generate_id()
    {
        list($usec, $sec) = explode(" ", microtime());
        $microtime = $sec . $usec;
        $microtime = str_replace(array(',', '.'), array('', ''), $microtime);
        $microtime = substr_replace($microtime, rand(10000, 99999), -2);
        return $microtime;
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

    private function transformsPengajuan($arr_pengajuan)
    {
        return array_map(function ($pengajuan) {
            return $this->transform($pengajuan);
        }, $arr_pengajuan);
    }


    private function transformLahan($lahan)
    {
        return array(
            'pembiayaan_lahan_id' => $lahan['pembiayaan_lahan_id'],
            'pembiayaan_id' => $lahan['pembiayaan_id'],
            'lahan_id' => $lahan['lahan_id'],
            'lahan_kd' => $lahan['lahan_kd'],
            // 'blok_lahan_id' => $lahan['blok_lahan_id'],
            'nama_pemilik' => $lahan['nama_pemilik'],
            'luas_lahan' => $lahan['luas_lahan'],
            'luas_sppt' => $lahan['luas_sppt'],
            'lahan_st' => $lahan['lahan_st'],
            'koordinat' => $lahan['koordinat'],
            // 'geom' => $lahan['geom'],
            'alamat' => $lahan['alamat'],
            'rt' => $lahan['rt'],
            'rw' => $lahan['rw'],
            'prov_id' => $lahan['prov_id'],
            'kab_id' => $lahan['kab_id'],
            'kec_id' => $lahan['kec_id'],
            'kel_id' => $lahan['kel_id'],
            'created_by' => $lahan['created_by'],
            'created_at' => $lahan['created_at'],
            // 'mdb' => $lahan['mdb'],
            // 'mdb_name' => $lahan['mdb_name'],
            // 'mdd' => $lahan['mdd'],
            'geojson' => $lahan['geojson'],
            'latitude' => $lahan['latitude'],
            'longitude' => $lahan['longitude'],
            'prov_nama' => $lahan['prov_nama'],
            'kab_nama' => $lahan['kab_nama'],
            'kec_nama' => $lahan['kec_nama'],
            'kel_nama' => $lahan['kel_nama'],
            'varietas_id' => $lahan['varietas_id'],
            'varietas_nama' => $lahan['varietas_nama'],
            'komoditas_id' => $lahan['komoditas_id'],
            'komoditas_nama' => $lahan['komoditas_nama'],
        );
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
            // return $params['active_st'];
        } else {
            $params = array('subcluster_id' => $subClusterID);
        }
        if (request()->has('per_page')) {
            $result = $this->arrayPaginator(SmartFarmingMobileAO::GetLahan($params), request('per_page'));
        } else {
            $result = SmartFarmingMobileAO::GetLahan($params);
        }
        // return mb_convert_encoding($result, 'UTF-8', 'UTF-8');
        return ResponseFormatter::success($this->transformLahanAnother($result));
    }

    private function transformLahanAnother($result)
    {
        $data = array();
        foreach ($result as $key => $lahan) {
            $lahan_id  = isset($lahan->lahan_id) ? $lahan->lahan_id : $lahan->petak_lahan_id;
            $lahan_st  = isset($lahan->lahan_st) ? $lahan->lahan_st : $lahan->status_kepemilikan;
            $lahan_exists = isset($lahan->exists) ? $lahan->lahan_exists : null;
            $arrData = array(
                'lahan_id'        => $lahan_id,
                'lahan_kd'        => isset($lahan->lahan_kd) ? $lahan->lahan_kd : null,
                'blok_lahan_id'   => isset($lahan->blok_lahan_id) ? $lahan->blok_lahan_id : null,
                'nama_pemilik'    => strtoupper($lahan->nama_pemilik),
                'luas_lahan'      => $lahan->luas_lahan,
                'luas_sppt'       => $lahan->luas_sppt,
                'lahan_st'        => $lahan_st,
                'lahan_exists'    => $lahan_exists,
                // 'koordinat'       => $lahan->koordinat,
                'latitude'        => $lahan->latitude,
                'longitude'       => $lahan->longitude,
                'geojson'         => $lahan->geojson,
                // 'geom'            => $lahan->geom,
                'alamat'          => strtoupper($lahan->alamat),
                'rt'              => isset($lahan->rt) ? $lahan->rt : null,
                'rw'              => isset($lahan->rw) ? $lahan->rw : null,
                'prov_id'         => $lahan->prov_id,
                'kab_id'          => $lahan->kab_id,
                'kec_id'          => $lahan->kec_id,
                'kel_id'          => $lahan->kel_id,
                'active_st'       => isset($lahan->active_st) ? $lahan->active_st : null,
                'created_by'      => isset($lahan->created_by) ? $lahan->created_by : null,
                'created_at'      => isset($lahan->created_at) ? $lahan->created_at : null,
                'blok_lahan_kd'   => isset($lahan->blok_lahan_kd) ? $lahan->blok_lahan_kd : null,
                'blok_lahan_nama' => isset($lahan->blok_lahan_nama) ? $lahan->blok_lahan_nama : null,
                'kelompok_kd'     => isset($lahan->kelompok_kd) ? $lahan->kelompok_kd : null,
                'kelompok_nama'   => isset($lahan->kelompok_nama) ? $lahan->kelompok_nama : null,
                'ketua_kelompok'  => isset($lahan->ketua_kelompok) ? $lahan->ketua_kelompok : null,
                'prov_nama'       => isset($lahan->prov_nama) ? $lahan->prov_nama : null,
                'kab_nama'        => isset($lahan->kab_nama) ? $lahan->kab_nama : null,
                'kec_nama'        => isset($lahan->kec_nama) ? $lahan->kec_nama : null,
                'kel_nama'        => isset($lahan->kel_nama) ? $lahan->kel_nama : null,
            );
            array_push($data, $arrData);
        }
        return $data;
    }

    public function getKios()
    {
        // $result = MasterKios::where('active_st', 'yes')
        //     ->with('wilayah_kelurahan', function ($query) {
        //         $query->get(['kel_nama']);
        //     })->get(['kios_id', 'kios_kd', 'kios_nama']);
        // $result = MasterKios::with('wilayah_kelurahan:kel_nama')->where('active_st', 'yes')->get(['kios_id', 'kios_kd', 'kios_nama']);
        // $result = DB::select("SELECT a.kios_id, a.kios_kd , a.kios_nama, b.kel_nama FROM master_kios a
        // JOIN wilayah_kelurahan b ON a.kel_id = b.kel_id
        // WHERE a.active_st = 'yes'");
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
        return ResponseFormatter::success($this->transformVarietas($result), 'Data varietas berhasil didapatkan');
    }

    private function transformVarietas($result)
    {
        $data = array();
        foreach ($result as $key => $varietas) {
            $arrData = array(
                'varietas_id'   => $varietas->varietas_id,
                'varietas_nama' => $varietas->varietas_nama,
            );
            array_push($data, $arrData);
        }
        return $data;
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
        $rab = $this->calculateRab(request('luas_lahan'));
        unset($rab['kegiatan_mingguan']);
        $rab['item'] = $this->transformsItemRab($rab['item']);
        return ResponseFormatter::success($rab, 'Data rab berhasil didapatkan');
    }

    private function calculateRab($luas_lahan)
    {
        $rabs = Rab::where('active_st', 'yes')->orderBy('nama_rab', 'asc')->get()->toArray();
        $satu_hektar = 10000;
        //default
        $paket_terkecil = 1500;
        foreach ($rabs as $key => $rab) {
            // $break = false;
            $rab['nilai_pembiayaan'] = 0;
            $current_luas_lahan = $satu_hektar;
            if ($luas_lahan < $paket_terkecil) {
                $current_luas_lahan = $paket_terkecil;
                // $break = true;
            } else if ($luas_lahan < $satu_hektar) {
                $tmp_current_luas_lahan = $luas_lahan / 500;
                $current_luas_lahan = 500 * (ceil($tmp_current_luas_lahan));
                // $break = true;
            }
            $rabMingguan = SmartFarmingMobileAO::GetDetailRabMingguan($rab['rab_id'], $current_luas_lahan);
            // merubah collection object menjadi array
            $arr_rab_mingguan = array_map(function ($value) {
                return (array)$value;
            }, $rabMingguan);
            foreach ($arr_rab_mingguan as  $rab_mingguan) {
                // array item
                $rab['item'][$rab_mingguan['item_rab_id']]['item_rab_id']  = $rab_mingguan['item_rab_id'];
                $rab['item'][$rab_mingguan['item_rab_id']]['nama_item']    = $rab_mingguan['nama_item'];
                $rab['item'][$rab_mingguan['item_rab_id']]['harga']        = $rab_mingguan['harga'];
                $rab['item'][$rab_mingguan['item_rab_id']]['satuan']       = $rab_mingguan['satuan'];
                if (isset($rab['item'][$rab_mingguan['item_rab_id']]['jumlah'])) {
                    $rab['item'][$rab_mingguan['item_rab_id']]['jumlah']  += $rab_mingguan['jumlah'];
                } else {
                    $rab['item'][$rab_mingguan['item_rab_id']]['jumlah']  = $rab_mingguan['jumlah'];
                }
                // nilai pembiayaan
                $rab['nilai_pembiayaan'] += ($rab_mingguan['jumlah'] * $rab_mingguan['harga']);
                // kegiatan mingguan
                $key = $rab_mingguan['proses_tanam_id'] . $rab_mingguan['item_rab_id'];
                $rab['kegiatan_mingguan'][$key]['rab_detail_mingguan_id'] = $rab_mingguan['rab_detail_mingguan_id'];
                $rab['kegiatan_mingguan'][$key]['proses_tanam_id']        = $rab_mingguan['proses_tanam_id'];
                $rab['kegiatan_mingguan'][$key]['proses_tanam_nama']      = $rab_mingguan['proses_tanam_nama'];
                $rab['kegiatan_mingguan'][$key]['item_rab_id']            = $rab_mingguan['item_rab_id'];
                $rab['kegiatan_mingguan'][$key]['nama_item']              = $rab_mingguan['nama_item'];
                $rab['kegiatan_mingguan'][$key]['harga']                  = $rab_mingguan['harga'];
                $rab['kegiatan_mingguan'][$key]['satuan']                 = $rab_mingguan['satuan'];
                $rab['kegiatan_mingguan'][$key]['sort']                   = $rab_mingguan['sort'];
                if (isset($rab['kegiatan_mingguan'][$key]['jumlah'])) {
                    $rab['kegiatan_mingguan'][$key]['jumlah'] += $rab_mingguan['jumlah'];
                } else {
                    $rab['kegiatan_mingguan'][$key]['jumlah'] = $rab_mingguan['jumlah'];
                }
            }
            usort($rab['kegiatan_mingguan'], function ($a, $b) {
                if ($a['sort'] == $b['sort']) {
                    return $a['rab_detail_mingguan_id'] - $b['rab_detail_mingguan_id'];
                }
                return $a['sort'] - $b['sort'];
            });
            $rab['item'] = array_values($rab['item']);
            $rab['kegiatan_mingguan'] = array_values($rab['kegiatan_mingguan']);
            return $rab;
        }
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

    public function postLahan()
    {
        $validator = Validator::make(request()->all(), [
            // 'lahan_id' => 'required',
            // 'lahan_kd' => 'required',
            'nama_pemilik' => 'required',
            'luas_lahan' => 'required',
            'luas_sppt' => 'required',
            'lahan_st' => 'nullable',
            'koordinat' => 'required',
            'geojson' => 'required',
            'alamat' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'prov_id' => 'required',
            'kab_id' => 'required',
            'kec_id' => 'required',
            'kel_id' => 'required',
            // 'kelompok_id' => 'required',
            'blok_lahan_id' => 'nullable',
            'cluster_id' => 'required',
            'subcluster_id' => 'required',
            'user_id' => 'required',
            'nama_lengkap' => 'required',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        $lahan_ref_id = 'REF' . $this->generate_id();
        $subcluster = SmartFarmingMobileAO::GetMasterSubclusterByID(request('subcluster_id'));
        // return $subcluster[0]->prov_id;
        $prefix = self::PREFIX_KD_LAHAN . $subcluster[0]->prov_id . $subcluster[0]->cluster_kd . $subcluster[0]->subcluster_kd;
        // return $prefix;
        $geojson = json_decode(request('geojson'), true);
        // return json_encode($geojson);
        $params = array(
            'lahan_id'         => $this->generate_id(),
            'lahan_ref_id'     => $lahan_ref_id,
            'cluster_id'       => request('cluster_id'),
            'subcluster_id'    => request('subcluster_id'),
            'lahan_kd'         => SmartFarmingMobileAO::GenerateKD($prefix),
            'blok_lahan_id'    => request('blok_lahan_id'),
            'nama_pemilik'     => request('nama_pemilik'),
            'luas_lahan'       => request('luas_lahan'),
            'luas_sppt'        => request('luas_sppt'),
            'lahan_st'         => request('lahan_st'),
            'koordinat'        => request('koordinat'),
            // 'geojson'          => '{"type":"Feature","properties":{},"geometry":{"type":"Polygon","coordinates":[[[110.405356,-7.735614],[110.405359,-7.735604],[110.405345,-7.735567],[110.405347,-7.735569],[110.405353,-7.73556],[110.40536,-7.735563],[110.405358,-7.735545],[110.405356,-7.735531],[110.405351,-7.735519],[110.405348,-7.735493],[110.405355,-7.735468],[110.405381,-7.735449],[110.405406,-7.73545],[110.405433,-7.735463],[110.40543,-7.73547],[110.405431,-7.735476],[110.405433,-7.735489],[110.405434,-7.735501],[110.405437,-7.735516],[110.405441,-7.735532],[110.405444,-7.735546],[110.405446,-7.735556],[110.405445,-7.735569],[110.405445,-7.735569],[110.405445,-7.735566],[110.405445,-7.735564],[110.405445,-7.735561],[110.405356,-7.735614]]]}}',
            'geojson'          => request('geojson'),
            'alamat'           => request('alamat'),
            'rt'               => request('rt'),
            'rw'               => request('rw'),
            'prov_id'          => request('prov_id'),
            'kab_id'           => request('kab_id'),
            'kec_id'           => request('kec_id'),
            'kel_id'           => request('kel_id'),
            'active_st'        => 'yes',
            'created_by'       => request('user_id'),
            'created_at'       => now(),
            'mdb'              => request('user_id'),
            'mdb_name'         => request('nama_lengkap'),
            'mdd'              => now(),
            'validasi_by'      => request('user_id'),
            'validasi_by_name' => request('nama_lengkap'),
            'validasi_at'      => now(),
        );
        try {
            $result = SmartFarmingMobileAO::InsertLahan($params);
            return ResponseFormatter::success($result, 'Data lahan berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }
}
