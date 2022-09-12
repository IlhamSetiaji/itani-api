<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Petani extends Model
{
    use HasFactory;

    public function scopePetaniGetRealisasiKegiatan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(a.`pencairan_st`='yes' AND e.item_rab_id != '32' AND e.group_rab_id!='1',(a.jumlah*b.harga),0)) +
        SUM(IF(a.`pembayaran_st`='yes' AND e.group_rab_id=1,(a.jumlah*b.harga),0)) + IF(f.nilai_tambahan IS NOT NULL, f.nilai_tambahan,0)
        AS pembiayaan_terpakai,
        e.`nama_item`,a.`jumlah`,b.`harga`,a.`pencairan_date`,
        SUM(IF(a.`pembayaran_st`='yes' AND e.group_rab_id=1,(a.jumlah*b.harga),0)) AS saprodi,
        SUM(IF(a.`pencairan_st`='yes' AND b.item_rab_id !=1 AND e.group_rab_id!=1,(a.jumlah*b.harga),0)) AS tenaga,
        IF(f.nilai_tambahan IS NOT NULL, f.nilai_tambahan,0) AS nilai_tambahan
        FROM pembiayaan_rab_mingguan a
        LEFT JOIN pembiayaan_rab b ON b.pembiayaan_rab_id = a.pembiayaan_rab_id
        LEFT JOIN pembiayaan c ON c.pembiayaan_id = b.pembiayaan_id
        LEFT JOIN master_item_fee d ON d.item_rab_id = b.item_rab_id
        LEFT JOIN master_item_rab e ON e.`item_rab_id` = b.`item_rab_id`
        LEFT JOIN pembiayaan_rab_tambahan f ON f.pembiayaan_id = c.pembiayaan_id
        WHERE c.pembiayaan_id = :pembiayaan_id", [
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetTotalPendapatanBersihPetani($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(pendapatan_bersih_petani) AS total_pendapatan_petani FROM panen_penimbangan_hasil
        WHERE petani_id = :petani_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetTotalSaldoRekening($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(jenis_transaksi='pencairan mingguan', nilai_transaksi, 0)) -
        SUM(IF(jenis_transaksi='saprodi', nilai_transaksi, 0)) -
        SUM(IF(jenis_transaksi='fee distribusi', nilai_transaksi, 0)) +
        SUM(IF(jenis_transaksi='pencairan tambahan', nilai_transaksi, 0)) +
        SUM(IF(jenis_transaksi='gkp', nilai_transaksi, 0)) -
        SUM(IF(jenis_transaksi='pelunasan', nilai_transaksi, 0)) -
        SUM(IF(jenis_transaksi='jasa angkut', nilai_transaksi, 0)) -
        SUM(IF(jenis_transaksi='penarikan updah', nilai_transaksi, 0))
        AS total_saldo_rekening FROM data_transaksi
        WHERE petani_id=:petani_id AND pembiayaan_id=:pembiayaan_id", [
            'petani_id' => $petaniID,
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetDataTransaksiPembiayaan($query, $petaniID, $bulan, $tahun)
    {
        $query = DB::connection('mysql')->select("SELECT * FROM
        (SELECT petani_id,jenis_transaksi,tanggal_transaksi,'Pencairan Minggu Ini' AS desc_transaksi,SUM(nilai_transaksi) AS nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='pencairan mingguan'
        GROUP BY YEAR(tanggal_transaksi),MONTH(tanggal_transaksi),DAY(tanggal_transaksi)
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='saprodi'
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='jasa angkut'
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='fee distribusi'
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='pencairan tambahan'
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='pelunasan'
        UNION
        SELECT petani_id,jenis_transaksi,tanggal_transaksi,desc_transaksi,nilai_transaksi FROM data_transaksi
        WHERE jenis_transaksi='gkp'
        )rst WHERE rst.petani_id=:petani_id AND MONTH(rst.tanggal_transaksi)=:bulan AND YEAR(rst.tanggal_transaksi)=:tahun
        ORDER BY tanggal_transaksi DESC", [
            'petani_id' => $petaniID,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
        return $query;
    }

    public function scopePetaniGetImgHasilRekomendasi($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT file_path, file_name, jenis_foto FROM pembiayaan_foto_rekomendasi
        WHERE pembiayaan_kunjungan_id = :pembiayaan_kunjungan_id", [
            'pembiayaan_kunjungan_id' => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopePetaniGetSaldoRekeningPetani($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(a.pencairan_st = 'yes',(a.`jumlah`*b.`harga`),0)) -
        SUM(IF(a.pembayaran_st = 'yes' AND e.group_rab_id = 1,(a.`jumlah`*b.`harga`),0)) +
        SUM(IF(a.pencairan_st = 'yes' AND e.group_rab_id != 1,(a.`jumlah`*b.`harga`),0)) -
        SUM(IF(c.pembiayaan_st = 'finish' AND a.pembayaran_st='yes',(a.jumlah * b.harga),0)) -
        (IF(f.konfirmasi_date IS NOT NULL,f.fee_distribusi,0)) +
        (IF(f.konfirmasi_date IS NOT NULL,f.total_pembelian,0)) -
        SUM(IF(a.pembayaran_st = 'yes' AND e.group_rab_id = 1,(((a.`jumlah`*b.`harga`)/100)*g.persentase_item_fee),0))
        AS total_saldo_rekening,
        SUM(IF(a.pencairan_st = 'yes',(a.`jumlah`*b.`harga`),0)) AS pencairan,
        SUM(IF(a.pembayaran_st = 'yes' AND e.group_rab_id = 1,(a.`jumlah`*b.`harga`),0)) AS saprodi,
        SUM(IF(a.pencairan_st = 'yes' AND e.group_rab_id != 1,(a.`jumlah`*b.`harga`),0)) AS upah,-
        SUM(IF(c.pembiayaan_st = 'finish' AND a.pembayaran_st='yes',(a.jumlah * b.harga),0)) AS pelunasan,
        (IF(f.konfirmasi_date IS NOT NULL,f.fee_distribusi,0)) AS jasa_angkut,
        (IF(f.konfirmasi_date IS NOT NULL,f.total_pembelian,0)) AS gkp,
        SUM(IF(a.pembayaran_st = 'yes' AND e.group_rab_id = 1,(((a.`jumlah`*b.`harga`)/100)*g.persentase_item_fee),0)) AS fee_saprodi
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.`pembiayaan_rab_id` = a.`pembiayaan_rab_id`
        JOIN pembiayaan c ON c.`pembiayaan_id` = b.`pembiayaan_id`
        JOIN pembiayaan_petani d ON d.`pembiayaan_id` =c.`pembiayaan_id`
        JOIN master_item_rab e ON e.`item_rab_id` = b.item_rab_id
        LEFT JOIN panen_penimbangan_hasil f ON f.`petani_id` = d.`petani_id`
        LEFT JOIN master_item_fee g ON g.`item_rab_id` = e.`item_rab_id`
        WHERE d.petani_id=:petani_id AND c.`pembiayaan_id`=:pembiayaan_id", [
            'petani_id' => $petaniID,
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetTransaksiSaldoPembiayaan($query, $tahun, $bulan, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT * FROM
        (SELECT c.`pembiayaan_id`,d.petani_id AS petani_id, a.pembayaran_date AS tanggal ,
        e.nama_item AS nama_item, ROUND(b.`harga`*a.`jumlah`) AS total,
        e.`group_rab_id`AS group_rab_id, 'Saprodi' AS jenis
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.`pembiayaan_rab_id` = b.`pembiayaan_rab_id`
        JOIN pembiayaan c ON b.`pembiayaan_id` = c.`pembiayaan_id`
        JOIN pembiayaan_petani d ON c.`pembiayaan_id` = d.`pembiayaan_id`
        JOIN master_item_rab e ON e.`item_rab_id` = b.`item_rab_id`
        LEFT JOIN panen_penimbangan_hasil f ON f.`petani_id` = d.`petani_id`
        WHERE c.`pembiayaan_id` = :pembiayaan_id
        UNION
        SELECT h.pembiayaan_id,k.`petani_id` AS petani_id, i.pembayaran_date AS tanggal,
        'Pembiayaan' AS nama_item ,
        SUM(IF(i.`pembayaran_st`='yes',(i.`jumlah`*g.`harga`),0)) AS total,
        '1' AS group_rab_id ,
        'Pelunasan' AS jenis
        FROM pembiayaan h
        JOIN pembiayaan_rab g ON g.`pembiayaan_id` = h.`pembiayaan_id`
        JOIN pembiayaan_rab_mingguan i ON i.`pembiayaan_rab_id` = g.`pembiayaan_rab_id`
        JOIN pembiayaan_petani k ON k.`pembiayaan_id` = h.`pembiayaan_id`
        WHERE h.`pembiayaan_id` = :pembiayaan_id AND h.pembiayaan_st='finish'
        UNION
        SELECT m.pembiayaan_id,l.petani_id,l.mutasi_date AS tanggal, 'GKP' AS nama_item,
        l.total_pembelian AS total, '1' AS group_rab_id ,
        'Pembelian' AS jenis
        FROM panen_penimbangan_hasil l
        JOIN pembiayaan_petani m ON m.petani_id = l.petani_id
        WHERE m.`pembiayaan_id` = :pembiayaan_id
        UNION
        SELECT o.pembiayaan_id, o.petani_id,n.mutasi_date AS tanggal, 'Angkut' AS nama_item,
        n.fee_distribusi AS total, '1' AS group_rab_id,
        'Jasa' AS jenis
        FROM panen_penimbangan_hasil n
        JOIN pembiayaan_petani o ON o.petani_id = n.petani_id
        WHERE o.`pembiayaan_id` = :pembiayaan_id
        UNION
        SELECT r.pembiayaan_id,s.petani_id AS petani_id,p.pencairan_date AS tanggal, 'Mingguan' AS nama_item,
        SUM((q.harga*p.jumlah)) AS total, '1' AS group_rab_id,
        'Pencairan' AS jenis
        FROM pembiayaan_rab_mingguan p
        JOIN pembiayaan_rab q ON q.`pembiayaan_rab_id` = p.`pembiayaan_rab_id`
        JOIN pembiayaan r ON r.`pembiayaan_id` = q.`pembiayaan_id`
        JOIN pembiayaan_petani s ON s.`pembiayaan_id` = q.`pembiayaan_id`
        JOIN master_item_rab t ON t.`item_rab_id` = q.item_rab_id
        WHERE r.`pembiayaan_id` = :pembiayaan_id AND p.`pencairan_date` IS NOT NULL AND p.pencairan_st='yes'
        GROUP BY YEAR(p.pencairan_date), MONTH(p.pencairan_date), DAY(p.pencairan_date)
        UNION
        SELECT w.`pembiayaan_id`,z.petani_id AS petani_id, u.pembayaran_date AS tanggal ,
        ab.nama_item AS nama_item, ROUND(((v.`harga`*u.`jumlah`)/100)*10) AS total,
        ab.`group_rab_id`AS group_rab_id,
        'Fee Distribusi Saprodi' AS jenis FROM pembiayaan_rab_mingguan u
        JOIN pembiayaan_rab v ON u.`pembiayaan_rab_id` = v.`pembiayaan_rab_id`
        JOIN pembiayaan w ON v.`pembiayaan_id` = w.`pembiayaan_id`
        JOIN pembiayaan_petani z ON w.`pembiayaan_id` = z.`pembiayaan_id`
        JOIN master_item_rab ab ON ab.`item_rab_id` = v.`item_rab_id`
        WHERE w.`pembiayaan_id` = :pembiayaan_id) rst
        WHERE group_rab_id = '1' AND rst.petani_id=:petani_id
        AND rst.pembiayaan_id= :pembiayaan_id
        AND MONTH(rst.tanggal) = :bulan
        AND YEAR(rst.tanggal) = :tahun
        ORDER BY tanggal DESC", [
            'petani_id' => $petaniID,
            'pembiayaan_id' => $pembiayaanID,
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);
        return $query;
    }

    public function scopePetaniGetRekeningPelunasanPembiayaan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(c.`pencairan_st`='yes',(c.`jumlah`*b.`harga`),0)) AS pelunasan,a.mdd FROM pembiayaan a
        JOIN pembiayaan_rab b ON b.`pembiayaan_id` = a.`pembiayaan_id`
        JOIN pembiayaan_rab_mingguan c ON c.`pembiayaan_rab_id` = b.`pembiayaan_rab_id`
        WHERE a.pembiayaan_id=:pembiayaan_id
        AND a.`pembiayaan_st` = 'finish';", [
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetFotoKegiatan($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT * FROM `pembiayaan_foto_kegiatan_petani`
        WHERE pembiayaan_id=:pembiayaan_id AND proses_tanam_id=:proses_tanam_id", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID
        ]);
        return $query;
    }

    public function scopePetaniGetSaldoPetani($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(rst.upah_petani) +
        SUM(IF(rst.total_pendapatan_bersih IS NOT NULL,
        (rst.total_pendapatan_bersih),0))
        AS saldo_petani,rst.* FROM(
        SELECT c.`nilai_pembiayaan`, (e.pendapatan_bersih_petani) AS total_pendapatan_bersih, a.jumlah,b.harga,a.pencairan_st,
        SUM(IF(a.pencairan_st = 'yes',(a.jumlah*b.harga),0)) AS upah_petani
        FROM pembiayaan_rab_mingguan a
        LEFT JOIN pembiayaan_rab b ON b.pembiayaan_rab_id = a.pembiayaan_rab_id
        LEFT JOIN pembiayaan c ON c.pembiayaan_id = b.pembiayaan_id
        LEFT JOIN master_item_fee i ON i.item_rab_id = b.item_rab_id
        LEFT JOIN master_item_rab j ON j.item_rab_id = b.item_rab_id
        LEFT JOIN pembiayaan_petani d ON d.pembiayaan_id = c.pembiayaan_id
        LEFT JOIN panen_penimbangan_hasil e ON e.`petani_id`= d.`petani_id`
        WHERE d.petani_id = :petani_id AND  j.group_rab_id != '1' AND j.`item_rab_id` != '32'
        GROUP BY e.`penimbangan_hasil_id`,c.pembiayaan_id)rst;", [
            "petani_id" => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetSaldoPencairanMingguIni($query, $tahun, $bulan, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(b.`harga`*a.`jumlah`) AS total,
        a.pencairan_date
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.`pembiayaan_rab_id` = b.`pembiayaan_rab_id`
        JOIN pembiayaan c ON b.`pembiayaan_id` = c.`pembiayaan_id`
        JOIN pembiayaan_petani d ON c.`pembiayaan_id` = d.`pembiayaan_id`
        JOIN master_item_rab e ON e.`item_rab_id` = b.`item_rab_id`
        WHERE d.petani_id=:petani_id AND a.pencairan_st='yes'
        AND YEAR(a.`pencairan_date`) = :tahun
        AND MONTH(a.`pencairan_date`)= :bulan;", [
            "petani_id" => $petaniID,
            "tahun" => $tahun,
            "bulan" => $bulan,
        ]);
        return $query;
    }

    public function scopePetaniGetTransaksiSaldoFeeDistribusi($query, $tahun, $bulan, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT * FROM panen_penimbangan_hasil
        WHERE petani_id = :petani_id
        AND YEAR(`konfirmasi_date`) = :tahun
        AND MONTH(konfirmasi_date)=:bulan", [
            "petani_id" => $petaniID,
            "tahun" => $tahun,
            "bulan" => $bulan,
        ]);
        return $query;
    }

    public function scopePetaniGetPengambilanSaprodiTambahan($query, $pembiayaanRabTambahanID)
    {
        $query = DB::connection('mysql')->select("SELECT
        a.item_rab_id,
        d.nama_item,
        a.jumlah,
        d.satuan
        FROM pembiayaan_rab_tambahan a
        JOIN pembiayaan_kunjungan b ON a.pembiayaan_kunjungan_id = b.pembiayaan_kunjungan_id
        JOIN pembiayaan c ON c.pembiayaan_id = b.pembiayaan_id
        JOIN master_item_rab d ON a.item_rab_id = d.item_rab_id
        where a.pembiayaan_rab_tambahan_id =:pembiayaan_rab_tambahan_id", [
            "pembiayaan_rab_tambahan_id" => $pembiayaanRabTambahanID,
        ]);
        return $query;
    }

    public function scopePetaniPengambilanSaprodiTambahanAll($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT
        a.item_rab_id,
        d.nama_item,
        a.jumlah,
        d.satuan
        FROM pembiayaan_rab_tambahan a
        JOIN pembiayaan_kunjungan b ON a.pembiayaan_kunjungan_id = b.pembiayaan_kunjungan_id
        JOIN pembiayaan c ON c.pembiayaan_id = b.pembiayaan_id
        JOIN master_item_rab d ON a.item_rab_id = d.item_rab_id
        where a.pembiayaan_rab_tambahan_id =:pembiayaan_rab_tambahan_id", [
            "petani_id" => $petaniID,
            "pembiayaan_id" => $pembiayaanID
        ]);
        return $query;
    }

    public function scopePetaniGetPhotoKunjunganLahan($query, $pembiayaanKunjunganID)
    {
        $query = DB::connection('mysql')->select("SELECT * FROM `pembiayaan_kunjungan_hasil`
        WHERE pembiayaan_kunjungan_id=:pembiayaan_kunjungan_id", [
            "pembiayaan_kunjungan_id" => $pembiayaanKunjunganID,
        ]);
        return $query;
    }

    public function scopePetaniGetPengajuanPembiayaan($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT a.nilai_pembiayaan,b.pengajuan_st,
        a.luas_lahan,b.created_at,a.pengajuan_id,c.alamat ,a.pembiayaan_id,c.`lahan_id`, d.nama_lengkap, d.created_at
        FROM pembiayaan a
        JOIN task_pengajuan b ON a.pengajuan_id=b.pengajuan_id
        JOIN pembiayaan_lahan c ON a.pembiayaan_id = c.pembiayaan_id
        JOIN pembiayaan_petani d ON a.pembiayaan_id = d.pembiayaan_id
        WHERE d.petani_id=:petani_id
        GROUP BY pengajuan_id", [
            "petani_id" => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetDetailPengajuanPembiayaanRAB($query, $pengajuanID)
    {
        $query = DB::connection('mysql')->select("SELECT b.nama_item,d.jumlah,b.satuan,
        (a.harga*d.jumlah) as harga FROM pembiayaan_rab a
        JOIN pembiayaan_rab_mingguan d ON d.pembiayaan_rab_id = a.pembiayaan_rab_id
        JOIN master_item_rab b ON b.item_rab_id = a.item_rab_id
        JOIN `pembiayaan` c ON a.`pembiayaan_id` = c.`pembiayaan_id`
        WHERE c.`pengajuan_id`=:pengajuan_id", [
            "pengajuan_id" => $pengajuanID,
        ]);
        return $query;
    }

    public function scopePetaniGetDetailPengajuanPembiayaan($query, $pengajuanID, $lahanID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pengajuan_id,a.nilai_pembiayaan,c.luas_lahan,a.created_at,b.pengajuan_st,
        d.nama_lengkap,d.nik,d.petani_kd,d.tempat_lahir,d.tanggal_lahir,d.status_perkawinan,
        e.`kel_nama` AS kelurahan_lahan, f.`kec_nama` AS kecamatan_lahan,g.`kab_nama` AS kabupaten_lahan,
        c.nama_pemilik,c.lahan_id,c.alamat,c.rt,c.rw,c.`kel_id`,c.`kec_id`,c.`kab_id`,c.pembiayaan_lahan_id,
        d.nama_pasangan,d.jenis_kelamin_pasangan,d.tanggungan_anak,c.`lahan_id`,
        c.`nama_pemilik`,a.`mdb_name`,CONVERT(c.luas_lahan, CHAR) AS luas,d.`petani_id`,
        c.lahan_id
        FROM pembiayaan a
        JOIN task_pengajuan b ON a.pengajuan_id=b.pengajuan_id
        JOIN pembiayaan_lahan c ON a.pembiayaan_id = c.pembiayaan_id
        JOIN pembiayaan_petani d ON c.pembiayaan_id = d.pembiayaan_id
        JOIN wilayah_kelurahan e ON e.`kel_id` = c.`kel_id`
        JOIN wilayah_kecamatan f ON f.`kec_id` = c.`kec_id`
        JOIN wilayah_kabupaten g ON g.`kab_id` = c.`kab_id`
        WHERE a.`pengajuan_id`=:pengajuan_id AND c.`lahan_id`=:lahan_id", [
            "pengajuan_id" => $pengajuanID,
            "lahan_id" => $lahanID
        ]);
        return $query;
    }

    public function scopePetaniGetGeoJsonLahan($query, $pembiayaanLahanID)
    {
        $query = DB::connection('mysql')->select("SELECT ST_ASGEOJSON(geom) AS geojson,
        st_x(koordinat) AS longitude, st_y(koordinat) AS latitude from
        pembiayaan_lahan a
        where a.pembiayaan_lahan_id=:pembiayaan_lahan_id", [
            "pembiayaan_lahan_id" => $pembiayaanLahanID,
        ]);
        return $query;
    }

    public function scopePetaniGetTotalPembiayaan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT IF(pembiayaan_id IS NOT NULL,COUNT(pembiayaan_id),0) AS
        totalPembiayaanAktif FROM pembiayaan
        WHERE `active_st`='yes' AND pembiayaan_id=:pembiayaan_id", [
            "pembiayaan_id" => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetPengambilanSaprodi($query, $pembiayaanRabMingguanID)
    {
        $query = DB::connection('mysql')->select("SELECT d.proses_tanam_id,
        d.pembiayaan_rab_mingguan_id,
        c.item_rab_id,
        e.nama_item,
        c.jumlah,
        e.satuan
        from pembiayaan_petani a
        JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        JOIN pembiayaan_rab c on b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_rab_mingguan d on c.pembiayaan_rab_id=d.pembiayaan_rab_id
        JOIN master_item_rab e on c.item_rab_id=e.item_rab_id
        JOIN master_proses_tanam f on d.proses_tanam_id=f.proses_tanam_id
        JOIN pembiayaan_lahan g on a.pembiayaan_id = g.pembiayaan_id
        JOIN pembiayaan h on a.pembiayaan_id = h.pembiayaan_id
        JOIN master_kios i on h.kios_id = i.kios_id
        where d.pembiayaan_rab_mingguan_id=:pembiayaan_rab_mingguan_id
        order by d.proses_tanam_id asc", [
            "pembiayaan_rab_mingguan_id" => $pembiayaanRabMingguanID,
        ]);
        return $query;
    }

    public function scopeAoGetJumlahKesiapanTenaga($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT
        SUM(IF(a.kesiapan_tenaga_kerja_st='yes',1,0)) AS kesiapan,
        SUM(IF(a.kesiapan_tenaga_kerja_st IS NOT NULL,1,0)) AS total,
        SUM(a.jumlah*b.harga) AS bayaran FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.proses_tanam_id = c.proses_tanam_id
        JOIN master_item_rab d ON b.item_rab_id = d.item_rab_id
        WHERE group_rab_id!=1 AND b.`pembiayaan_id`=:pembiayaan_id
        AND d.`item_rab_id`!='32'AND
        c.`proses_tanam_id`=:proses_tanam_id", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID,
        ]);
        return $query;
    }

    public function scopeAoGetKesiapanTenaga($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_rab_mingguan_id,
        a.pembiayaan_rab_id,d.`item_rab_id`,d.satuan,
        b.`jumlah`,d.`nama_item`,(b.harga*a.jumlah) as harga,a.kesiapan_tenaga_kerja_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.proses_tanam_id = c.proses_tanam_id
        JOIN master_item_rab d ON b.item_rab_id = d.item_rab_id
        WHERE group_rab_id!=1 AND b.`pembiayaan_id`=:pembiayaan_id AND
        d.`item_rab_id`!='32'AND
        c.`proses_tanam_id`=:proses_tanam_id", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID,
        ]);
        return $query;
    }

    public function scopeAoGetKesiapanSaprodi($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT a.`jumlah`,d.`nama_item`,
        d.`satuan`,
        (a.jumlah*b.`harga`) AS harga,a.kesiapan_stok_st,
        e.`kios_nama`,e.`kios_id`,
        f.`alamat`,
        a.`kesiapan_stok_date`,
        a.kesiapan_stok_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.`proses_tanam_id` = c.`proses_tanam_id`
        JOIN `master_item_rab` d ON b.`item_rab_id` = d.`item_rab_id`
        JOIN pembiayaan e ON e.`pembiayaan_id` = b.`pembiayaan_id`
        JOIN `master_kios` f ON e.`kios_id` = f.`kios_id`
        WHERE d.`group_rab_id`=1 AND a.`proses_tanam_id`=:proses_tanam_id
        AND b.`pembiayaan_id`=:pembiayaan_id AND d.`item_rab_id` != 32 AND d.`item_rab_id` != 33", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID,
        ]);
        return $query;
    }

    public function scopeAoGetJumlahMonitoringPelaksanaan($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(hsl.`rencana_kegiatan_st` = 'done',1,0)) AS kegiatan_selesai,
        SUM(IF(hsl.`rencana_kegiatan_st`='process',1,0)) AS kegiatan_on_progress,
        SUM(IF(hsl.`rencana_kegiatan_st`='waiting',1,0)) AS kegiatan_on_belum,
        SUM(IF(hsl.`rencana_kegiatan_st` IS NOT NULL,1,0)) AS jumlah
        FROM (SELECT a.`rencana_kegiatan_st`
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`rencana_kegiatan_st` IS NOT NULL and c.subcluster_id=:subcluster_id
        GROUP BY a.proses_tanam_id) hsl;", [
            "subcluster_id" => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoGetRencanaKegiatan($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st,
        a.proses_tanam_id,d.petani_id,d.petani_kd,e.lahan_id,
        e.luas_lahan,f.proses_tanam_nama,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`,c.`pengajuan_id`
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`rencana_kegiatan_st` IS NOT NULL and c.subcluster_id =:subcluster_id
        GROUP BY a.proses_tanam_id", [
            "subcluster_id" => $subClusterID,
        ]);
        return $query;
    }

    public function scopepetaniPengambilanSaprodiAll($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT d.proses_tanam_id,
        f.proses_tanam_nama,
        f.proses_tanam_desc,
        e.nama_item,
        e.item_rab_id,
        d.jumlah,
        a.petani_id,
        a.pembiayaan_id,
        d.periode_kegiatan_start,
        d.periode_kegiatan_end,
        d.pembiayaan_rab_mingguan_id,
        d.kesiapan_kegiatan_st,
        d.pengambilan_st,
        g.lahan_id,
        i.kios_nama,
        i.alamat,
        d.kesiapan_stok_st
        from pembiayaan_petani a
        -- JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        JOIN pembiayaan_rab c on a.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_rab_mingguan d on c.pembiayaan_rab_id=d.pembiayaan_rab_id
        JOIN master_item_rab e on c.item_rab_id=e.item_rab_id
        JOIN master_proses_tanam f on d.proses_tanam_id=f.proses_tanam_id
        JOIN pembiayaan_lahan g on a.pembiayaan_id = g.pembiayaan_id
        JOIN pembiayaan h on a.pembiayaan_id = h.pembiayaan_id
        JOIN master_kios i on h.kios_id = i.kios_id
        where a.petani_id=:petani_id and a.pembiayaan_id=:pembiayaan_id
        group by f.proses_tanam_desc
        order by d.proses_tanam_id asc", [
            "petani_id" => $petaniID,
            "pembiayaan_id" => $pembiayaanID
        ]);
        return $query;
    }

    public function scopeGetItemRabByPetaniPembiayaan($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT d.proses_tanam_id,
        f.proses_tanam_nama,
        f.proses_tanam_desc,
        e.nama_item,
        e.item_rab_id,
        d.jumlah,
        a.petani_id,
        a.pembiayaan_id,
        d.periode_kegiatan_start,
        d.periode_kegiatan_end,
        d.pembiayaan_rab_mingguan_id,
        d.kesiapan_kegiatan_st,
        d.pengambilan_st,
        g.lahan_id,
        i.kios_nama,
        i.alamat,
        d.kesiapan_stok_st
        from pembiayaan_petani a
        -- JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        JOIN pembiayaan_rab c on a.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_rab_mingguan d on c.pembiayaan_rab_id=d.pembiayaan_rab_id
        JOIN master_item_rab e on c.item_rab_id=e.item_rab_id
        JOIN master_proses_tanam f on d.proses_tanam_id=f.proses_tanam_id
        JOIN pembiayaan_lahan g on a.pembiayaan_id = g.pembiayaan_id
        JOIN pembiayaan h on a.pembiayaan_id = h.pembiayaan_id
        JOIN master_kios i on h.kios_id = i.kios_id
        where a.petani_id=:petani_id and a.pembiayaan_id=:pembiayaan_id and e.group_rab_id = '1'
        -- group by f.proses_tanam_desc
        order by d.proses_tanam_id asc", [
            "petani_id" => $petaniID,
            "pembiayaan_id" => $pembiayaanID
        ]);
        return $query;
    }

    public function scopeGetItemRabByGrub2PetaniPembiayaan($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT d.proses_tanam_id,
        e.group_rab_id,
        f.proses_tanam_nama,
        f.proses_tanam_desc,
        e.nama_item,
        e.item_rab_id,
        d.jumlah,
        a.petani_id,
        a.pembiayaan_id,
        d.periode_kegiatan_start,
        d.periode_kegiatan_end,
        d.pembiayaan_rab_mingguan_id,
        d.kesiapan_kegiatan_st,
        d.pengambilan_st,
        g.lahan_id,
        i.kios_nama,
        i.alamat,
        d.kesiapan_stok_st
        FROM pembiayaan_petani a
        -- JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        JOIN pembiayaan_rab c ON a.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_rab_mingguan d ON c.pembiayaan_rab_id=d.pembiayaan_rab_id
        JOIN master_item_rab e ON c.item_rab_id=e.item_rab_id
        JOIN master_proses_tanam f ON d.proses_tanam_id=f.proses_tanam_id
        JOIN pembiayaan_lahan g ON a.pembiayaan_id = g.pembiayaan_id
        JOIN pembiayaan h ON a.pembiayaan_id = h.pembiayaan_id
        JOIN master_kios i ON h.kios_id = i.kios_id
        WHERE a.petani_id=:petani_id AND a.pembiayaan_id=:pembiayaan_id AND  e.`group_rab_id` = '2'

        -- group by f.proses_tanam_desc
        order by d.proses_tanam_id asc", [
            "petani_id" => $petaniID,
            "pembiayaan_id" => $pembiayaanID
        ]);
        return $query;
    }

    public function scopeGetItemRabByGrub3PetaniPembiayaan($query, $petaniID, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT d.proses_tanam_id,
        e.group_rab_id,
        f.proses_tanam_nama,
        f.proses_tanam_desc,
        e.nama_item,
        e.item_rab_id,
        d.jumlah,
        a.petani_id,
        a.pembiayaan_id,
        d.periode_kegiatan_start,
        d.periode_kegiatan_end,
        d.pembiayaan_rab_mingguan_id,
        d.kesiapan_kegiatan_st,
        d.pengambilan_st,
        g.lahan_id,
        i.kios_nama,
        i.alamat,
        d.kesiapan_stok_st
        FROM pembiayaan_petani a
        -- JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        JOIN pembiayaan_rab c ON a.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_rab_mingguan d ON c.pembiayaan_rab_id=d.pembiayaan_rab_id
        JOIN master_item_rab e ON c.item_rab_id=e.item_rab_id
        JOIN master_proses_tanam f ON d.proses_tanam_id=f.proses_tanam_id
        JOIN pembiayaan_lahan g ON a.pembiayaan_id = g.pembiayaan_id
        JOIN pembiayaan h ON a.pembiayaan_id = h.pembiayaan_id
        JOIN master_kios i ON h.kios_id = i.kios_id
        WHERE a.petani_id=:petani_id AND a.pembiayaan_id=:pembiayaan_id AND  e.`group_rab_id` = '3'

        -- group by f.proses_tanam_desc
        order by d.proses_tanam_id asc", [
            "petani_id" => $petaniID,
            "pembiayaan_id" => $pembiayaanID
        ]);
        return $query;
    }

    public function scopePetaniGetDataPetani($query, $petaniID)
    {
        $query = DB::connection('mysql_second')->select("SELECT pp.*, crp.* FROM com_user_petani crp JOIN pendataan_petani pp ON crp.`petani_id` = pp.`petani_id`
        WHERE crp.petani_id =:petani_id", [
            "petani_id" => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetPembiayaan($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_id,
        round(sum(luas_lahan/10000),5) as totalLuasLahan,
        (a.nilai_pembiayaan) as totalNilaiPembiayaan,
        COUNT(active_st='yes') as totalPembiayaanAktif
        FROM pembiayaan a
        JOIN pembiayaan_petani b on a.pembiayaan_id=b.pembiayaan_id
        where b.petani_id=:petani_id and active_st='yes'", [
            "petani_id" => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetPembiayaanAktif($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT a.pembiayaan_id,
        b.varietas_nama,
        a.nilai_pembiayaan,
        CONVERT(b.luas_lahan, char)as luas_lahan,
        b.alamat
        from pembiayaan a
        JOIN pembiayaan_lahan b on
        a.pembiayaan_id = b.pembiayaan_id
        WHERE a.pembiayaan_id= :pembiayaan_id", [
            "pembiayaan_id" => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetKunjunganLahan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT a.*,
        b.nama_lengkap FROM pembiayaan_kunjungan a
        LEFT JOIN ipangan_sf_v1_db_demo.pendamping b ON a.pendamping_id=b.pendamping_id
        WHERE pembiayaan_id=:pembiayaan_id", [
            "pembiayaan_id" => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopePetaniGetLahan($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT a.lahan_id,
        a.nama_pemilik,
        b.nama_kelompok_tani,
        a.blok_lahan_id,
        a.alamat,
        a.luas_lahan
        FROM pembiayaan_lahan a
        JOIN pembiayaan_petani b ON a.pembiayaan_id = b.pembiayaan_id
        WHERE b.petani_id=:petani_id
        GROUP BY lahan_id", [
            "petani_id" => $petaniID,
        ]);
        return $query;
    }

    public function scopePetaniGetJadwal($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT DISTINCT(b.proses_tanam_id),b.pembiayaan_rab_mingguan_id,
                c.proses_tanam_nama,b.periode_kegiatan_start,b.periode_kegiatan_end, d.`file_path`,d.`file_name`,
                b.kesiapan_kegiatan_st,b.rencana_kegiatan_st
                FROM pembiayaan_rab a
                JOIN pembiayaan_rab_mingguan b ON a.pembiayaan_rab_id=b.pembiayaan_rab_id
                JOIN master_proses_tanam c ON b.proses_tanam_id=c.proses_tanam_id
                LEFT JOIN `pembiayaan_foto_kegiatan_petani` d ON d.pembiayaan_id=a.pembiayaan_id
                AND d.`proses_tanam_id`=b.`proses_tanam_id`
                WHERE a.pembiayaan_id=:pembiayaan_id
                GROUP BY b.proses_tanam_id ASC", [

            "pembiayaan_id" => $pembiayaanID,
        ]);
        return $query;
    }



    public function scopePetaniGetHasilPanen($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT a.berat_gkp
            AS berat_timbangan_lahan,
            a.mdd AS waktu_timbangan_lahan,
            e.berat_hasil_timbang AS berat_timbang_pabrik,
            e.mdd AS waktu_timbang_pabrik,
            f.supir_id,
            f.truk_nopol,
            g.pabrik_id, e.potongan_pajak,e.potongan_retribusi,
            h.alamat AS alamat_pabrik, e.`pembelian_bersih`,e.`pendapatan_bersih_petani`,
            e.fee_distribusi AS potongan_jasa_truk,
            e.`total_pembelian` AS pendapatan_kotor
            FROM panen_pengangkutan_hasil a
            LEFT JOIN pembiayaan_petani b ON a.petani_id=b.petani_id
            LEFT JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
            LEFT JOIN pembiayaan_lahan d ON d.lahan_id=a.lahan_id
            LEFT JOIN panen_penimbangan_hasil e ON e.petani_id=a.petani_id
            LEFT JOIN panen_pengangkutan f ON f.pengangkutan_id=a.pengangkutan_id
            LEFT JOIN panen_penimbangan g ON g.penimbangan_id=e.penimbangan_id
            LEFT JOIN master_pabrik h ON g.pabrik_id=h.pabrik_id
            WHERE b.pembiayaan_id=:pembiayaan_id AND c.active_st='yes'
            GROUP BY e.penimbangan_hasil_id", [

            "pembiayaan_id" => $pembiayaanID,
        ]);
        return $query;
    }
}
