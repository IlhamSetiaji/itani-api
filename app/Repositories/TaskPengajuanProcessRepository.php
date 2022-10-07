<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\TaskPengajuan;
use App\Models\TaskPengajuanProcess;
use App\Interfaces\TaskPengajuanProcessInterface;

class TaskPengajuanProcessRepository implements TaskPengajuanProcessInterface
{
    public function create($pengajuanID)
    {
        return TaskPengajuanProcess::create([
            'process_id'        => $this->generate_id(),
            'flow_id'           => '0301',
            'flow_prev_id'      => '0204',
            'pengajuan_id'      => $pengajuanID,
            // 'mdb'               => $this->com_user['user_id'],
            // 'mdb_name'          => $this->com_user['nama_lengkap'],
            'mdd'               => Carbon::now(),
        ]);
    }

    public function createSecond($pengajuanID)
    {
        return TaskPengajuanProcess::create([
            'process_id'        => $this->generate_id(),
            'flow_id'           => '0301',
            'pengajuan_id'      => $pengajuanID,
            // 'mdb'               => $this->com_user['user_id'],
            // 'mdb_name'          => $this->com_user['nama_lengkap'],
            'mdd'               => Carbon::now(),
        ]);
    }
}
