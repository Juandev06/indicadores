@component('common.modal')
    @slot('modalTipo') show @endslot
    @slot('modalOpc') modal-lg @endslot
    @slot('modalTitulo') Indicadores asociados a: <br>{{ $nombreVariableAct }} @endslot
    @slot('modalId') listarIndicadores @endslot

    @slot('contenido')
        <div class="row">
            <div class="col-sm-12 mb-1">
            <table class="table table-sm table-bordered">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Cod</th>
                </tr>
            </thead>
            <tbody>
                @if($listaIndicadores->count() > 0)
                    @foreach ($listaIndicadores as $indicador)
                      <tr>
                        <td>{{ $indicador->nombre }}</td>
                        <td>{{ $indicador->id}}</td>
                      </tr>  
                    @endforeach
                @else
                    <tr>
                        <td colspan="2"><h6>No hay indicadores asociados a la variable</h6></td>
                    </tr>
                @endif
            </tbody>
            </table>
            </div>
        </div>
        <hr>
    @endslot

@endcomponent
