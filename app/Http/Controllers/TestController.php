<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\ComUser;
use Illuminate\Http\Request;
use App\Models\TaskPengajuan;
use App\Models\PanenPengangkutan;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\PembiayaanKunjungan;
use App\Models\TaskPengajuanProcess;
use Illuminate\Support\Facades\Hash;
use App\Models\PembiayaanRabMingguan;
use App\Models\PembiayaanRabTambahan;
use App\Models\PanenPengangkutanHasil;
use App\Http\Requests\HasilPanenRequest;
use App\Http\Requests\PembiayaanKunjunganRequest;
use App\Http\Requests\PembiayaanKunjunganUpdateRequest;
use App\Http\Requests\PembiayaanRabTambahanRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PengangkutanRequest;
use App\Http\Requests\PengangkutanUpdateRequest;

class TestController extends Controller
{
    private function generate_id()
    {
        list($usec, $sec) = explode(" ", microtime());
        $microtime = $sec . $usec;
        $microtime = str_replace(array(',', '.'), array('', ''), $microtime);
        $microtime = substr_replace($microtime, rand(10000, 99999), -2);
        return $microtime;
    }

    public function petaniPostPermintaanKunjungan(PembiayaanKunjunganRequest $request)
    {
        $payload = $request->validated();
        $payload['status_kunjungan'] = 'no';
        $payload['tanggal_dibuat'] = Carbon::now();
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanKunjungan::create($payload);
            return ResponseFormatter::success($result, 'Data berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function agronomisLaporanPermintaanKunjungan(PembiayaanKunjunganUpdateRequest $request, $pembiayaanKunjunganID)
    {
        $pembiayaanKunjungan = PembiayaanKunjungan::find($pembiayaanKunjunganID);
        if (!$pembiayaanKunjungan) {
            return ResponseFormatter::error(null, 'Data pembiayaan kunjungan tidak ditemukan', 404);
        }
        $payload = $request->validated();
        try {
            if (request()->has('rekomendasi')) {
                $payload['rekomendasi_st'] = 'no';
                $pembiayaanKunjungan->update($payload);
                return ResponseFormatter::success($pembiayaanKunjungan, 'Data pembiayaan kunjungan berhasil diupdate');
            }
            $pembiayaanKunjungan->update($payload);
            return ResponseFormatter::success($pembiayaanKunjungan, 'Data pembiayaan kunjungan berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function agronomisAddRabTambahan(PembiayaanRabTambahanRequest $request)
    {
        $payload = $request->validated();
        $payload['pengambilan_st'] = 'no';
        $payload['mdd'] = Carbon::now();
        try {
            $result = PembiayaanRabTambahan::create($payload);
            return ResponseFormatter::success($result, 'Data berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function postSidangKomite($pembiayaanID, $pengajuanID, $prosesTanamID)
    {
        $countSidangKomite = DB::table('com_user_sidangkomite')->count();
        // $pembiayaanRabMingguan = PembiayaanRabMingguan::with('pembiayaan_rab')->where('pembiayaan_rab_mingguan.proses_tanam_id', $prosesTanamID)->where('pembiayaan_rab.pembiayaan_id', $pembiayaanID)->first();
        $pembiayaanRabMingguan = PembiayaanRabMingguan::where('proses_tanam_id', $prosesTanamID)
            ->with('pembiayaan_rab', function ($query) use ($pembiayaanID) {
                $query->where('pembiayaan_id', $pembiayaanID);
            })->first();
        if (!$pembiayaanRabMingguan) {
            return ResponseFormatter::error(null, 'Data pembiayaan rab mingguan tidak ditemukan', 404);
        }
        $arrTaskPengajuanProcess = array();
        try {
            $TaskPengajuan = TaskPengajuan::create([
                'pengajuan_id'      => $pengajuanID,
                'kode_group'        => '03',
                // 'mdb'               => $this->com_user['user_id'],
                // 'mdb_name'          => $this->com_user['nama_lengkap'],
                'mdd'               => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
        try {
            $pembiayaanRabMingguan->update([
                'pengajuan_id' => $pengajuanID,
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
        for ($i = 0; $i < $countSidangKomite; $i++) {
            try {
                $taskPengajuanProcess = TaskPengajuanProcess::create([
                    'process_id'        => $this->generate_id(),
                    'flow_id'           => '0301',
                    'flow_prev_id'      => '0204',
                    'pengajuan_id'      => $pengajuanID,
                    // 'mdb'               => $this->com_user['user_id'],
                    // 'mdb_name'          => $this->com_user['nama_lengkap'],
                    'mdd'               => Carbon::now(),
                ]);
                array_push($arrTaskPengajuanProcess, $taskPengajuanProcess);
            } catch (Exception $e) {
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
        }
        $result = [
            'task pengajuan' => $TaskPengajuan,
            'pembiayaan rab mingguan' => $pembiayaanRabMingguan,
            'task pengajuan process' => $arrTaskPengajuanProcess,
        ];
        return ResponseFormatter::success($result, 'Data berhasil diproses');
    }

    public function postSidangKomiteTambahan($pembiayaanID, $pengajuanID)
    {
        $countSidangKomite = DB::table('com_user_sidangkomite')->count();
        $pembiayaanRabTambahan = PembiayaanRabTambahan::where('pembiayaan_id', $pembiayaanID)->get();
        if (sizeof($pembiayaanRabTambahan) == 0) {
            return ResponseFormatter::error(null, 'Data pembiayaan rab tambahan tidak ditemukan');
        }
        $arrTaskPengajuanProcess = array();
        $arrPembiayaanRabTambahan = array();
        try {
            $TaskPengajuan = TaskPengajuan::create([
                'pengajuan_id'      => $pengajuanID,
                'kode_group'        => '03',
                // 'mdb'               => $this->com_user['user_id'],
                // 'mdb_name'          => $this->com_user['nama_lengkap'],
                'mdd'               => Carbon::now(),
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
        foreach ($pembiayaanRabTambahan as $value) {
            try {
                $value->update([
                    'pengajuan_id' => $pengajuanID,
                    'pembiayaan_id' => $pembiayaanID,
                ]);
                array_push($arrPembiayaanRabTambahan, $value);
            } catch (Exception $e) {
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
        }
        for ($i = 0; $i < $countSidangKomite; $i++) {
            try {
                $taskPengajuanProcess = TaskPengajuanProcess::create([
                    'process_id'        => $this->generate_id(),
                    'flow_id'           => '0301',
                    'pengajuan_id'      => $pengajuanID,
                    // 'mdb'               => $this->com_user['user_id'],
                    // 'mdb_name'          => $this->com_user['nama_lengkap'],
                    'mdd'               => Carbon::now(),
                ]);
                array_push($arrTaskPengajuanProcess, $taskPengajuanProcess);
            } catch (Exception $e) {
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
        }
        $result = [
            'task pengajuan' => $TaskPengajuan,
            'pembiayaan rab tambahan' => $arrPembiayaanRabTambahan,
            'task pengajuan process' => $arrTaskPengajuanProcess,
        ];
        return ResponseFormatter::success($result, 'Data berhasil diproses');
    }

    public function insertPengangkutan(PengangkutanRequest $request)
    {
        $payload = $request->validated();
        try {
            $result = PanenPengangkutan::create($payload);
            return ResponseFormatter::success($result, 'Data pengangkutan berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function updatePengangkutan(PengangkutanUpdateRequest $request, $pengangkutanID)
    {
        $pengangkutan = PanenPengangkutan::find($pengangkutanID);
        if (!$pengangkutan) {
            return ResponseFormatter::error(null, 'Data pengangkutan tidak ditemukan', 404);
        }
        $payload = $request->validated();
        $payload['pengangkutan_st'] = 'done';
        try {
            $pengangkutan->update($payload);
            return ResponseFormatter::success($pengangkutan, 'Data pengangkutan berhasil diupdate');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function addHasilPanen(HasilPanenRequest $request)
    {
        $payload = $request->validated();
        $payload['mdd'] = Carbon::now();
        try {
            $result = PanenPengangkutanHasil::create($payload);
            return ResponseFormatter::success($result, 'Data panen pengangkutan hasil berhasil diinputkan');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage(), 400);
        }
    }

    public function insertPassword()
    {
        $users = ComUser::all();
        foreach ($users as $user) {
            try {
                $user->update([
                    'password' => Hash::make('semuabisa'),
                ]);
            } catch (Exception $e) {
                return ResponseFormatter::error(null, $e->getMessage(), 400);
            }
        }
        return ResponseFormatter::success($users, 'Data password berhasil diinputkan');
    }
}
