<?php

namespace App\Interfaces;

interface SmartFarmingMobileAOInterface
{
    public function petaniGetDetail($alamat, $files, $result);
    public function transform($petani);
    public function transformAlamat($alamat);
    public function transformFile($file);
    public function transformPengajuan($pengajuan);
    public function transformsItemRab($arr_item);
    public function transformItemRab($item);
}
