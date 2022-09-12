<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\SupirController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\AgronomisController;
use App\Http\Controllers\PembiayaanController;
use App\Http\Controllers\AccountOfficerController;
use App\Http\Controllers\SmartFarmingMobileAOController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('change-password', [TestController::class, 'insertPassword']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('smartfarming')->group(function () {
    Route::get('/home/summary/{subcluster_id}', [SmartFarmingMobileAOController::class, 'summaryGet']);
    Route::get('petani/{subcluster_id}', [SmartFarmingMobileAOController::class, 'petaniGetAll']);
    Route::get('petani/detail/{petaniID}', [SmartFarmingMobileAOController::class, 'petaniGetDetail']);
    Route::get('/pengajuan_pembiayaan/{subcluster_id}', [SmartFarmingMobileAOController::class, 'getListPengajuanPembiayaanBySubClusterID']);
    Route::get('/jenjang_pendidikan', [SmartFarmingMobileAOController::class, 'getJenjangPendidikan']);
    Route::get('/komoditas', [SmartFarmingMobileAOController::class, 'getKomoditas']);
    Route::get('/provinsi', [SmartFarmingMobileAOController::class, 'getProvinsi']);
    Route::get('/lahan/{subcluster_id}', [SmartFarmingMobileAOController::class, 'getLahanBySubclusterID']);
    Route::get('/kios', [SmartFarmingMobileAOController::class, 'getKios']);
    Route::get('/varietas/by_komoditas', [SmartFarmingMobileAOController::class, 'getVarietasByKomoditas']);
    Route::get('/pengajuan_pembiayaan/rab/by_luas', [SmartFarmingMobileAOController::class, 'pengajuanPembiayaanByRabLuas']);
    Route::get('/kabupaten/by_provinsi', [SmartFarmingMobileAOController::class, 'getKabupatenByProvinsi']);
    Route::get('/kecamatan/by_kabupaten', [SmartFarmingMobileAOController::class, 'getKecamatanByKabupaten']);
    Route::get('/kelurahan/by_kecamatan', [SmartFarmingMobileAOController::class, 'getKelurahanByKecamatan']);
    Route::post('/lahan', [SmartFarmingMobileAOController::class, 'postLahan']);
});

Route::prefix('supir')->group(function () {
    Route::get('/get/id/petani', [SupirController::class, 'supirGetPetaniAktif']);
    Route::get('/get/data/dashboard/{supirID}', [SupirController::class, 'supirGetDataDashboard']);
    Route::get('/get/pengangkutan/aktif/{supirID}', [SupirController::class, 'supirGetPengangkutanAktif']);
    Route::get('/get/data/akun/{userID}', [SupirController::class, 'supirGetDataAkun']);
    Route::get('/get/pengangkutan/terkirim/{supirID}', [SupirController::class, 'supirGetPengangkutanTerkirim']);
    Route::get('/detail/pengangkutan/terkirim/{pengankutanID}', [SupirController::class, 'supirGetDetailPengangkutanTerkirim']);
    Route::get('/get/data/truk', [SupirController::class, 'supirGetDataTruk']);
});

/* Route::prefix('petani')->group(
    function () {
        Route::get('/get/jadwal/{pembiayaanID}', [PetaniController::class, 'petaniGetJadwal']);
        Route::get('/get/hasilpanen/{pembiayaanID}', [PetaniController::class, 'petaniGetHasilPanen']);
        Route::get('/get/saprodi/pengambilan/grub2/{petaniID}/{pembiayaanID}', [PetaniController::class, 'petaniPengambilanSaprodiGrup2']);
        Route::get('/get/saprodi/pengambilan/grub3/{petaniID}/{pembiayaanID}', [PetaniController::class, 'petaniPengambilanSaprodiGrup3']);
    }
); */

// Route::get('/',[PembiayaanController::class,'getLahanBysubclusterBysubcluster']);
Route::post('/ipangan/petani/post/permintaan/kunjungan', [TestController::class, 'petaniPostPermintaanKunjungan']);
Route::post('/ipangan/agronomis/laporan/permintaan/aktif/{pembiayaan_kunjungan_id}', [TestController::class, 'agronomisLaporanPermintaanKunjungan']);
Route::post('/ipangan/agronomis/rab/tambahan', [TestController::class, 'agronomisAddRabTambahan']);
Route::post('/smartfarming/verifikasi_kegiatan/sidangkomite_mingguan/{pembiayaanID}/{pengajuanID}/{prosesTanamID}', [TestController::class, 'postSidangKomite']);
Route::post('/smartfarming/verifikasi_kegiatan/sidangkomite_tambahan/{pembiayaanID}/{pengajuanID}', [TestController::class, 'postSidangKomiteTambahan']);
Route::post('/ipangan/supir/add/pengangkutan', [TestController::class, 'insertPengangkutan']);
Route::post('/ipangan/supir/add/hasil/panen', [TestController::class, 'addHasilPanen']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [PembiayaanController::class, 'getLahanBysubclusterBysubcluster']);
    Route::get('/rencana', [PembiayaanController::class, 'getRencanaKegiatan']);
    Route::prefix('ipangan')->group(function () {
        Route::prefix('agronomis')->group(function () {
            Route::get('/total/rab/tambahan/{pembiayaan_id}', [AgronomisController::class, 'agronomisGetRabTambahan']);
            Route::get('/img/hasil/rekomendasi/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetImgHasilRekomendasi']);
            Route::get('/dana/cadangan/{pembiayaan_id}/{item_rab_id}', [AgronomisController::class, 'agronomisGetDanaCadangan']);
            Route::get('/harga/item/rab/{item_rab_id}', [AgronomisController::class, 'agronomisGetHargaItemRab']);
            Route::get('/total/luas/lahan/{pendamping_id}', [AgronomisController::class, 'agronomisTotalLahan']);
            Route::get('/data/pendamping/{user_id}', [AgronomisController::class, 'agronomisGetPendampingId']);
            Route::get('/jadwal/kegiatan/mingguan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisJadwalKegiatanMingguan']);
            Route::get('/get/checkpoint/permintaan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetCheckpointPermintaan']);
            Route::get('/img/permintaan/aktif/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetImgPermintaan']);
            Route::get('/get/penugasan/riwayat/{pendamping_id}', [AgronomisController::class, 'agronomisGetRiwayatPenugasan']);
            Route::get('/get/penugasan/aktif/{pendamping_id}', [AgronomisController::class, 'agronomisGetRiwayatPenugasan']);
            Route::get('/get/permintaan/riwayat/{pendamping_id}', [AgronomisController::class, 'agronomisGetRiwayatPermintaan']);
            Route::get('/get/permintaan/aktif/{pendamping_id}', [AgronomisController::class, 'agronomisGetPermintaan']);
            Route::get('/data/testimoni/{pendamping_id}', [AgronomisController::class, 'agronomisGetTestimoni']);
            Route::get('/data/akun/{pendamping_id}', [AgronomisController::class, 'agronomisGetDataAkun']);
            Route::get('/data/dashboard/{pendamping_id}', [AgronomisController::class, 'agronomisDataDashboard']);
            Route::get('/laporan/penugasan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisLaporanPenugasanKunjunganRiwayat']);
            Route::get('/hasil/saprodi/tambahan/penugasan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetSaprodiTambahanPenugasan']); // Ini masih error karena di table pembiayaan_rab_tambahan tidak terdapat FK item_rab_id dan kolom jumlah
            Route::get('/get/img/laporan/penugasan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetImgLaporanPenugasan']);
            // Route get saprodi tambahan ambigu, karena cuma ada welcome tanpa code di action fusio
            Route::get('/get/img/laporan/permintaan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetImgLaporanPenugasan']);
            Route::get('/detail/permintaan/aktif/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetDetailPermintaan']);
            Route::get('/get/checkpoint/penugasan/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetCheckpointPenugasan']);
            Route::get('/img/penugasan/aktif/{pembiayaan_kunjungan_id}', [AgronomisController::class, 'agronomisGetImgPenugasan']);
            Route::get('/get/data/itemsaprodi', [AgronomisController::class, 'agronomisGetDataItemSaprodi']);
            Route::get('/get/data/bencana', [AgronomisController::class, 'agronomisGetDataBencana']);
            Route::get('/get/data/hama', [AgronomisController::class, 'agronomisGetDataHama']);
            Route::get('/get/data/penyakit', [AgronomisController::class, 'agronomisGetDataPenyakit']);
            Route::put('/update/dana/cadangan/{pembiayaan_id}/{item_rab_id}', [AgronomisController::class, 'agronomisUpdateDanaCadangan']);
            Route::delete('/del/hasil/rab/tambahan/{pembiayaan_rab_tambahan_id}', [AgronomisController::class, 'agronomisDelRabTambahan']);
            Route::delete('/del/hasil/saprodi/tambahan/permintaan/{pembiayaan_rab_tambahan_id}', [AgronomisController::class, 'agronomisDelRabTambahan']);
        });


        Route::prefix('ao')->group(function () {
            Route::get('/get/monitoring/pelaksanaan/{petani_id}', [AccountOfficerController::class, 'aoGetMonitoringPelaksanaan']);
            Route::get('/get/monitoring/pencairan/jumlah/{petani_id}', [AccountOfficerController::class, 'aoGetMonitoringPelaksanaan']);
            Route::get('/get/detail/kios/{subcluster_id}', [AccountOfficerController::class, 'aoGetDetailKiosBySubclusterId']);
            Route::get('/get/kios/by/{clusterID}/{subClusterID}', [AccountOfficerController::class, 'aoGetKiosbyClusterAndSubClusterId']);
            Route::get('/get/detail/kios/{cluster_id}/{subcluster_id}', [AccountOfficerController::class, 'aoGetDetailKiosbyClusterId']);
            Route::get('/get/monitoring/pencairan/{cluster_id}/{subcluster_id}', [AccountOfficerController::class, 'aoGetMonitoringPencairanbyClusterAndSubCluster']);
            Route::get('/get/jumlah/monitoring/persetujuan/{subcluster_id}', [AccountOfficerController::class, 'aoGetMonitoringPersetujuan']);
            Route::get('/get/home/data_accountofficer/{user_id}', [AccountOfficerController::class, 'aoGetDataAoHome']);
            Route::get('/get/kios/by_subcluster/{subcluster_id}', [AccountOfficerController::class, 'aoGetKiosBySubclusterId']);
            Route::get('/get/list/detail_rencana_kegiatan/{petani_id}', [AccountOfficerController::class, 'petaniGetListDetailRencanaKegiatanByPetaniId']);
            Route::get('/get/monitoring/pencairan/{subcluster_id}', [AccountOfficerController::class, 'aogetMonitoring']);
            Route::get('/get/rencana/jumlah/{subcluster_id}', [AccountOfficerController::class, 'aoGetJumlahRencanaKegiatan']);
            Route::get('/get/ringkasan/kegiatan/{pembiayaan_id}', [AccountOfficerController::class, 'aoRencanaKegiatan']);
            Route::post('/post/pesan/saprodi', [AccountOfficerController::class, 'aoPostPesanSaprodi']);
            Route::get('/get/kios/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetDataKios']);
            Route::post('/post/pembiayaan_rab/tenaga/kerja', [AccountOfficerController::class, 'aoPostPembiayaanTenagaKerja']);
            Route::get('/get/kesiapansaprodi/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetKesiapanSaprodi']);
            Route::get('/get/monitoring/pelaksanaan/jumlah/{subClusterID}', [AccountOfficerController::class, 'aoGetJumlahMonitoringPelaksanaan']);
            Route::get('/get/rencanakegiatan/{subClusterID}', [AccountOfficerController::class, 'aoGetRencanaKegiatan']);
            Route::get('/get/rencana/kegiatan/{cluster_id}/{subcluster_id}', [AccountOfficerController::class, 'aoGetRencanaKegiatanByClusterAndSubCluster']);
            Route::get('/get/tenaga/kerja/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetKesiapanTenaga']);
            Route::get('/get/jumlah/tenaga/kerja/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetJumlahKesiapanTenaga']);
            Route::get('/get/jumlah/kesiapansaprodi/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetJumlahKesiapanSaprodi']);
            Route::get('/get/kesiapanlahan/{pembiayaan_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoGetKios']);
            Route::post('/post/update/kesiapanverifikasi/{pembiayaan_rab_id}/{proses_tanam_id}', [AccountOfficerController::class, 'aoKesiapanVerifikasi']);
        });
        Route::prefix('petani')->group(function () {
            Route::get('/get/total/pembiayaan_aktif/{pembiayaan_id}', [PetaniController::class, 'petaniGetRealisasiKegiatan']);
            Route::get('/get/pendapatan_bersih/{petani_id}', [PetaniController::class, 'petaniGetTotalPendapatanBersihPetani']); // Kolom pendapatan_bersih not found di table panen_penimbangan_hasil
            Route::get('/get/realisasi/dana_kegiatan/{pembiayaan_id}', [PetaniController::class, 'petaniGetRealisasiKegiatan']);
            Route::get('/get/total/saldo_petani/{petani_id}/{pembiayaan_id}', [PetaniController::class, 'petaniGetTotalSaldoRekening']);
            Route::get('/get/data/transaksi/pembiayaan/{petani_id}/{tahun}/{bulan}', [PetaniController::class, 'petaniGetDataTransaksiPembiayaan']);
            Route::get('/img/hasil/rekomendasi/{pembiayaan_kunjungan_id}', [PetaniController::class, 'petaniGetImgHasilRekomendasi']);
            Route::get('/get/rekening/saldo/pembiayaan/{petani_id}/{pembiayaan_id}', [PetaniController::class, 'petaniGetSaldoRekeningPetani']);
            Route::get('/get/rekening/transaksipembiayaan/{tahun}/{bulan}/{petani_id}/{pembiayaan_id}', [PetaniController::class, 'petaniGetTransaksiSaldoPembiayaan']); // Invalid number parameter
            Route::get('/get/pelunasan/pembiayaan/{pembiayaan_id}', [PetaniController::class, 'petaniGetRekeningPelunasanPembiayaan']);
            Route::get('/get/foto/hasil/kegiatan/{pembiayaan_id}/{proses_tanam_id}', [PetaniController::class, 'petaniGetFotoKegiatan']);
            Route::post('/add/img/rekomendasi/kegiatan', [PetaniController::class, 'petaniAddFotoRekomendasi']);
            Route::post('/add/img/konfirmasi/kegiatan', [PetaniController::class, 'petaniAddFotoKonfirmasiKegiatan']);
            Route::get('/get/rekening/saldopetani/{petani_id}', [PetaniController::class, 'petaniGetSaldoPetani']); // Column not found
            Route::get('/get/rekening/traksaksi/saldo/pencairan/now/{tahun}/{bulan}/{petani_id}', [PetaniController::class, 'petaniGetSaldoPencairanMingguIni']);
            Route::get('/get/rekening/traksaksi/saldo/fee_distribusi/now/{tahun}/{bulan}/{petani_id}', [PetaniController::class, 'petaniGetTransaksiSaldoFeeDistribusi']);
            Route::get('/get/retribusi/aktif', [PetaniController::class, 'petaniGetRetribusi']);
            Route::post('/post/konfirmasi/pengambilan_saprodi_tambahan/{pembiayaan_rab_tambahan_id}', [PetaniController::class, 'petaniUpdatePengambilanSaprodiTambahan']);
            Route::get('/get/saprodi_tambahan/pengambilan/{pembiayaan_rab_tambahan_id}', [PetaniController::class, 'petaniGetPengambilanSaprodiTambahan']);
            Route::get('/get/saprodi_tambahan/pengambilan/all/{petani_id}/{pembiayaan_id}', [PetaniController::class, 'petaniPengambilanSaprodiTambahanAll']); // Column not found
            Route::get('/get/photo/kunjungan_lahan/{pembiayaan_kunjungan_id}', [PetaniController::class, 'petaniGetPhotoKunjunganLahan']);
            Route::post('/add/img/lahan', [PetaniController::class, 'petaniAddImageLahan']);
            Route::get('/get/pajak', [PetaniController::class, 'petaniGetPajak']);
            Route::get('/get/pengajuanpembiayaan/{petani_id}', [PetaniController::class, 'petaniGetPengajuanPembiayaan']);
            Route::get('/get/pesan/detail/{pesan_id}', [PetaniController::class, 'petaniGetDetailPesan']);
            Route::get('/get/pesan/{petani_id}', [PetaniController::class, 'petaniGetPesan']);
            Route::get('/get/petani/rekening/{petani_id}', [PetaniController::class, 'petaniGetDataRekeningPetani']);
            Route::get('/get/pengajuanpembiayaan/detail/rab/{pengajuan_id}', [PetaniController::class, 'petaniGetDetailPengajuanPembiayaanRAB']);
            Route::get('/get/pengajuanpembiayaan/detail/{pengajuan_id}/{lahan_id}', [PetaniController::class, 'petaniGetDetailPengajuanPembiayaan']);
            Route::get('/get/lahan/geojson/{pembiayaan_lahan_id}', [PetaniController::class, 'petaniGetGeoJsonLahan']);
            Route::get('/get/pembiayaan/terpakai/{pembiayaan_id}', [PetaniController::class, 'petaniGetTotalPembiayaan']);
            Route::get('/get/saprodi/pengambilan/{pembiayaan_rab_mingguan_id}', [PetaniController::class, 'petaniGetPengambilanSaprodi']);
            Route::get('/get/jumlah/tenaga/kerja/{pembiayaan_id}/{proses_tanam_id}', [PetaniController::class, 'aoGetJumlahKesiapanTenaga']);
            Route::get('/get/tenaga/kerja/{pembiayaan_id}/{proses_tanam_id}', [PetaniController::class, 'aoGetKesiapanTenaga']);
            Route::post('/post/pembiayaan_rab/tenaga/kerja', [PetaniController::class, 'aoPostPembiayaanTenagaKerja']);
            Route::get('/get/kesiapansaprodi/{pembiayaan_id}/{proses_tanam_id}', [PetaniController::class, 'aoGetKesiapanSaprodi']);
            Route::get('/get/monitoring/pelaksanaan/jumlah/{subcluster_id}', [PetaniController::class, 'aoGetJumlahMonitoringPelaksanaan']);
            Route::get('/get/rencanakegiatan/{subcluster_id}', [PetaniController::class, 'aoGetRencanaKegiatan']);
            Route::get('/get/saprodi/pengambilan/all/{petaniID}/{pembiayaanID}', [PetaniController::class, 'petaniPengambilanSaprodiAll']);
            Route::get('/get/petani/{petani_id}', [PetaniController::class, 'petaniGetDataPetani']);
            Route::get('/get/totalpembiayaan/all/{petani_id}', [PetaniController::class, 'petaniGetPembiayaan']);
            Route::get('/hargagabah/terkini', [PetaniController::class, 'petaniHargaGabahTerkini']);
            Route::get('/get/pembiayaan/aktif/{pembiayaan_id}', [PetaniController::class, 'petaniGetPembiayaanAktif']);
            Route::get('/kunjungan/lahan/{pembiayaan_id}', [PetaniController::class, 'petaniGetKunjunganLahan']);
            Route::get('/get/lahan/all/{petani_id}', [PetaniController::class, 'petaniGetLahan']);
            Route::get('/get/jadwal/{pembiayaanID}', [PetaniController::class, 'petaniGetJadwal']);
            Route::get('/get/hasilpanen/{pembiayaanID}', [PetaniController::class, 'petaniGetHasilPanen']);
            Route::get('/get/saprodi/pengambilan/grub2/{petaniID}/{pembiayaanID}', [PetaniController::class, 'petaniPengambilanSaprodiGrup2']);
            Route::get('/get/saprodi/pengambilan/grub3/{petaniID}/{pembiayaanID}', [PetaniController::class, 'petaniPengambilanSaprodiGrup3']);
        });
    });
});
