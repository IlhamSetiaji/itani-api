<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\TaskPengajuan;
use App\Interfaces\TaskPengajuanInterface;

class TaskPengajuanRepository implements TaskPengajuanInterface
{
    public function create($pengajuanID)
    {
        return TaskPengajuan::create([
            'pengajuan_id'      => $pengajuanID,
            'kode_group'        => '03',
            // 'mdb'               => $this->com_user['user_id'],
            // 'mdb_name'          => $this->com_user['nama_lengkap'],
            'mdd'               => Carbon::now(),
        ]);
    }
}
