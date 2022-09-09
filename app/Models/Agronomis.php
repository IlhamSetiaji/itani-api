<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agronomis extends Model
{
    use HasFactory;

    public function scopeAgronomisGetHargaItemRab($query, $itemRabID)
    {
        $query = DB::select("SELECT c.persentase_item_fee, b.harga FROM master_item_rab a JOIN pembiayaan_rab b ON a.item_rab_id = b.item_rab_id JOIN master_item_fee c ON a.item_rab_id = c.item_rab_id WHERE a.item_rab_id = :item_rab_id", [
            'item_rab_id' => $itemRabID,
        ]);
        return $query;
    }

    public function scopeAgronomisTotalLahan($query, $pendampingID)
    {
        $query = DB::select("SELECT ROUND (SUM(IF(a.status_kunjungan = 'no',b.luas_lahan,0)) ,2) AS total_lahan_pending, ROUND (SUM(IF(a.status_kunjungan = 'yes',b.luas_lahan,0)) ,2)AS total_lahan_done FROM pembiayaan_kunjungan a JOIN pembiayaan_lahan b ON a.lahan_id = b.lahan_id WHERE a.pendamping_id = :pendamping_id AND a.pendamping_id IS NOT NULL", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetPendampingId($query, $userID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.pendamping_id, b.image_file_name FROM com_user_pendamping a JOIN pendamping b ON a.pendamping_id = b.pendamping_id WHERE user_id = :user_id", [
            'user_id' => $userID,
        ]);
        return $query;
    }

    public function scopeAgronomisJadwalKegiatanMingguan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT a.nama_lengkap AS nama_petani, a.pembiayaan_id, c.proses_tanam_id, c.periode_kegiatan_start, c.periode_kegiatan_end, c.rencana_kegiatan_st, d.proses_tanam_nama FROM pembiayaan_petani a JOIN pembiayaan_rab b ON a.pembiayaan_id = b.pembiayaan_id JOIN pembiayaan_rab_mingguan c ON b.pembiayaan_rab_id = c.pembiayaan_rab_id JOIN master_proses_tanam d ON c.proses_tanam_id = d.proses_tanam_id WHERE a.pembiayaan_id = :pembiayaan_id GROUP BY c.proses_tanam_id", [
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetCheckpointPermintaan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT latitude_kunjungan, longitude_kunjungan, tanggal_kunjungan FROM pembiayaan_kunjungan  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetImgPermintaan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT file_path, file_img FROM pembiayaan_kunjungan_file  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetRiwayatPenugasan($query, $pendampingID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.pembiayaan_kunjungan_id, a.tanggal_dibuat, a.catatan_kunjungan, a.jenis_kunjungan,
        b.nama_lengkap, b.image_file_name FROM ipangan_finance_v1_db_demo.pembiayaan_kunjungan a
        LEFT JOIN ipangan_sf_v1_db_demo.pendamping b ON a.pendamping_id=b.pendamping_id
        WHERE a.jenis_kunjungan = 'penugasan' AND a.status_kunjungan = 'yes' AND a.pendamping_id IS NOT NULL AND a.pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetPenugasan($query, $pendampingID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.pembiayaan_kunjungan_id, a.lahan_id, a.tanggal_dibuat, a.catatan_kunjungan, a.jenis_kunjungan,
        b.nama_lengkap, b.image_file_name FROM ipangan_finance_v1_db_demo.pembiayaan_kunjungan a
        LEFT JOIN ipangan_sf_v1_db_demo.pendamping b ON a.pendamping_id=b.pendamping_id
        WHERE a.jenis_kunjungan = 'penugasan' AND a.status_kunjungan = 'no' AND a.pendamping_id IS NOT NULL AND a.pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetRiwayatPermintaan($query, $pendampingID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.pembiayaan_kunjungan_id, a.tanggal_dibuat, a.catatan_kunjungan, a.jenis_kunjungan,
        b.nama_lengkap, b.image_file_name FROM ipangan_finance_v1_db_demo.pembiayaan_kunjungan a
        LEFT JOIN ipangan_sf_v1_db_demo.pendamping b ON a.pendamping_id=b.pendamping_id
        WHERE a.jenis_kunjungan = 'permintaan' AND a.status_kunjungan = 'yes' AND a.pendamping_id IS NOT NULL AND a.pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetPermintaan($query, $pendampingID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.pembiayaan_kunjungan_id, a.tanggal_dibuat, a.catatan_kunjungan, a.jenis_kunjungan,
        b.nama_lengkap, b.image_file_name FROM ipangan_finance_v1_db_demo.pembiayaan_kunjungan a
        LEFT JOIN ipangan_sf_v1_db_demo.pendamping b ON a.pendamping_id=b.pendamping_id
        WHERE a.jenis_kunjungan = 'permintaan' AND a.status_kunjungan = 'no' AND a.pendamping_id IS NOT NULL AND a.pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetTestimoni($query, $pendampingID)
    {
        $query = DB::connection('mysql')->select("SELECT a.penilaian_kesan, b.nama_lengkap FROM pembiayaan_kunjungan a JOIN pembiayaan_petani b ON a.pembiayaan_id = b.pembiayaan_id WHERE penilaian_kesan IS NOT NULL AND pendamping_id = :pendamping_id ORDER BY a.pendamping_id DESC LIMIT 8", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetDataAkun($query, $pendampingID)
    {
        $query = DB::connection('mysql_second')->select("SELECT pendamping_id, nama_lengkap, image_file_name FROM pendamping WHERE pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisDataDashboard($query, $pendampingID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(jenis_kunjungan = 'permintaan' AND status_kunjungan ='yes' AND pendamping_id  IS NOT NULL,1,0)) AS total_permintaan_done, SUM(IF(jenis_kunjungan = 'permintaan' AND status_kunjungan ='no' AND pendamping_id  IS NOT NULL,1,0)) AS total_permintaan_pending, SUM(IF(jenis_kunjungan = 'penugasan' AND status_kunjungan ='yes' AND pendamping_id  IS NOT NULL,1,0)) AS total_penugasan_done, SUM(IF(jenis_kunjungan = 'penugasan' AND status_kunjungan ='no' AND pendamping_id  IS NOT NULL,1,0)) AS total_penugasan_pending,SUM(IF(penilaian_petani = '5',1,0)) AS rating_5,SUM(IF(penilaian_petani = '4',1,0)) AS rating_4,SUM(IF(penilaian_petani = '3',1,0)) AS rating_3,SUM(IF(penilaian_petani = '2',1,0)) AS rating_2,SUM(IF(penilaian_petani = '1',1,0)) AS rating_1 FROM pembiayaan_kunjungan WHERE pendamping_id = :pendamping_id", [
            'pendamping_id' => $pendampingID,
        ]);
        return $query;
    }

    public function scopeAgronomisLaporanPenugasanKunjunganRiwayat($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_kunjungan_id, a.tanggal_kunjungan, a.latitude_kunjungan, a.longitude_kunjungan, a.analisis_penyebab, a.luas_terdampak, a.penyakit, a.hama, a.bencana, a.hasil_pengamatan, a.rekomendasi, a.rekomendasi_st, a.penilaian_status, a.penilaian_petani, a.penilaian_kesan FROM pembiayaan_kunjungan a  WHERE a.pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetSaprodiTambahanPenugasan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_rab_tambahan_id, a.pembiayaan_kunjungan_id,  b.nama_item, a.jumlah, b.satuan FROM pembiayaan_rab_tambahan a JOIN master_item_rab b ON a.item_rab_id = b.item_rab_id WHERE a.pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetImgLaporanPenugasan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT hasil_path, hasil_img FROM pembiayaan_kunjungan_hasil  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetDetailPermintaan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_kunjungan_id, a.latitude_awal, a.longitude_awal, a.pembiayaan_id, c.petani_id, a.catatan_kunjungan, a.tanggal_dibuat, a.status_kunjungan, b.lahan_kd, b.nama_pemilik, b.luas_lahan, b.varietas_nama, c.nama_lengkap AS nama_petani, c.nama_kelompok_tani, b.alamat,b.rt,b.rw, e.kel_nama, f.kec_nama, g.kab_nama , h.mdb_name AS acc_officer FROM pembiayaan_kunjungan a JOIN pembiayaan_lahan b ON a.pembiayaan_id = b.pembiayaan_id AND a.lahan_id = b.lahan_id JOIN pembiayaan_petani c ON a.pembiayaan_id = c.pembiayaan_id JOIN wilayah_kelurahan e ON b.kel_id = e.kel_id JOIN wilayah_kecamatan f ON b.kec_id = f.kec_id JOIN wilayah_kabupaten g ON b.kab_id = g.kab_id JOIN pembiayaan h ON a.pembiayaan_id = h.pembiayaan_id  WHERE a.pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetCheckpointPenugasan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT latitude_kunjungan, longitude_kunjungan, tanggal_kunjungan FROM pembiayaan_kunjungan  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopeAgronomisGetImgPenugasan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT file_path, file_img FROM pembiayaan_kunjungan_file  WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }
}
