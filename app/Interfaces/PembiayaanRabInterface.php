<?php

namespace App\Interfaces;

interface PembiayaanRabInterface
{
    public function update($payload, $pembiayaanID, $itemRabID);
}
