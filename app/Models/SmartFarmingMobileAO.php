<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartFarmingMobileAO extends Model
{
    use HasFactory;

    public function scopeCountAllPetani($query, $args = array())
    {
        $params = array();
        $sql = "SELECT COUNT(*) AS 'total' FROM ( 
            SELECT a.* FROM pendataan_petani a 
            INNER JOIN master_subcluster b ON a.subcluster_id = b.subcluster_id  
            INNER JOIN pendataan_petani_alamat c ON a.petani_id = c.petani_id 
            INNER JOIN master_cluster i ON a.cluster_id = i.cluster_id
            INNER JOIN master_jenjang_pendidikan h ON h.jenjang_id = a.jenjang_id
            WHERE a.petani_id <> 0 ";
        if (!empty($args['petani_kd'])) {
            $sql .= " AND a.petani_kd LIKE ?";
            array_push($params, "{$args['petani_kd']}%");
        }
        if (!empty($args['nama_lengkap'])) {
            $sql .= " AND a.nama_lengkap LIKE ?";
            array_push($params, "{$args['nama_lengkap']}%");
        }
        if (!empty($args['nik'])) {
            $sql .= " AND a.nik LIKE ?";
            array_push($params, "{$args['nik']}%");
        }
        if (!empty($args['active_st'])) {
            $sql .= " AND a.active_st = ?";
            array_push($params, $args['active_st']);
        }
        if (!empty($args['prov_id'])) {
            $sql .= " AND b.prov_id = ?";
            array_push($params, $args['prov_id']);
        }
        if (!empty($args['kab_id'])) {
            $sql .= " AND b.kab_id = ?";
            array_push($params, $args['kab_id']);
        }
        if (!empty($args['kec_id'])) {
            $sql .= " AND b.kec_id = ?";
            array_push($params, $args['kec_id']);
        }
        if (!empty($args['kel_id'])) {
            $sql .= " AND b.kel_id = ?";
            array_push($params, $args['kel_id']);
        }
        if (!empty($args['cluster_id'])) {
            $sql .= " AND a.cluster_id = ?";
            array_push($params, $args['cluster_id']);
        }
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND a.subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        $sql .= " GROUP BY a.petani_id)rs1";
        $query = DB::connection('mysql_second')->select($sql, [$params[0]]);
        return $query[0]->total;
    }

    public function scopeCountAllLahanBySubclusterID($query, $subClusterID)
    {
        $query = DB::connection('mysql_second')->select("SELECT COUNT(*) AS 'total' FROM pendataan_lahan a WHERE a.lahan_id <> 0 AND a.subcluster_id = :subcluster_id", [
            "subcluster_id" => $subClusterID,
        ]);
        return $query[0]->total;
    }

    public function scopeSumLuasLahanBySubclusterID($query, $subClusterID)
    {
        $query = DB::connection('mysql_second')->select("SELECT SUM(luas_lahan) AS luas_lahan 
        FROM pendataan_lahan WHERE lahan_id <> 0 AND subcluster_id = :subcluster_id", [
            "subcluster_id" => $subClusterID,
        ]);
        return $query[0]->luas_lahan;
    }

    public function scopeCountAllPengajuan($query, $args = array())
    {
        $params = array();
        $sql = "SELECT COUNT(*) AS 'total' FROM (SELECT a.*
        FROM pembiayaan a 
            LEFT JOIN pembiayaan_petani b ON a.pembiayaan_id = b.pembiayaan_id
            LEFT JOIN pembiayaan_lahan c ON a.pembiayaan_id = c.pembiayaan_id
            LEFT JOIN pembiayaan_files d ON a.pembiayaan_id = d.pembiayaan_id AND d.persyaratan_id = '0201'
            LEFT JOIN task_pengajuan e ON a.pengajuan_id = e.pengajuan_id
        WHERE a.pembiayaan_id <> 0";
        if (!empty($args['nama_lengkap'])) {
            $sql .= " AND b.nama_lengkap LIKE ?";
            array_push($params, "{$args['nama_lengkap']}%");
        }
        if (!empty($args['petani_kd'])) {
            $sql .= " AND b.petani_kd LIKE ?";
            array_push($params, "{$args['petani_kd']}%");
        }
        if (!empty($args['nik'])) {
            $sql .= " AND b.nik LIKE ?";
            array_push($params, "{$args['nik']}%");
        }
        if (!empty($args['pengajuan_st'])) {
            $sql .= " AND e.pengajuan_st = ?";
            array_push($params, $args['pengajuan_st']);
        }
        if (!empty($args['cluster_id'])) {
            $sql .= " AND a.cluster_id = ?";
            array_push($params, $args['cluster_id']);
        }
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND a.subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        if (!empty($args['active_st'])) {
            $sql .= " AND a.active_st = ?";
            array_push($params, $args['active_st']);
        }
        $sql .= " GROUP BY a.pembiayaan_id)rs1";
        $query = DB::connection('mysql')->select($sql, [$params[0]]);
        return $query[0]->total;
    }

    public function scopeSumAllPembiayaan($query, $args = array())
    {
        $params = array();
        $sql = "SELECT SUM(nilai_pembiayaan) AS nilai_pembiayaan 
        FROM pembiayaan WHERE pembiayaan_id <> 0";
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        $query = DB::connection('mysql')->select($sql, [$params[0]]);
        return $query[0]->nilai_pembiayaan;
    }

    public function scopePetaniGetAll($query, $args = array())
    {
        $params = array();
        $sql = "SELECT SUM(nilai_pembiayaan) AS nilai_pembiayaan 
        FROM pembiayaan WHERE pembiayaan_id <> 0";
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        $sql = "SELECT c.*, b.*, a.*, k.kios_kd,k.kios_nama, d.prov_nama, e.kab_nama, f.kec_nama, g.kel_nama, h.jenjang_nama AS 'jenjang_pendidikan', i.cluster_nama, j.subcluster_nama, l.user_id
        FROM pendataan_petani a
        LEFT JOIN pendataan_petani_alamat b ON a.petani_id = b.petani_id AND b.jenis_id = '01'
        LEFT JOIN pendataan_petani_files c ON a.petani_id = c.petani_id AND c.persyaratan_id = '0201'
        LEFT JOIN wilayah_provinsi d ON b.prov_id = d.prov_id
        LEFT JOIN wilayah_kabupaten e ON b.kab_id = e.kab_id
        LEFT JOIN wilayah_kecamatan f ON b.kec_id = f.kec_id
        LEFT JOIN wilayah_kelurahan g ON b.kel_id = g.kel_id
        LEFT JOIN master_jenjang_pendidikan h ON a.jenjang_id = h.jenjang_id
        LEFT JOIN master_cluster i ON a.cluster_id = i.cluster_id
        LEFT JOIN master_subcluster j ON a.subcluster_id = j.subcluster_id
        LEFT JOIN ipangan_finance_v1_db_demo.master_kios k ON a.kios_id = k.kios_id
        LEFT JOIN com_user_petani l ON a.petani_id = l.petani_id
        WHERE a.petani_id <> 0";
        if (!empty($args['petani_kd'])) {
            $sql .= " AND a.petani_kd LIKE ?";
            array_push($params, "{$args['petani_kd']}%");
        }
        if (!empty($args['nama_lengkap'])) {
            $sql .= " AND a.nama_lengkap LIKE ?";
            array_push($params, "{$args['nama_lengkap']}%");
        }
        if (!empty($args['mdb_name'])) {
            $sql .= " AND a.mdb_name LIKE ?";
            array_push($params, "%{$args['mdb_name']}%");
        }
        if (!empty($args['nik'])) {
            $sql .= " AND a.nik LIKE ?";
            array_push($params, "{$args['nik']}%");
        }
        if (!empty($args['active_st'])) {
            $sql .= " AND a.active_st = ?";
            array_push($params, $args['active_st']);
        }
        if (!empty($args['prov_id'])) {
            $sql .= " AND b.prov_id = ?";
            array_push($params, $args['prov_id']);
        }
        if (!empty($args['kab_id'])) {
            $sql .= " AND b.kab_id = ?";
            array_push($params, $args['kab_id']);
        }
        if (!empty($args['kec_id'])) {
            $sql .= " AND b.kec_id = ?";
            array_push($params, $args['kec_id']);
        }
        if (!empty($args['kel_id'])) {
            $sql .= " AND b.kel_id = ?";
            array_push($params, $args['kel_id']);
        }
        if (!empty($args['cluster_id'])) {
            $sql .= " AND a.cluster_id = ?";
            array_push($params, $args['cluster_id']);
        }
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND a.subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        $query = DB::connection('mysql_second')->select($sql, [$params[0]]);
        return $query;
    }

    public function scopePetaniGetByID($query, $petaniID)
    {
        $query = DB::connection('mysql_second')->select("SELECT c.*, b.*, a.*, d.prov_nama, e.kab_nama, f.kec_nama, g.kel_nama,
        h.jenjang_nama AS 'jenjang_pendidikan', i.cluster_nama, j.subcluster_nama, k.kios_id,k.kios_kd,k.kios_nama, l.user_id
        FROM pendataan_petani a
        LEFT JOIN pendataan_petani_alamat b ON a.petani_id = b.petani_id AND b.jenis_id = '01'
        LEFT JOIN pendataan_petani_files c ON a.petani_id = c.petani_id AND c.persyaratan_id = '0201'
        LEFT JOIN wilayah_provinsi d ON b.prov_id = d.prov_id
        LEFT JOIN wilayah_kabupaten e ON b.kab_id = e.kab_id
        LEFT JOIN wilayah_kecamatan f ON b.kec_id = f.kec_id
        LEFT JOIN wilayah_kelurahan g ON b.kel_id = g.kel_id
        LEFT JOIN master_jenjang_pendidikan h ON a.jenjang_id = h.jenjang_id
        LEFT JOIN master_cluster i ON a.cluster_id = i.cluster_id
        LEFT JOIN master_subcluster j ON a.subcluster_id = j.subcluster_id
        LEFT JOIN ipangan_finance_v1_db_demo.master_kios k ON a.kios_id = k.kios_id
        LEFT JOIN com_user_petani l ON a.petani_id = l.petani_id
        WHERE a.petani_id = :petani_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopeGetPetaniFiles($query, $petaniID)
    {
        $query = DB::connection('mysql_second')->select("SELECT b.*, a.* 
        FROM pendataan_petani_files a 
        LEFT JOIN persyaratan_files b ON a.persyaratan_id = b.persyaratan_id
        WHERE a.petani_id = :petani_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopeGetPetaniAlamat($query, $petaniID)
    {
        $query = DB::connection('mysql_second')->select("SELECT b.*, a.*, c.prov_nama, d.kab_nama, e.kec_nama, f.kel_nama 
        FROM pendataan_petani_alamat a 
            LEFT JOIN master_jenis_alamat b ON a.jenis_id = b.jenis_id
            LEFT JOIN wilayah_provinsi c ON a.prov_id = c.prov_id
            LEFT JOIN wilayah_kabupaten d ON a.kab_id = d.kab_id
            LEFT JOIN wilayah_kecamatan e ON a.kec_id = e.kec_id
            LEFT JOIN wilayah_kelurahan f ON a.kel_id = f.kel_id
        WHERE a.petani_id = :petani_id", [
            'petani_id' => $petaniID,
        ]);
        return $query;
    }

    public function scopeGetListPengajuanPembiayaan($query, $args = array())
    {
        $params = array();
        $sql = "SELECT a.*, b.nama_lengkap, b.petani_id, b.petani_kd, b.nik, b.no_telp,
        ROUND(SUM(c.luas_lahan), 2) AS luas_lahan, d.file_name, d.file_path,
        e.pengajuan_st, e.pengajuan_keterangan
        FROM pembiayaan a 
            LEFT JOIN pembiayaan_petani b ON a.pembiayaan_id = b.pembiayaan_id
            LEFT JOIN pembiayaan_lahan c ON a.pembiayaan_id = c.pembiayaan_id
            LEFT JOIN pembiayaan_files d ON a.pembiayaan_id = d.pembiayaan_id AND d.persyaratan_id = '0201'
            LEFT JOIN task_pengajuan e ON a.pengajuan_id = e.pengajuan_id
        WHERE a.pembiayaan_id <> 0";
        if (!empty($args['nama_lengkap'])) {
            $sql .= " AND b.nama_lengkap LIKE ?";
            array_push($params, "{$args['nama_lengkap']}%");
        }
        if (!empty($args['petani_kd'])) {
            $sql .= " AND b.petani_kd LIKE ?";
            array_push($params, "{$args['petani_kd']}%");
        }
        if (!empty($args['nik'])) {
            $sql .= " AND b.nik LIKE ?";
            array_push($params, "{$args['nik']}%");
        }
        if (!empty($args['pengajuan_st'])) {
            $sql .= " AND e.pengajuan_st = ?";
            array_push($params, $args['pengajuan_st']);
        }
        if (!empty($args['cluster_id'])) {
            $sql .= " AND a.cluster_id = ?";
            array_push($params, $args['cluster_id']);
        }
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND a.subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        $sql .= " GROUP BY a.pembiayaan_id ORDER BY b.nama_lengkap";
        $query = DB::connection('mysql')->select($sql, [$params[0]]);
        return $query[0];
    }

    public function scopeGetLahan($query, $args = array())
    {
        $params = array();
        $sql = "SELECT a.*, a.luas_lahan AS luas_lahan_orig,
        ROUND(a.luas_lahan, 2) AS luas_lahan, 
        ST_AsGeoJson(a.geom) AS geojson, 
        ST_AsText(a.koordinat) AS 'koordinat',
        ST_Y(a.koordinat) AS latitude, 
        ST_X(a.koordinat) AS longitude,
        b.blok_lahan_kd, b.blok_lahan_nama, 
        c.kelompok_kd, c.kelompok_nama, c.ketua_kelompok, 
        d.prov_nama, e.kab_nama, f.kec_nama, g.kel_nama, h.cluster_nama, i.subcluster_nama
        FROM pendataan_lahan a 
            LEFT JOIN pendataan_blok_lahan b ON a.blok_lahan_id = b.blok_lahan_id
            LEFT JOIN pendataan_kelompok_tani c ON b.kelompok_id = c.kelompok_id
            LEFT JOIN wilayah_provinsi d ON a.prov_id = d.prov_id
            LEFT JOIN wilayah_kabupaten e ON a.kab_id = e.kab_id
            LEFT JOIN wilayah_kecamatan f ON a.kec_id = f.kec_id
            LEFT JOIN wilayah_kelurahan g ON a.kel_id = g.kel_id
            LEFT JOIN master_cluster h ON a.cluster_id = h.cluster_id
            LEFT JOIN master_subcluster i ON a.subcluster_id = i.subcluster_id
        WHERE a.lahan_id <> 0";
        if (!empty($args['lahan_id'])) {
            if (!is_array($args['lahan_id'])) {
                $args['lahan_id'] = array($args['lahan_id']);
            }
            $sql .= " AND a.lahan_id IN (?";
            array_push($params, $args['lahan_id'][0]);
            for ($i = 1; $i < count($args['lahan_id']); $i++) {
                $sql .= " ,?";
                array_push($params, $args['lahan_id'][$i]);
            }
            $sql .= ")";
        }
        if (!empty($args['lahan_kd'])) {
            $sql .= " AND a.lahan_kd LIKE ?";
            array_push($params, "{$args['lahan_kd']}%");
        }
        if (!empty($args['nama_pemilik'])) {
            $sql .= " AND a.nama_pemilik LIKE ?";
            array_push($params, "{$args['nama_pemilik']}%");
        }
        if (!empty($args['active_st'])) {
            $sql .= " AND a.active_st = ?";
            array_push($params, $args['active_st']);
        }
        if (!empty($args['prov_id'])) {
            $sql .= " AND a.prov_id = ?";
            array_push($params, $args['prov_id']);
        }
        if (!empty($args['kab_id'])) {
            $sql .= " AND a.kab_id = ?";
            array_push($params, $args['kab_id']);
        }
        if (!empty($args['kec_id'])) {
            $sql .= " AND a.kec_id = ?";
            array_push($params, $args['kec_id']);
        }
        if (!empty($args['kel_id'])) {
            $sql .= " AND a.kel_id = ?";
            array_push($params, $args['kel_id']);
        }
        if (!empty($args['cluster_id'])) {
            $sql .= " AND a.cluster_id = ?";
            array_push($params, $args['cluster_id']);
        }
        if (!empty($args['subcluster_id'])) {
            $sql .= " AND a.subcluster_id = ?";
            array_push($params, $args['subcluster_id']);
        }
        // dd($params);
        $sql .= " ORDER BY a.nama_pemilik";
        $query = DB::connection('mysql_second')->select($sql, $params);
        // dd($query);
        return $query;
    }

    public function scopeGetKios($query)
    {
        $query = DB::connection('mysql')->select("SELECT a.kios_id, a.kios_kd , a.kios_nama, b.kel_nama FROM master_kios a
        JOIN wilayah_kelurahan b ON a.kel_id = b.kel_id
        WHERE a.active_st = 'yes'");
        return $query;
    }

    public function scopeVarietasKomoditasByCluster($query, $clusterID, $args = array())
    {
        $params = array();
        $sql = "SELECT a.*, c.komoditas_nama
        FROM master_varietas a 
        INNER JOIN mapping_varietas_lokal b ON a.varietas_id = b.varietas_id
        LEFT JOIN master_komoditas c ON a.komoditas_id = c.komoditas_id
			WHERE b.cluster_id = ?";
        array_push($params, $clusterID);
        if (!empty($args['komoditas_id'])) {
            $sql .= " AND a.komoditas_id = ?";
            array_push($params, $args['komoditas_id']);
        }
        if (!empty($args['active_st'])) {
            $sql .= " AND a.active_st = ?";
            array_push($params, $args['active_st']);
        }
        $sql .= " ORDER BY a.varietas_nama";
        $query = DB::connection('mysql_second')->select($sql, $params);
        return $query;
    }

    public function scopeGetDetailRabMingguan($query, $rabID, $rsLuas)
    {
        $sql = "SELECT a.*, b.rab_detail_mingguan_id, b.jumlah, c.nama_item, c.satuan, f.proses_tanam_id, f.proses_tanam_nama, f.sort
        FROM rab_detail a
            LEFT JOIN rab_detail_mingguan b ON a.rab_detail_id = b.rab_detail_id
            LEFT JOIN master_item_rab c ON a.item_rab_id = c.item_rab_id
            LEFT JOIN master_group_item_rab d ON c.group_rab_id = d.group_id
            LEFT JOIN master_paket_rab e ON a.paket_rab_id = e.paket_rab_id
            LEFT JOIN master_proses_tanam f ON b.proses_tanam_id = f.proses_tanam_id
        WHERE a.rab_id = ? AND e.paket_rab_luas = ?
        ORDER BY c.sort ASC";
        $params = array($rabID, $rsLuas);
        $query = DB::connection('mysql')->select($sql, $params);
        // $result = collect($query)->toArray();
        return $query;
    }

    public function scopeInsertLahan($query, $params)
    {
        $sql = "INSERT INTO pendataan_lahan (
            lahan_id, lahan_ref_id, cluster_id, subcluster_id, lahan_kd, blok_lahan_id, nama_pemilik, 
            luas_lahan, luas_sppt, lahan_st, koordinat, geom, 
            alamat, rt, rw, prov_id, kab_id, 
            kec_id, kel_id, active_st, created_by, created_at, 
            mdb, mdb_name, mdd, validasi_by, validasi_by_name, validasi_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, st_multilinestringfromtext(?),  ST_GeomFromGeoJSON('{$params['geojson']}'),
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?
            )";
        $new_params = array(
            $params['lahan_id'],
            $params['lahan_ref_id'],
            $params['cluster_id'],
            $params['subcluster_id'],
            $params['lahan_kd'],
            $params['blok_lahan_id'],
            $params['nama_pemilik'],
            $params['luas_lahan'],
            $params['luas_sppt'],
            $params['lahan_st'],
            $params['koordinat'],
            // $params['geojson'],
            $params['alamat'],
            $params['rt'],
            $params['rw'],
            $params['prov_id'],
            $params['kab_id'],
            $params['kec_id'],
            $params['kel_id'],
            $params['active_st'],
            $params['created_by'],
            $params['created_at'],
            $params['mdb'],
            $params['mdb_name'],
            $params['mdd'],
            $params['validasi_by'],
            $params['validasi_by_name'],
            $params['validasi_at'],
        );

        $query = DB::connection('mysql_second')->insert($sql, $new_params);
        return $query;
    }

    public function scopeGenerateKD($query, $prefix)
    {
        $sql = "SELECT IF(next_kd IS NULL, '0001', next_kd) AS 'next_kd' 
        FROM (
            SELECT LPAD((MAX(RIGHT(lahan_kd, 4))+1), 4, '0') AS 'next_kd' 
            FROM pendataan_lahan 
            WHERE LEFT(lahan_kd, 7) = 0001
        ) AS rs";
        $query = DB::connection('mysql_second')->select($sql);
        if ($query > 0) {
            return $prefix . $query[0]->next_kd;
        }
        return $prefix . '0001';
    }

    public function scopeGetMasterSubclusterByID($query, $subClusterID)
    {
        $query = DB::connection('mysql_second')->select("SELECT a.*, b.cluster_nama, b.cluster_kd, c.prov_id
        FROM master_subcluster a 
            LEFT JOIN master_cluster b ON a.cluster_id = b.cluster_id
            LEFT JOIN wilayah_kabupaten c ON b.kab_id = c.kab_id
        WHERE a.subcluster_id = :subcluster_id", [
            'subcluster_id' => $subClusterID,
        ]);
        return $query;
    }
}
