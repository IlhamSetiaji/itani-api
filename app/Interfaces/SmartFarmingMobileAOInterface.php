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
    public function transformsPengajuan($arr_pengajuan);
    public function transformLahan($lahan);
    public function transformLahanAnother($result);
    public function transformVarietas($result);
    public function calculateRab($luas_lahan);
    public function generate_id();
    public function postLahan($payload);
}
