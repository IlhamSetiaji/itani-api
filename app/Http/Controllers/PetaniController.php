<?php

namespace App\Http\Controllers;

use App\Models\Petani;
use App\Models\Pembiayaan;
use Illuminate\Http\Request;
use App\Models\DataTransaksi;
use App\Models\AccountOfficer;
use App\Helpers\ResponseFormatter;
use App\Models\ComUser;
use App\Models\MasterHargaGabah;
use App\Models\MasterPajak;
use App\Models\MasterProsesTanam;
use App\Models\MasterRekeningPetani;
use App\Models\MasterRetribusi;
use App\Models\PembiayaanKunjungan;
use App\Models\PanenPenimbanganHasil;
use App\Models\PembiayaanFotoKegiatanPetani;
use App\Models\PembiayaanFotoRekomendasi;
use App\Models\PembiayaanKunjunganFile;
use App\Models\PembiayaanLahan;
use App\Models\PembiayaanPetani;
use App\Models\PembiayaanRab;
use App\Models\PembiayaanRabMingguan;
use App\Models\PembiayaanRabTambahan;
use App\Models\PendataanPetani;
use App\Models\PesanNotifikasi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PetaniController extends Controller
{
    public function __construct()
    {
        $this->middleware('petani');
    }

    public function petaniGetRealisasiKegiatan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetRealisasiKegiatan($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data realisasi kegiatan berhasil didapatkan');
    }

    public function petaniGetTotalPendapatanBersihPetani($petaniID)
    {
        $petani = PanenPenimbanganHasil::where('petani_id', $petaniID)->get();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data pembiayaan petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTotalPendapatanBersihPetani($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data total pendapatan petani berhasil didapatkan');
    }

    public function petaniGetTotalSaldoRekening($petaniID, $pembiayaanID)
    {
        $pembiayaan = DataTransaksi::where('pembiayaan_id', $pembiayaanID)->first();
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $petani = DataTransaksi::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTotalSaldoRekening($petaniID, $pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data total saldo rekening berhasil didapatkan');
    }

    public function petaniGetDataTransaksiPembiayaan($petaniID, $tahun, $bulan)
    {
        $petani = DataTransaksi::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetDataTransaksiPembiayaan($petaniID, $bulan, $tahun);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data transaksi pembiayaan berhasil didapatkan');
    }

    public function petaniGetImgHasilRekomendasi($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetImgHasilRekomendasi($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data image hasil rekomendasi berhasil didapatkan');
    }

    public function petaniGetSaldoRekeningPetani($petaniID, $pembiayaanID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetSaldoRekeningPetani($petaniID, $pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data saldo rekening petani berhasil didapatkan');
    }

    public function petaniGetTransaksiSaldoPembiayaan($tahun, $bulan, $petaniID, $pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $petani = PanenPenimbanganHasil::where('petani_id', $petaniID)->get();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data pembiayaan petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTransaksiSaldoPembiayaan($tahun, $bulan, $petaniID, $pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data transaksi saldo pembiayaan berhasil didapatkan');
    }

    public function petaniGetRekeningPelunasanPembiayaan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetRekeningPelunasanPembiayaan($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data rekening pelunasan pembiayaan berhasil didapatkan');
    }

    public function petaniGetFotoKegiatan($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $prosesTanam = PembiayaanFotoKegiatanPetani::where('proses_tanam_id', $prosesTanamID)->first();
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetFotoKegiatan($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data foto kegiatan didapatkan');
    }

    public function petaniAddFotoRekomendasi()
    {
        $user = request()->user();
        $validator = Validator::make(request()->all(), [
            'pembiayaan_foto_rekomendasi_id' => 'required|numeric',
            'pembiayaan_kunjungan_id' => 'required|string',
            'pembiayaan_id' => 'required|numeric',
            'jenis_kunjungan' => 'required|string',
            'jenis_foto' => 'required|string',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        try {
            $result = PembiayaanFotoRekomendasi::create([
                'pembiayaan_foto_rekomendasi_id' => request('pembiayaan_foto_rekomendasi_id'),
                'pembiayaan_kunjungan_id' => request('pembiayaan_kunjungan_id'),
                'pembiayaan_id' => request('pembiayaan_id'),
                'jenis_kunjungan' => request('jenis_kunjungan'),
                'jenis_foto' => request('jenis_foto'),
                'file_path' => request('file_path'),
                'file_name' => request('file_name'),
                'mdb' => $user->user_id,
                'mdb_name' => $user->user_name,
                'mdd' => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data foto rekomendasi berhasil ditambahkan');
    }

    public function petaniAddFotoKonfirmasiKegiatan()
    {
        $user = request()->user();
        $validator = Validator::make(request()->all(), [
            'pembiayaan_foto_kegiatan_id' => 'required|numeric',
            'proses_tanam_id' => 'required|numeric',
            'pembiayaan_id' => 'required|numeric',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        try {
            $result = PembiayaanFotoKegiatanPetani::create([
                'pembiayaan_foto_kegiatan_id' => request('pembiayaan_foto_kegiatan_id'),
                'proses_tanam_id' => request('proses_tanam_id'),
                'pembiayaan_id' => request('pembiayaan_id'),
                'file_path' => request('file_path'),
                'file_name' => request('file_name'),
                'mdb' => $user->user_id,
                'mdb_name' => $user->user_name,
                'mdd' => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data foto kegiatan berhasil ditambahkan');
    }

    public function petaniGetSaldoPetani($petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetSaldoPetani($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data foto kegiatan didapatkan');
    }

    public function petaniGetSaldoPencairanMingguIni($tahun, $bulan, $petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetSaldoPencairanMingguIni($tahun, $bulan, $petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data foto kegiatan didapatkan');
    }

    public function petaniGetTransaksiSaldoFeeDistribusi($tahun, $bulan, $petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTransaksiSaldoFeeDistribusi($tahun, $bulan, $petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetRetribusi()
    {
        $result = MasterRetribusi::where('active_st', 'yes')->get();
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniUpdatePengambilanSaprodiTambahan($pembiayaanRabTambahanID)
    {
        $pembiayaanRabTambahan = PembiayaanRabTambahan::find($pembiayaanRabTambahanID);
        if (!$pembiayaanRabTambahan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        try {
            $result = $pembiayaanRabTambahan->update([
                'pengambilan_st' => 'yes',
                'pengambilan_date' => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }

        return ResponseFormatter::success($result, 'Data berhasil diupdate');
    }

    public function petaniGetPengambilanSaprodiTambahan($pembiayaanRabTambahanID)
    {
        $pembiayaanRabTambahan = PembiayaanRabTambahan::find($pembiayaanRabTambahanID);
        if (!$pembiayaanRabTambahan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetPengambilanSaprodiTambahan($pembiayaanRabTambahanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data foto kegiatan didapatkan');
    }

    public function petaniPengambilanSaprodiTambahanAll($petaniID, $pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniPengambilanSaprodiTambahanAll($petaniID, $pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPhotoKunjunganLahan($pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetPhotoKunjunganLahan($pembiayaanKunjunganID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniAddImageLahan()
    {
        $user = request()->user();
        $validator = Validator::make(request()->all(), [
            'file_id' => 'required|numeric',
            'pembiayaan_kunjungan_id' => 'required|string',
            'file_path' => 'required|string',
            'file_img' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        try {
            $result = PembiayaanKunjunganFile::create([
                'file_id' => request('file_id'),
                'pembiayaan_kunjungan_id' => request('pembiayaan_kunjungan_id'),
                'file_path' => request('file_path'),
                'file_img' => request('file_img'),
                'mdb' => $user->user_id,
                'mdb_name' => $user->user_name,
                'mdd' => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data berhasil tambahkan');
    }

    public function petaniGetPajak()
    {
        $result = MasterPajak::where('active_st', 'yes')->get();
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPengajuanPembiayaan($petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetPengajuanPembiayaan($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetDetailPesan($pesanID)
    {
        $result = PesanNotifikasi::find($pesanID);
        if (!$result) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPesan($petaniID)
    {
        $petani = ComUser::find($petaniID);
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = PesanNotifikasi::where('user_id', $petaniID)->orderBy('pesan_id', 'desc')->get();
        if (!$result) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetDataRekeningPetani($petaniID)
    {
        $petani = MasterRekeningPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = MasterRekeningPetani::where('petani_id', $petaniID)->get(['petani_id', 'rekening']);
        if (!$result) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetDetailPengajuanPembiayaanRAB($pengajuanID)
    {
        $pengajuan = Pembiayaan::where('pengajuan_id', $pengajuanID)->first();
        if (!$pengajuan) {
            return ResponseFormatter::error(null, 'Data pengajuan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetDetailPengajuanPembiayaanRAB($pengajuanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetDetailPengajuanPembiayaan($pengajuanID, $lahanID)
    {
        $pengajuan = Pembiayaan::where('pengajuan_id', $pengajuanID)->first();
        if (!$pengajuan) {
            return ResponseFormatter::error(null, 'Data pengajuan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetDetailPengajuanPembiayaan($pengajuanID, $lahanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetGeoJsonLahan($pembiayaanLahanID)
    {
        $pembiayaanLahan = PembiayaanLahan::find($pembiayaanLahanID);
        if (!$pembiayaanLahan) {
            return ResponseFormatter::error(null, 'Data pembiayaan lahan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetGeoJsonLahan($pembiayaanLahanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetTotalPembiayaan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTotalPembiayaan($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPengambilanSaprodi($pembiayaanRabMingguanID)
    {
        $pembiayaanRabMingguan = PembiayaanRabMingguan::find($pembiayaanRabMingguanID);
        if (!$pembiayaanRabMingguan) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetPengambilanSaprodi($pembiayaanRabMingguanID);
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

    public function aoPostPembiayaanTenagaKerja()
    {
        $validator = Validator::make(request()->all(), [
            'pembiayaan_rab_id' => 'required|numeric',
            'pembiayaan_id' => 'required|numeric',
            'item_rab_id' => 'required|numeric',
            'jumlah' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator, $validator->messages(), 403);
        }
        try {
            $result = PembiayaanRab::create([
                'pembiayaan_rab_id' => request('pembiayaan_rab_id'),
                'pembiayaan_id' => request('pembiayaan_id'),
                'item_rab_id' => request('item_rab_id'),
                'jumlah' => request('jumlah'),
                'harga' => request('harga'),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data berhasil diinputkan');
    }

    public function aoGetKesiapanSaprodi($pembiayaanID, $prosesTanamID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $prosesTanam = MasterProsesTanam::find($prosesTanamID);
        if (!$prosesTanam) {
            return ResponseFormatter::error(null, 'Data proses tanam tidak ditemukan', 404);
        }
        $result = Petani::AoGetKesiapanSaprodi($pembiayaanID, $prosesTanamID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetJumlahMonitoringPelaksanaan($subClusterID)
    {
        $subCluster = Pembiayaan::where('subcluster_id', $subClusterID)->first();
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::AoGetJumlahMonitoringPelaksanaan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function aoGetRencanaKegiatan($subClusterID)
    {
        $subCluster = Pembiayaan::where('subcluster_id', $subClusterID)->first();
        if (!$subCluster) {
            return ResponseFormatter::error(null, 'Data tidak ditemukan', 404);
        }
        $result = Petani::AoGetRencanaKegiatan($subClusterID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniPengambilanSaprodiAll($petaniID, $pembiayaanID)
    {
        $data = Petani::PetaniPengambilanSaprodiAll($petaniID, $pembiayaanID);
        $dataPetani = Petani::GetItemRabByPetaniPembiayaan($petaniID, $pembiayaanID);
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
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
        // $result = array();

        // if (sizeof($result) == 0) {
        //     return response()->json([
        //         'data' => null,
        //         'status' => 204,
        //         'message' => 'Data Kosong',
        //     ]);
        // }

    }

    public function petaniGetDataPetani($petaniID)
    {
        // $petani = PendataanPetani::find($petaniID);
        // if (!$petani) {
        //     return ResponseFormatter::error(null, 'Data petani tidak ditemukan',404);
        // }
        $result = Petani::petaniGetDataPetani($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPembiayaan($petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::petaniGetPembiayaan($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniHargaGabahTerkini()
    {
        $result = MasterHargaGabah::where('active_st', 'yes')->get(['harga_terkini']);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetPembiayaanAktif($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $result = Petani::petaniGetPembiayaanAktif($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetKunjunganLahan($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetKunjunganLahan($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniGetLahan($petaniID)
    {
        $result = Petani::PetaniGetLahan($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }
}
