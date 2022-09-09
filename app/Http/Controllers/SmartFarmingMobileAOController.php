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
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class SmartFarmingMobileAOController extends Controller
{
    const PREFIX_KD_LAHAN = 'L';

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
        foreach ($alamat as $value) {
            $result[$value->jenis_kd] = $value;
        }
        foreach ($files as $value) {
            $result[$value->file_field] = $value;
        }
        // return $result;
        return $this->transform($result);
    }

    private function transform($petani)
    {
        $file_path = empty($petani->file_name)
            ? "/images/pendamping/default.png"
            : $petani['file_path'] . $petani['file_name'];
        $image_url = "http://ftp.itani.id/" . $file_path;
        $alamat_domisili = array_key_exists('alamat_domisili', $petani) ? $this->transformAlamat($petani['alamat_domisili']) : null;
        $alamat_ktp      = array_key_exists('alamat_ktp', $petani) ? $this->transformAlamat($petani['alamat_ktp']) : null;
        $alamat_tempat_kerja = array_key_exists('alamat_tempat_kerja', $petani) ? $this->transformAlamat($petani['alamat_tempat_kerja']) : null;
        $file_ktp        = array_key_exists('file_ktp', $petani) ? $this->transformFile($petani['file_ktp']) : null;
        $file_kk         = array_key_exists('file_kk', $petani) ? $this->transformFile($petani['file_kk']) : null;
        $file_npwp       = array_key_exists('file_npwp', $petani) ? $this->transformFile($petani['file_npwp']) : null;
        $file_pas_foto   = array_key_exists('file_pas_foto', $petani) ? $this->transformFile($petani['file_pas_foto']) : null;
        $file_pas_foto_pasangan = array_key_exists('file_pas_foto_pasangan', $petani) ? $this->transformFile($petani['file_pas_foto_pasangan']) : null;
        $file_surat_nikah = array_key_exists('file_surat_nikah', $petani) ? $this->transformFile($petani['file_surat_nikah']) : null;
        return array(
            'petani_id' => $petani[0]->petani_id,
            'petani_kd' => $petani[0]->petani_kd,
            'image_url' => $image_url,
            'alamat'    => $petani[0]->alamat,
            'rt'        => $petani[0]->rt,
            'rw'        => $petani[0]->rw,
            'prov_id'   => $petani[0]->prov_id,
            'kab_id'    => $petani[0]->kab_id,
            'kec_id'    => $petani[0]->kec_id,
            'kel_id'    => $petani[0]->kel_id,
            'kode_pos'  => $petani[0]->kode_pos,
            'no_telp'   => $petani[0]->no_telp,
            'active_st' => $petani[0]->active_st,
            'nik'       => $petani[0]->nik,
            'nama_lengkap'      => strtoupper($petani[0]->nama_lengkap),
            'tempat_lahir'      => $petani[0]->tempat_lahir,
            'tanggal_lahir'     => $petani[0]->tanggal_lahir,
            'jenis_kelamin'     => $petani[0]->jenis_kelamin,
            'status_perkawinan' => $petani[0]->status_perkawinan,
            'nama_pasangan'     => strtoupper($petani[0]->nama_pasangan),
            'jenis_kelamin_pasangan' => $petani[0]->jenis_kelamin_pasangan,
            'tanggungan_istri'  => $petani[0]->tanggungan_istri,
            'tanggungan_anak'   => $petani[0]->tanggungan_anak,
            'tanggungan_ortu'   => $petani[0]->tanggungan_ortu,
            'tanggungan_lain'   => $petani[0]->tanggungan_lain,
            'nama_gadis_ibu'    => strtoupper($petani[0]->nama_gadis_ibu),
            'email'             => strtolower($petani[0]->email),
            'no_npwp'           => $petani[0]->no_npwp,
            'status_rumah'      => $petani[0]->status_rumah,
            'tinggal_sejak'     => $petani[0]->tinggal_sejak,
            'jenis_pekerjaan'   => $petani[0]->jenis_pekerjaan,
            'jabatan_pekerjaan' => $petani[0]->jabatan_pekerjaan,
            'status_pekerjaan'  => $petani[0]->status_pekerjaan,
            'bekerja_sejak'     => $petani[0]->bekerja_sejak,
            'alamat_koresponden' => $petani[0]->alamat_koresponden,
            'jenjang_id'        => $petani[0]->jenjang_id,
            'nama_instansi'     => $petani[0]->nama_instansi,
            'alamat_instansi'   => $petani[0]->alamat_instansi,
            'jurusan_pendidikan' => $petani[0]->jurusan_pendidikan,
            'tahun_lulus'   => $petani[0]->tahun_lulus,
            'created_by'    => $petani[0]->created_by,
            'created_at'    => $petani[0]->created_at,
            'prov_nama'     => $petani[0]->prov_nama,
            'kab_nama'      => $petani[0]->kab_nama,
            'kec_nama'      => $petani[0]->kec_nama,
            'kel_nama'      => $petani[0]->kel_nama,
            'jenjang_pendidikan'  => $petani[0]->jenjang_pendidikan,
            'alamat_domisili'     => $alamat_domisili,
            'alamat_ktp'          => $alamat_ktp,
            'alamat_tempat_kerja' => $alamat_tempat_kerja,
            'file_ktp'         => $file_ktp,
            'file_kk'          => $file_kk,
            'file_npwp'        => $file_npwp,
            'file_pas_foto'    => $file_pas_foto,
            'file_pas_foto_pasangan' => $file_pas_foto_pasangan,
            'file_surat_nikah' => $file_surat_nikah,
            'kios_id'   => $petani[0]->kios_id,
            'kios_kd'   => $petani[0]->kios_kd,
            'kios_nama' => $petani[0]->kios_nama
        );
    }

    private function transformAlamat($alamat)
    {
        return array(
            'alamat_id' => $alamat->alamat_id,
            'alamat'    => strtoupper($alamat->alamat),
            'rt'        => $alamat->rt,
            'rw'        => $alamat->rw,
            'prov_id'   => $alamat->prov_id,
            'kab_id'    => $alamat->kab_id,
            'kec_id'    => $alamat->kec_id,
            'kel_id'    => $alamat->kel_id,
            'kode_pos'  => $alamat->kode_pos,
            'no_telp'   => $alamat->no_telp,
            'prov_nama' => $alamat->prov_nama,
            'kab_nama'  => $alamat->kab_nama,
            'kec_nama'  => $alamat->kec_nama,
            'kel_nama'  => $alamat->kel_nama,
        );
    }

    private function transformFile($file)
    {
        $file_path = $file->file_path . $file->file_name;
        $file_url  = "http://ftp.itani.id/" . $file_path;
        return array(
            'file_id'    => $file->file_id,
            'file_field' => $file->file_field,
            'file_title' => $file->file_title,
            'file_desc'  => $file->file_desc,
            'file_name'  => $file->file_name,
            'file_orig_name' => $file->file_orig_name,
            'file_path'  => $file->file_path,
            'file_url'   => $file_url,
        );
    }

    public function getListPengajuanPembiayaanBySubClusterID($subClusterID)
    {
        $params = array('subcluster_id' => $subClusterID);
        $result = SmartFarmingMobileAO::GetListPengajuanPembiayaan($params);
        // return $result;
        return $this->transformPengajuan($result);
    }

    private function transformsPengajuan($arr_pengajuan)
    {
        return array_map(function ($pengajuan) {
            return $this->transform($pengajuan);
        }, $arr_pengajuan);
    }

    private function transformPengajuan($pengajuan)
    {
        $file_path = empty($pengajuan->file_name)
            ? "/images/pendamping/default.png"
            : $pengajuan->file_path . $pengajuan->file_name;
        $image_url = "http://ftp.itani.id/" . $file_path;
        $lahan = null;
        if (isset($pengajuan->lahan)) {
            $lahan = array_map(function ($l) {
                return $this->transformLahan($l);
            }, $pengajuan->lahan);
        }
        $alamat_domisili  = isset($pengajuan->alamat_domisili) ? $this->transformAlamat($pengajuan->alamat_domisili) : null;
        $alamat_ktp       = isset($pengajuan->alamat_ktp) ? $this->transformAlamat($pengajuan->alamat_ktp) : null;
        $alamat_tempat_kerja = isset($pengajuan->alamat_tempat_kerja) ? $this->transformAlamat($pengajuan->alamat_tempat_kerja) : null;
        $file_ktp         = isset($pengajuan->file_ktp) ? $this->transformFile($pengajuan->file_ktp) : null;
        $file_kk          = isset($pengajuan->file_kk) ? $this->transformFile($pengajuan->file_kk) : null;
        $file_npwp        = isset($pengajuan->file_npwp) ? $this->transformFile($pengajuan->file_npwp) : null;
        $file_pas_foto    = isset($pengajuan->file_pas_foto) ? $this->transformFile($pengajuan->file_pas_foto) : null;
        $file_pas_foto_pasangan = isset($pengajuan->file_pas_foto_pasangan) ? $this->transformFile($pengajuan->file_pas_foto_pasangan) : null;
        $file_surat_nikah = isset($pengajuan->file_surat_nikah) ? $this->transformFile($pengajuan->file_surat_nikah) : null;
        $item_rab         = isset($pengajuan->item_rab) ? $this->transformsItemRab($pengajuan->item_rab) : null;
        return array(
            'pembiayaan_id'     => $pengajuan->pembiayaan_id,
            'pengajuan_id'      => $pengajuan->pengajuan_id,
            'nilai_pembiayaan'  => $pengajuan->nilai_pembiayaan,
            'active_st'         => $pengajuan->active_st,
            'petani_id'         => $pengajuan->petani_id,
            'petani_kd'         => $pengajuan->petani_kd,
            'image_url'         => $image_url,
            'no_telp'           => $pengajuan->no_telp,
            'nik'               => $pengajuan->nik,
            'nama_lengkap'      => strtoupper($pengajuan->nama_lengkap),
            'tempat_lahir'      => isset($pengajuan->tempat_lahir) ? $pengajuan->tempat_lahir : null,
            'tanggal_lahir'     => isset($pengajuan->tanggal_lahir) ? $pengajuan->tanggal_lahir : null,
            'jenis_kelamin'     => isset($pengajuan->jenis_kelamin) ? $pengajuan->jenis_kelamin : null,
            'status_perkawinan' => isset($pengajuan->status_perkawinan) ? $pengajuan->status_perkawinan : null,
            'nama_pasangan'     => isset($pengajuan->nama_pasangan) ? strtoupper($pengajuan->nama_pasangan) : null,
            'jenis_kelamin_pasangan' => isset($pengajuan->jenis_kelamin_pasangan) ? $pengajuan->jenis_kelamin_pasangan : null,
            'tanggungan_istri'  => isset($pengajuan->tanggungan_istri) ? $pengajuan->tanggungan_istri : null,
            'tanggungan_anak'   => isset($pengajuan->tanggungan_anak) ? $pengajuan->tanggungan_anak : null,
            'tanggungan_ortu'   => isset($pengajuan->tanggungan_ortu) ? $pengajuan->tanggungan_ortu : null,
            'tanggungan_lain'   => isset($pengajuan->tanggungan_lain) ? $pengajuan->tanggungan_lain : null,
            'nama_gadis_ibu'    => isset($pengajuan->nama_gadis_ibu) ? strtoupper($pengajuan->nama_gadis_ibu) : null,
            'email'             => isset($pengajuan->email) ? strtolower($pengajuan->email) : null,
            'no_npwp'           => isset($pengajuan->no_npwp) ? $pengajuan->no_npwp : null,
            'status_rumah'      => isset($pengajuan->status_rumah) ? $pengajuan->status_rumah : null,
            'tinggal_sejak'     => isset($pengajuan->tinggal_sejak) ? $pengajuan->tinggal_sejak : null,
            'jenis_pekerjaan'   => isset($pengajuan->jenis_pekerjaan) ? $pengajuan->jenis_pekerjaan : null,
            'jabatan_pekerjaan' => isset($pengajuan->jabatan_pekerjaan) ? $pengajuan->jabatan_pekerjaan : null,
            'status_pekerjaan'  => isset($pengajuan->status_pekerjaan) ? $pengajuan->status_pekerjaan : null,
            'bekerja_sejak'     => isset($pengajuan->bekerja_sejak) ? $pengajuan->bekerja_sejak : null,
            'alamat_koresponden' => isset($pengajuan->alamat_koresponden) ? $pengajuan->alamat_koresponden : null,
            'jenjang_id'        => isset($pengajuan->jenjang_id) ? $pengajuan->jenjang_id : null,
            'nama_instansi'     => isset($pengajuan->nama_instansi) ? $pengajuan->nama_instansi : null,
            'alamat_instansi'   => isset($pengajuan->alamat_instansi) ? $pengajuan->alamat_instansi : null,
            'jurusan_pendidikan' => isset($pengajuan->jurusan_pendidikan) ? $pengajuan->jurusan_pendidikan : null,
            'tahun_lulus'       => isset($pengajuan->tahun_lulus) ? $pengajuan->tahun_lulus : null,
            'luas_lahan'        => $pengajuan->luas_lahan,
            'lahan'             => $lahan,
            'alamat_domisili'   => $alamat_domisili,
            'alamat_ktp'        => $alamat_ktp,
            'alamat_tempat_kerja' => $alamat_tempat_kerja,
            'file_ktp'          => $file_ktp,
            'file_kk'           => $file_kk,
            'file_npwp'         => $file_npwp,
            'file_pas_foto'     => $file_pas_foto,
            'file_pas_foto_pasangan' => $file_pas_foto_pasangan,
            'file_surat_nikah'  => $file_surat_nikah,
            'created_at'        => $pengajuan->created_at,
            'created_by'        => $pengajuan->created_by,
            'pengajuan_st'      => $pengajuan->pengajuan_st,
            'pengajuan_keterangan' => $pengajuan->pengajuan_keterangan,
            'item_rab'          => $item_rab,
        );
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

    private function transformsItemRab($arr_item)
    {
        return array_map(function ($item) {
            return $this->transformItemRab($item);
        }, $arr_item);
    }

    private function transformItemRab($item)
    {
        $jumlah_biaya = $item['jumlah'] * $item['harga'];
        return array(
            'item_rab_id'  => $item['item_rab_id'],
            'nama_item'    => $item['nama_item'],
            'satuan'       => $item['satuan'],
            'jumlah'       => $item['jumlah'],
            'harga'        => $item['harga'],
            'jumlah_biaya' => $jumlah_biaya,
        );
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
            'lahan_st' => 'required',
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
            'blok_lahan_id' => 'required',
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
            'geojson'          => '{"type":"Point","coordinates":[-48.23456,20.12345]}',
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
