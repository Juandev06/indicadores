<?php

namespace App\Exports;

use App\Models\Aux\PeriodoDets;
use App\Models\Variables\Variables;
use App\Models\Variables\VariableValores;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReporteVariablesExport implements FromCollection, WithHeadings //, ShouldAutoSize, WithStyles
{
    public
    $filtros; // [$idsVariables, $mes, $año]

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $variables = $this->filtros[0];
        $anoSel = $this->filtros[1];
        $mesSel = $this->filtros[2];

        // subquery para obtener los valores de las variables
        $valoresVariables = DB::table('variable_valores')
            ->select('valor', 'id_variable')
            ->where('ano', $anoSel)
            ->where('mes', $mesSel);
        
        // filtrar los periodos que aplican para el mes activo para ingreso de variables
        $periodosCalendario = PeriodoDets::select(DB::raw('concat(id_calendario,id_periodo) as cal_per'))
            ->where('mes', $mesSel)
            ->where('aplica', 1)
            ->get()
            ->pluck('cal_per')
            ->toArray();

        $datosVariables = Variables::select(DB::raw(
            'variables.id, 
            variables.nombre, 
            val_variables.valor,
            aux_periodos.nombre as periodo,
            concat(users.name, " ", users.lastName) as responsable',
        ))
            ->leftJoin('users', 'users.id', 'variables.id_usuario')
            ->leftJoin('aux_periodos', 'aux_periodos.id', 'variables.id_periodo')
            ->leftJoinSub($valoresVariables, 'val_variables', function ($join) {
                $join->on('val_variables.id_variable', 'variables.id');
            })
            ->whereIn(DB::raw('concat(id_calendario,id_periodo)'), $periodosCalendario)
            ->whereIn('variables.id', $variables)
            ->get();

            return $datosVariables;
    }

    public function headings(): array
    {
        return [
            'Código',
            'nombre',
            'Valor',
            'Periodo',
            'Responsable',
        ];
    }
}