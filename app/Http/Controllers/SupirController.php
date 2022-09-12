<?php

namespace App\Http\Controllers;

use App\Models\ComUser;
use App\Models\RoleSupir;
use App\Models\MasterSupir;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\PanenPengangkutan;
use Illuminate\Support\Facades\DB;

class SupirController extends Controller
{
    public function __construct()
    {
        // $this->middleware('supir');
    }

    public function supirGetPetaniAktif()
    {
        $result = RoleSupir::SupirGetAktifPetani();
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data kios berhasil didapatkan');
    }

    public function supirGetDataDashboard($supirID)
    {
        $supir = MasterSupir::find($supirID);
        if (!$supir) {
            return ResponseFormatter::error(null, 'Data Supir tidak ditemukan', 404);
        }
        $result = RoleSupir::SupirGetDataDashboard($supirID);

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Supir berhasil didapatkan');
    }


    public function supirGetPengangkutanTerkirim($supirID)
    {
        $supir = MasterSupir::find($supirID);
        if (!$supir) {
            return ResponseFormatter::error(null, 'Data Supir tidak ditemukan', 404);
        }
        $result = RoleSupir::SupirGetPengangkutanTerkirim($supirID);

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Supir berhasil didapatkan');
    }


    public function supirGetPengangkutanAktif($supirID)
    {
        $supir = MasterSupir::find($supirID);
        if (!$supir) {
            return ResponseFormatter::error(null, 'Data Supir tidak ditemukan', 404);
        }
        $result = RoleSupir::SupirGetPengangkutanAktif($supirID);

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Supir berhasil didapatkan');
    }

    public function supirGetDataAkun($userID)
    {
        $supir = ComUser::find($userID);
        if (!$supir) {
            return ResponseFormatter::error(null, 'Data User tidak ditemukan', 404);
        }

        $result = RoleSupir::SupirGetDataAkun($userID);

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Supir berhasil didapatkan');
    }


    public function supirGetDetailPengangkutanTerkirim($pengangkutanID)
    {
        $pengangkutan = PanenPengangkutan::find($pengangkutanID);

        if (!$pengangkutan) {
            return ResponseFormatter::error(null, 'Data panen tidak ditemukan', 404);
        }

        $result = RoleSupir::supirGetDetailPengangkutanTerkirim($pengangkutanID);

        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Panen berhasil didapatkan');
    }


    public function supirGetDataTruk()
    {
        $result = RoleSupir::SupirGetDataTruk();
        if (sizeof($result) == 0) {
            return response()->json([
                'data' => null,
                'status' => 204,
                'message' => 'Data Kosong',
            ]);
        }
        return ResponseFormatter::success($result, 'Data Supir berhasil didapatkan');
    }
}
