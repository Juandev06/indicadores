<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AnalisisIndicadoresExport implements FromCollection, WithHeadings //, ShouldAutoSize, WithStyles
{
    public
    $filtros; // [$año, $mes, $area]

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $anoSel = $this->filtros[0];
        $mesSel = $this->filtros[1] == 0 ? false : $this->filtros[1];
        $areaSel = $this->filtros[2] == 0 ? false : $this->filtros[2];

        return DB::table('indicadores AS ind')
            ->join('indicador_valores AS ind_val', 'ind.id', 'ind_val.id_indicador')
            ->join('metas', function ($join) {
                $join->on('metas.id_indicador', 'ind_val.id_indicador');
                $join->on('ind_val.mes', 'metas.mes');
            })
            ->join('users', 'users.id', 'ind.id_usuario')
            ->join('areas', 'areas.id', 'users.id_area')
            ->select(DB::raw(
                'areas.name as area_name,
                concat(users.name, " ", users.lastName) as usuario,
                ind.nombre as ind_name,
                ind_val.ano as ano,
                ind_val.mes as mes,
                metas.value as meta_vlr,
                ind_val.valor as ind_vlr,
                ind.tolerancia,
                if(ind.tendencia = 1, "Asc", "Desc") as tendencia,
                if(ind.tipo = "P", "%", "#") as tipo_ind,
                ind_val.obs as obs'
            )
            )
            ->when($mesSel, function ($query, $mesSel) {
                $query->where('ind_val.mes', $mesSel);
            })
            ->when($areaSel, function ($query, $areaSel) {
                $query->where('areas.id', $areaSel);
            })
            ->where('ano', $anoSel)
            ->orderBy('areas.name')
            ->orderBy('ind.nombre')
            ->orderBy('ind_val.mes')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Area',
            'Usuario',
            'Indicador',
            'Año',
            'Mes',
            'Meta',
            'Valor Ind.',
            'Tolerancia',
            'Tendencia',
            'Tipo',
            'Observaciones',
        ];

    }
/*
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:P1')->getFont()->setBold(true)->getColor()->setRGB('#551709');
        $sheet->getStyle('A1:P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCDCDC');
        
        $ultimafila = $this->totalRegistrosRep + 1;
        $rango = 'A1:P'.$ultimafila;
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        
    }
    */
}