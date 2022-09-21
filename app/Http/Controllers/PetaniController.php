<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Petani;
use App\Models\ComUser;
use App\Models\Pembiayaan;
use App\Models\MasterPajak;
use Illuminate\Http\Request;
use App\Models\DataTransaksi;
use App\Models\PembiayaanRab;
use App\Models\AccountOfficer;
use App\Models\PendataanLahan;
use App\Models\MasterRetribusi;
use App\Models\PembiayaanLahan;
use App\Models\PendataanPetani;
use App\Models\PesanNotifikasi;
use App\Models\MasterHargaGabah;
use App\Models\PembiayaanPetani;
use App\Models\MasterProsesTanam;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\PembiayaanFotoKegiatanPetaniRequest;
use App\Http\Requests\PembiayaanFotoRekomendasiRequest;
use App\Http\Requests\PembiayaanKunjunganFileRequest;
use App\Http\Requests\PembiayaanRabRequest;
use App\Http\Requests\PenilaianKunjunganRequest;
use App\Http\Requests\UpdateKegiatanRekomendasiRequest;
use Illuminate\Support\Facades\DB;
use App\Models\PembiayaanKunjungan;
use App\Models\MasterRekeningPetani;
use App\Models\PanenPenimbanganHasil;
use App\Models\PembiayaanRabMingguan;
use App\Models\PembiayaanRabTambahan;
use App\Models\PembiayaanKunjunganFile;
use App\Models\PembiayaanFotoRekomendasi;
use Illuminate\Support\Facades\Validator;
use App\Models\PembiayaanFotoKegiatanPetani;
use App\Repositories\PetaniRepository;

class PetaniController extends Controller
{
    private PetaniRepository $petaniRepository;

    public function __construct(PetaniRepository $petaniRepository)
    {
        /*   $this->middleware('petani'); */
        $this->petaniRepository = $petaniRepository;
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

    public function petaniAddFotoRekomendasi(PembiayaanFotoRekomendasiRequest $request)
    {
        $user = request()->user();
        $payload = $request->validated();
        $payload['mdb'] = $user->user_id;
        $payload['mdb_name'] = $user->user_name;
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanFotoRekomendasi::create($payload);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), '400');
        }
        return ResponseFormatter::success($result, 'Data foto rekomendasi berhasil ditambahkan');
    }

    public function petaniAddFotoKonfirmasiKegiatan(PembiayaanFotoKegiatanPetaniRequest $request)
    {
        $user = request()->user();
        $payload = $request->validated();
        $payload['mdb'] = $user->user_id;
        $payload['mdb_name'] = $user->user_name;
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanFotoKegiatanPetani::create($payload);
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

    public function petaniAddImageLahan(PembiayaanKunjunganFileRequest $request)
    {
        $user = request()->user();
        $payload = $request->validated();
        $payload['mdb'] = $user->user_id;
        $payload['mdb_name'] = $user->user_name;
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanKunjunganFile::create($payload);
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
        $petani = PendataanPetani::find($petaniID);
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

    public function aoPostPembiayaanTenagaKerja(PembiayaanRabRequest $request)
    {
        $payload = $request->validated();
        try {
            $result = PembiayaanRab::create($payload);
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
        $result = $this->petaniRepository->pengambilanSaprodiAll($data, $dataPetani);
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }

    public function petaniPengambilanSaprodiGrup2($petaniID, $pembiayaanID)
    {
        $data = Petani::PetaniPengambilanSaprodiAll($petaniID, $pembiayaanID);
        $dataPetani = Petani::GetItemRabByGrub2PetaniPembiayaan($petaniID, $pembiayaanID);
        $result = $this->petaniRepository->pengambilanSaprodiGrup2($data, $dataPetani);
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
    }


    public function petaniPengambilanSaprodiGrup3($petaniID, $pembiayaanID)
    {
        $data = Petani::PetaniPengambilanSaprodiAll($petaniID, $pembiayaanID);
        $dataPetani = Petani::GetItemRabByGrub3PetaniPembiayaan($petaniID, $pembiayaanID);
        $result = $this->petaniRepository->pengambilanSaprodiGrup3($data, $dataPetani);
        return ResponseFormatter::success($result, 'Data berhasil didapatkan');
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


    public function petaniGetJadwal($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetJadwal($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Jadwal berhasil didapatkan');
    }


    public function petaniGetHasilPanen($pembiayaanID)
    {
        $pembiayaan = Pembiayaan::find($pembiayaanID);
        if (!$pembiayaan) {
            return ResponseFormatter::error(null, 'Data pembiayaan tidak ditemukan', 404);
        }
        $result = Petani::petaniGetHasilPanen($pembiayaanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Panen berhasil didapatkan');
    }

    public function getLahanShow($lahanID)
    {
        $lahan = PembiayaanLahan::where('lahan_id', $lahanID)->first();
        if (!$lahan) {
            return ResponseFormatter::error(null, 'Data lahan tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetPetaniLahanShow($lahanID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Panen berhasil didapatkan');
    }

    public function petaniGetTransaksiSaldoPetani($tahun, $bulan, $petaniID)
    {
        $petani = PembiayaanPetani::where('petani_id', $petaniID)->first();
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::PetaniGetTransaksiSaldoPetani($tahun, $bulan, $petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data transaksi saldo petani berhasil didapatkan');
    }

    public function petaniUpdateKesanKunjunganLahan(PenilaianKunjunganRequest $request, $pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $payload = $request->validated();
        $payload['penilaian_status'] = 'yes';
        try {
            $pembiayaanKunjungan->update($payload);
            return ResponseFormatter::success($pembiayaanKunjungan, 'Data berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function petaniUpdateKegiatanRekomendasi(UpdateKegiatanRekomendasiRequest $request, $pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $payload = $request->validated();
        $payload['rekomendasi_st'] = 'yes';
        $payload['mdd'] = Carbon::now();
        $payload['mdb'] = $payload['user_id'];
        $payload['mdb_name'] = $payload['nama_petani'];
        unset($payload['user_id']);
        unset($payload['nama_petani']);
        try {
            $pembiayaanKunjungan->update($payload);
            return ResponseFormatter::success($pembiayaanKunjungan, 'Data pembiayaan kunjungan berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }



    public function getDataPhoto($petaniID)
    {
        $petani = PendataanPetani::find($petaniID);
        if (!$petani) {
            return ResponseFormatter::error(null, 'Data petani tidak ditemukan', 404);
        }
        $result = Petani::GetDataPhoto($petaniID);
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data petani berhasil didapatkan');
    }
}
