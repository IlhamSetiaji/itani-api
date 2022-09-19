<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PengambilanSaprodilAllResource extends JsonResource
{
    private $data, $dataPetani;

    public function __construct($resource, $data, $dataPetani)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->data = $data;
        $this->dataPetani = $dataPetani;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = array();
        foreach ($this->data as $key => $value) {
            $temp_data = array(
                'pembiayaan_id' => $value->pembiayaan_id,
                'proses_tanam_desc' => $value->proses_tanam_desc,
                'proses_tanam_nama' => $value->proses_tanam_nama,
                'periode_kegiatan_start' => $value->periode_kegiatan_start,
                'periode_kegiatan_end' => $value->periode_kegiatan_end,
                'lahan_id' => $value->lahan_id,
                'kesiapan_kegiatan_st' => $value->kesiapan_kegiatan_st,
                'kios_nama' => $value->kios_nama,
                'alamat' => $value->alamat,
                'item' => array(),
            );
            array_push($result, $temp_data);
            foreach ($this->dataPetani as $k => $d) {
                if ($value->proses_tanam_nama == $d->proses_tanam_nama) {
                    $arr_item = array(
                        'nama_item' => $d->nama_item,
                        'jumlah' => $d->jumlah,
                        'pengambilan_st' => $d->pengambilan_st,
                        'kesiapan_stok_st' => $d->kesiapan_stok_st,
                    );
                    array_push($result[$key]['item'], $arr_item);
                }
            }
        }
        return $result;
    }
}
