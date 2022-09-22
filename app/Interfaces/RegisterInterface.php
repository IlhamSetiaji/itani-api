<?php

namespace App\Interfaces;

interface RegisterInterface
{
    public function generate_id();
    public function createResponse($payload);
    public function updateResponse($payload, $user, $pegawai);
}
