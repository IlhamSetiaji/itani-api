<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountOfficer extends Model
{
    use HasFactory;

    public function scopeAoGetMonitoringPelaksanaan($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st,
        a.proses_tanam_id,d.petani_id,d.petani_kd,e.lahan_id,f.proses_tanam_nama,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`,a.`pembayaran_st`,
        a.persetujuan_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`pembayaran_st` IS NOT NULL AND petani_id=:petani_id
        GROUP BY b.pembiayaan_id,a.proses_tanam_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopeAoGetDetailKiosBySubclusterId($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT a.kios_id, b.`kios_nama`,b.`alamat`,b.`nama_pemilik` FROM pembiayaan a
        JOIN master_kios b ON a.`kios_id` = b.`kios_id`
        WHERE a.`subcluster_id`=:subcluster_id
        GROUP BY a.kios_id", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoGetKiosbyClusterAndSubClusterId($query, $clusterID, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT COUNT(kios_id) AS 'jumlah_semua',
        (SELECT COUNT(a.kios_id) AS 'jumlah_satuan' FROM master_kios a
        INNER JOIN (
        SELECT COUNT(kios_id), kios_id FROM pembiayaan
        WHERE subcluster_id = :subcluster_id and cluster_id=:cluster_id
        GROUP BY kios_id
        )b ON a.kios_id = b.kios_id
        GROUP BY a.kios_id) AS 'jumlah_satuan'
        FROM master_kios", [
            'subcluster_id' => $subClusterID,
            'cluster_id' => $clusterID,
        ]);
        return $query;
    }

    public function scopeAoGetDetailKiosbyClusterId($query, $clusterID, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT a.kios_id, b.`kios_nama`,b.`alamat`,b.`nama_pemilik` FROM pembiayaan a
        JOIN master_kios b ON a.`kios_id` = b.`kios_id`
        WHERE a.`subcluster_id`=:subcluster_id AND cluster_id=:cluster_id
        GROUP BY a.kios_id", [
            'subcluster_id' => $subClusterID,
            'cluster_id' => $clusterID,
        ]);
        return $query;
    }

    public function scopeAoGetMonitoringPencairanbyClusterAndSubCluster($query, $clusterID, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st,
        a.proses_tanam_id,d.petani_id,d.petani_kd,e.lahan_id,f.proses_tanam_nama,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`,a.`pembayaran_st`,
        a.pencairan_st, d.no_telp, d.email, c.pembiayaan_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`pembayaran_st` IS NOT NULL and c.subcluster_id = :subcluster_id and c.cluster_id=:cluster_id
        group by d.petani_id", [
            'subcluster_id' => $subClusterID,
            'cluster_id' => $clusterID,
        ]);
        return $query;
    }

    public function scopeAoGetMonitoringPersetujuan($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(hsl.`persetujuan_st` = 'yes',1,0)) AS sudah_disetujui,
        SUM(IF(hsl.`persetujuan_st`='no',1,0)) AS belum_disetujui,
        SUM(IF(hsl.`persetujuan_st` IS NOT NULL,1,0)) AS jumlah
        FROM (SELECT a.`persetujuan_st`FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`pembayaran_st` IS NOT NULL and c.subcluster_id=:subcluster_id
        GROUP BY b.pembiayaan_id,a.`proses_tanam_id`) hsl", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoGetDataAoHome($query, $userID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.`pendamping_id`,a.`pendamping_kd`,
        a.`nama_lengkap`,b.`subcluster_id`,c.`user_id`, a.`image_file_name` FROM pendamping a
        JOIN `pendamping_account_officer` b ON a.`pendamping_id`=b.`pendamping_id`
        JOIN com_user_pendamping c ON c.`pendamping_id`=b.`pendamping_id`
        WHERE c.`user_id`=:user_id", [
            'user_id' => $userID,
        ]);
        return $query;
    }

    public function scopeAoGetKiosBySubclusterId($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT COUNT(kios_id) AS 'jumlah_semua',
        (SELECT COUNT(a.kios_id) AS 'jumlah_satuan' FROM master_kios a
        INNER JOIN (
        SELECT COUNT(kios_id), kios_id FROM pembiayaan
        WHERE subcluster_id = :subcluster_id
        GROUP BY kios_id
        )b ON a.kios_id = b.kios_id
        GROUP BY a.kios_id) AS 'jumlah_satuan'
        FROM master_kios", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopePetaniGetListDetailRencanaKegiatanByPetaniId($query, $petaniID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st, k.file_name,
        a.proses_tanam_id,d.petani_id,d.petani_kd,e.lahan_id,f.proses_tanam_nama,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`
        ,a.`pembayaran_st`,
        a.persetujuan_st,a.`periode_kegiatan_start`,a.`kesiapan_kegiatan_date`,
        c.pembiayaan_st,c.pembiayaan_id
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_files k ON c.`pembiayaan_id` = k.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        JOIN master_item_rab g ON g.item_rab_id = b.item_rab_id
        WHERE a.`pembayaran_st` IS NOT NULL AND d.petani_id=:petani_id AND g.item_rab_id != '32' AND  k.`persyaratan_id` = '0201'
        GROUP BY b.pembiayaan_id,a.proses_tanam_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopeAogetMonitoring($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st,
        a.proses_tanam_id,d.petani_id,d.petani_kd,e.lahan_id,f.proses_tanam_nama,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`,a.`pembayaran_st`,
        a.pencairan_st, d.no_telp, d.email, c.pembiayaan_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.`pembayaran_st` IS NOT NULL and c.subcluster_id = :subcluster_id
        group by d.petani_id", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoGetJumlahRencanaKegiatan($query, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT SUM(IF(rst.kesiapan_kegiatan_st = 'no',1,0)) AS waiting,
        SUM(IF(rst.kesiapan_kegiatan_st='yes',1,0)) AS done,
        SUM(rst.kesiapan_kegiatan_st IS NOT NULL) AS jumlah
        FROM ( SELECT a.kesiapan_kegiatan_st
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.rencana_kegiatan_st IS NOT NULL and subcluster_id=:subcluster_id
        GROUP BY a.proses_tanam_id) rst", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoRencanaKegiatan($query, $pembiayaanID)
    {
        $query = DB::connection('mysql')->select("SELECT *
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.proses_tanam_id=c.proses_tanam_id
        WHERE b.pembiayaan_id=:pembiayaan_id
        GROUP BY a.proses_tanam_id", [
            'pembiayaan_id' => $pembiayaanID,
        ]);
        return $query;
    }

    public function scopeAoGetDataKios($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT b.`jumlah`,d.`nama_item`,d.`satuan`,
        (b.jumlah*b.`harga`) AS harga,a.kesiapan_stok_st,
        SUM(IF(a.`kesiapan_kegiatan_st`='yes',1,0)) AS kesiapan,
        SUM(a.`kesiapan_kegiatan_st`) AS total,
        e.`kios_nama`,e.`kios_id`,
        f.`alamat`,a.`kesiapan_stok_date`
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.`proses_tanam_id` = c.`proses_tanam_id`
        JOIN `master_item_rab` d ON b.`item_rab_id` = d.`item_rab_id`
        JOIN pembiayaan e ON e.`pembiayaan_id` = b.`pembiayaan_id`
        JOIN `master_kios` f ON e.`kios_id` = f.`kios_id`
        WHERE d.`group_rab_id`=1 AND a.`proses_tanam_id`=:proses_tanam_id
        AND b.`pembiayaan_id`=:pembiayaan_id", [
            'pembiayaan_id' => $pembiayaanID,
            'proses_tanam_id' => $prosesTanamID,
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
            'pembiayaan_id' => $pembiayaanID,
            'proses_tanam_id' => $prosesTanamID,
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
        GROUP BY a.proses_tanam_id) hsl", [
            'subcluster_id' => $subClusterID,
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
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }

    public function scopeAoGetRencanaKegiatanByClusterAndSubCluster($query, $clusterID, $subClusterID)
    {
        $query = DB::connection('mysql')->select("SELECT b.pembiayaan_id,a.pembiayaan_rab_mingguan_id,a.pembiayaan_rab_id,
        a.kesiapan_kegiatan_st,a.kesiapan_tenaga_kerja_st,a.rencana_kegiatan_st,
        a.proses_tanam_id,d.petani_id,d.petani_kd, e.lahan_id,
        e.luas_lahan,f.proses_tanam_nama, k.file_name, a.kesiapan_kegiatan_date,
        a.rencana_kegiatan_st,d.`nama_lengkap`,e.`lahan_kd`,d.`petani_kd`,c.`pengajuan_id`,d.`no_telp`,
        d.email,c.`pembiayaan_st`
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON b.pembiayaan_rab_id=a.pembiayaan_rab_id
        JOIN pembiayaan c ON b.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_files k ON c.`pembiayaan_id` = k.pembiayaan_id
        JOIN pembiayaan_petani d ON d.pembiayaan_id=c.pembiayaan_id
        JOIN pembiayaan_lahan e ON e.pembiayaan_id=c.pembiayaan_id
        JOIN master_proses_tanam f ON a.proses_tanam_id = f.proses_tanam_id
        WHERE a.rencana_kegiatan_st IS NOT NULL AND c.`subcluster_id`=:subcluster_id
        AND c.`cluster_id`=:cluster_id AND persyaratan_id = '0201'
        GROUP BY d.petani_id", [
            "subcluster_id" => $subClusterID,
            "cluster_id" => $clusterID,
        ]);
        return $query;
    }

    public function scopeAoGetJumlahKesiapanSaprodi($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT b.`jumlah`,d.`nama_item`,d.`satuan`,
        (b.jumlah*b.`harga`) AS harga,a.kesiapan_stok_st,
        SUM(IF(a.`kesiapan_stok_st`='yes',1,0)) AS kesiapan,
        SUM(a.`kesiapan_stok_st` IS NOT NULL) AS total,
        e.`kios_nama`,e.`kios_id`,
        f.`alamat`,a.`kesiapan_stok_date`
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN master_proses_tanam c ON a.`proses_tanam_id` = c.`proses_tanam_id`
        JOIN `master_item_rab` d ON b.`item_rab_id` = d.`item_rab_id`
        JOIN pembiayaan e ON e.`pembiayaan_id` = b.`pembiayaan_id`
        JOIN `master_kios` f ON e.`kios_id` = f.`kios_id`
        WHERE d.`group_rab_id`=1 AND a.`proses_tanam_id`=:proses_tanam_id
        AND b.`pembiayaan_id`=:pembiayaan_id", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID,
        ]);
        return $query;
    }

    public function scopeAoGetKios($query, $pembiayaanID, $prosesTanamID)
    {
        $query = DB::connection('mysql')->select("SELECT d.lahan_id,
        d.alamat,d.luas_lahan,
        d.mdb_name, a.kesiapan_lahan_st,a.kesiapan_kegiatan_date,
        a.kesiapan_tenaga_kerja_date,d.lahan_kd,
        a.kesiapan_tenaga_kerja_st,a.kesiapan_stok_date,
        a.kesiapan_stok_st,
        a.jumlah ,b.pembiayaan_rab_id,c.pembiayaan_id,e.petani_id
        FROM pembiayaan_rab_mingguan a
        JOIN pembiayaan_rab b ON a.pembiayaan_rab_id = b.pembiayaan_rab_id
        JOIN pembiayaan c ON c.pembiayaan_id = b.pembiayaan_id
        JOIN pembiayaan_lahan d ON c.pembiayaan_id = d.pembiayaan_id
        JOIN pembiayaan_petani e ON c.pembiayaan_id = e.pembiayaan_id
        WHERE b.pembiayaan_id=:pembiayaan_id AND
        a.proses_tanam_id=:proses_tanam_id
        GROUP BY a.proses_tanam_id", [
            "pembiayaan_id" => $pembiayaanID,
            "proses_tanam_id" => $prosesTanamID,
        ]);
        return $query;
    }
}
