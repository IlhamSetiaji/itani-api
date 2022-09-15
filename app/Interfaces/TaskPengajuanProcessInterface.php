<?php

namespace App\Interfaces;

interface TaskPengajuanProcessInterface
{
    public function create($pengajuanID);
    public function createSecond($pengajuanID);
}
