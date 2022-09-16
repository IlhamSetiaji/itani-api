<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthLogin extends Model
{
    use HasFactory;

    public function scopeUserAccountOfficer($query, $user)
    {
        $query = collect(DB::connection('mysql_second')->select("SELECT a.*, c.pendamping_kd, d.cluster_nama, e.subcluster_nama
                    FROM pendamping_account_officer a
                        INNER JOIN com_user_pendamping b ON a.pendamping_id = b.pendamping_id
                        INNER JOIN pendamping c ON a.pendamping_id = c.pendamping_id
                        LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
            'user_id' => $user->user_id,
        ]))->first();
        return $query;
    }

    public function scopeUserAgronomis($query, $user)
    {
        $query = collect(DB::connection('mysql_second')->select("SELECT a.*, c.pendamping_kd, d.cluster_nama, e.subcluster_nama
                    FROM pendamping_agronomis a
                        INNER JOIN com_user_pendamping b ON a.pendamping_id = b.pendamping_id
                        INNER JOIN pendamping c ON a.pendamping_id = c.pendamping_id
                        LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
            'user_id' => $user->user_id,
        ]))->first();
        return $query;
    }

    public function scopeUserPetaniAndSupir($query, $user)
    {
        $query = DB::connection('mysql_second')->select("SELECT c.pendamping_kd
                    FROM com_user_pendamping b
                        INNER JOIN pendamping c ON b.pendamping_id = c.pendamping_id
                        -- LEFT JOIN master_cluster d ON a.cluster_id = d.cluster_id
                        -- LEFT JOIN master_subcluster e ON a.subcluster_id = e.subcluster_id
                    WHERE b.user_id = :user_id", [
            'user_id' => $user->user_id,
        ]);
        return $query;
    }
}
