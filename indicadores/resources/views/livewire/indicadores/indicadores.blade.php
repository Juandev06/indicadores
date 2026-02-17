<div>
    @include('layouts.theme.header')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @component('common.contentHeader')
                @slot('pageTitle')
                    {{ $pageTitle }}
                @endslot
                @slot('pageSubtitle')
                    Cantidad de indicadores: {{ $indicadores->total() }}
                @endslot
            @endcomponent
            <div class="content-body card p-1">
                <div class="row no-print">
                    <div class="col-12 col-sm-8 d-flex justify-content-start mb-2">
                        @include('common.searchbox')
                        @can($permisoEditarModulo)
                            <div>
                                <button id="myBtn" class="btn btn-dark d-flex flex-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#theModal">
                                    <i class="ico-plus-circle"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="show-print text-center mb-1">
                    <span class="fs-24">{{ $pageTitle }}</span> <br>
                    <span class="fs-14">Cantidad de indicadores: {{ $indicadores->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Periodicidad</th>
                                <th class="text-center">Area</th>
                                <th class="text-center">Usuario</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center no-print">Fórmula</th>
                                <th class="text-center no-print">Ficha Técnica</th>
                                @can($permisoEditarModulo)
                                    <th class="text-center no-print">Acciones</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @if ($indicadores->count() <= 0)
                                <tr>
                                    <td colspan="10">No hay registros.</td>
                                </tr>
                            @endif
                            @foreach ($indicadores as $indicador)
                                <tr>
                                    <td>{{ $indicador->nombre }}</td>
                                    <td>{{ $indicador->id }}</td>
                                    <td>{{ $indicador->tipo == 'P' ? '%' : 'N°' }}</td>
                                    <td>{{ $indicador->periodo }}</td>
                                    <td>{{ $indicador->area }}</td>
                                    <td>{{ $indicador->userName }}</td>
                                    <td class="text-center">
                                        <div class="td-content">
                                            <span
                                                class="{{ $indicador->estado == 'A' ? 'badge bg-success' : 'badge bg-danger' }}">
                                                {{ $indicador->estado == 'A' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center no-print">
                                        <a href="javascript:void(0)" wire:click="Formula({{ $indicador->id }})"
                                            class="btn btn-dark btn-sm" title="Formula">
                                            <i class="ico-fx"></i> </a>
                                    </td>
                                    <td class="text-center no-print">
                                        <button class="btn btn-dark btn-sm"
                                            wire:click="AbrirCargaArchivo({{ $indicador->id }})">
                                            <span class="fa-solid fa-file-pdf"></span>
                                        </button>
                                    </td>
                                    @can($permisoEditarModulo)
                                        <td class="text-center no-print">
                                            <div class="d-flex flex-row flex-nowrap align-items-start">
                                                <a href="javascript:void(0)" wire:click="Edit({{ $indicador->id }})"
                                                    class="btn btn-dark btn-sm mr-05" title="Editar">
                                                    <i class="ico-edit"></i>
                                                </a>
                                                <a href="javascript:void(0)" onClick="Confirm('{{ $indicador->id }}')"
                                                    class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="ico-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $indicadores->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('livewire.indicadores.form')
    @include('livewire.indicadores.formulas')
    @include('livewire.indicadores.fichaTecnica')
    @include('livewire.indicadores.formulaVariables')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('show-modal', msg => {
                $('#theModal').modal('show');
            });
            window.livewire.on('indicador-ok', msg => {
                $('#theModal').modal('hide');
                noty(msg);
            });
            window.livewire.on('msg-ok', msg => {
                noty(msg);
            });
            window.livewire.on('error-categorias', msg => {
                setError(msg);
            })
            window.livewire.on('hidde-modal', msg => {
                $('#theModal').modal('hide');
            });
            window.livewire.on('open-formula', msg => {
                $('#formulaModal').modal('show');
            });
            window.livewire.on('formula-updated', msg => {
                $('#formulaModal').modal('hide');
                noty(msg);
            });
            window.livewire.on('open-contegories', msg => {
                $('#categoryModal').modal('show');
            });
            window.livewire.on('close-modal', msg => {
                $('#formulaModal').modal('hide')
                $('#categoryModal').modal('hide')
            });
            // Ficha técnica
            window.livewire.on('show-ficha-tec', msg => {
                $('#fichaTecnica').modal('show');
            });
            window.livewire.on('upload-ok', msg => {
                $('#fichaTecnica').modal('hide');
                noty(msg);
            });
            // modal de variables
            window.livewire.on('show-variables', msg => {
                $('#buscarVariables').modal('show');
            });
            window.livewire.on('variables-ok', msg => {
                $('#buscarVariables').modal('hide');
            });
        });

        //Funcion para confirmar la eliminacion del registro emitiendo el id a eliminar.
        function Confirm(id) {
            new swal({
                title: '¿Está seguro que desea eliminar el indicador?',
                html: '<h4 style="color:#f00">Esta acción no se puede deshacer</h4>',
                icon: 'error',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#3b3f5c',
                confirmButtonText: 'Eliminar',
                confirmButtonColor: '#f00'
            }).then(function(result) {
                if (result.value) {
                    window.livewire.emit('deleteRow', id);
                    swal.close();
                };
            });
        }

        // muestra alerta cuando el usuario cambia el periodo, tipo de resultado (numérico o porcentual),
        // tendencia o tolerancia de un indicador ya existente
        function alertaCambioPeriodo() {
            new swal({
                title: 'Cuidado',
                html: 'Si cambia este campo del indicador, <span class="text-danger"><b>se recalcularán todos los resultados y se eliminarán los análisis<b><span>',
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3b3f5c'
            });
        }

        // funciones fórmulas 
        var _numeros = '0123456789';
        var _operadores = '/*-+^';
        var _cadenas = 'abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ_:123456789 ';

        function rtrim(s, que) {
            var j = 0;
            // Busca el último caracter segun el especificado
            for (var i = s.length - 1; i > -1; i--)
                if (s.substring(i, i + 1) != que) {
                    j = i;
                    break;
                }
            return s.substring(0, j + 1);
        }

        function InsertarVariable() {
            console.log('inicio');
            var btnAct = event.target;
            // obtener el nombre de la variable y quitarle paréntesis
            var txtFormula = btnAct.getAttribute('nombreVar')
                .replace(/\s{2,}/g, ' ') // reemplazar espacios de exceso
                .trim(); // limpiar espacios al inicio y al final
            var valor = '{' + txtFormula + ' :' + btnAct.getAttribute('idVar') + '}';
            var ca = CaracterAntesCursor('txtFormula');
            var cd = CaracterDespuesCursor('txtFormula');

            if (ca == '}' || cd == '{') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre dos campos',
                })
                //alert('Debe haber un operador entre dos campos');
                return false;
            } else if (ca == ')' || cd == '(') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre un campo y una expresión',
                })
                //alert('Debe haber un operador entre un campo y una expresión');
                return false;
            } else if ((ca != '' && ca != null && _numeros.indexOf(ca) > -1) || (cd != '' && cd != null && _numeros.indexOf(
                    cd) > -1)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre un campo y un número',
                })
                //alert('Debe haber un operador entre un campo y un número');
                return false;
            } else if (ca == '{' || cd == '}' ||
                (cd != null && cd != '' && _cadenas.indexOf(cd) != -1) ||
                (ca != null && ca != '' && _cadenas.indexOf(ca) != -1)) {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con esta acción se dañaría almenos un campo, operación cancelada',
                })
                //alert('Con esta acción se dañaría almenos un campo, operación cancelada');
                return false;
            }
            InsEnCursor('txtFormula', valor);
            jQuery('#buscarVariables').modal('hide');
        }

        function InsertarOperador(cual) {
            var ca = CaracterAntesCursor('txtFormula');
            var cd = CaracterDespuesCursor('txtFormula');

            if (ca == '(' || ca == '' || ca == null || _operadores.indexOf(ca) > -1) {
                cual = '' + cual;
            }

            if (cd == ')' || cd == '' || cd == null || _operadores.indexOf(cd) > -1) {
                cual = cual + '';
            }

            InsEnCursor('txtFormula', cual);
        }

        function Borrar() {
            var ca = CaracterAntesCursor('txtFormula');
            var cd = CaracterDespuesCursor('txtFormula');

            if (ca == '{' &&
                (cd != null && cd != '' && _cadenas.indexOf(cd) == -1)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                //alert('Con este borrado se dañaría la expresión, operación cancelada');
                return false;
            } else if (cd == '}' &&
                (ca != null && ca != '' && (_cadenas.indexOf(ca) == -1))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })

                //alert('Con este borrado se dañaría la expresión, operación cancelada');
                return false;
            } else if ((ca != null && ca != '' && _cadenas.indexOf(ca) != -1) &&
                (cd == null || cd == '' || _cadenas.indexOf(cd) == -1)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                //alert('Con este borrado se dañaría la expresión, operación cancelada');
                return false;
            } else if ((cd != null && cd != '' && _cadenas.indexOf(cd) != -1) &&
                (ca == null || ca == '' || _cadenas.indexOf(ca) == -1)) {
                //alert('Con este borrado se dañaría la expresión, operación cancelada');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            } else if (cd != null && cd != '' && _operadores.indexOf(cd) != -1 &&
                (ca == '{' || ca == '(' ||
                    (ca != null && ca != '' && (_cadenas.indexOf(ca) != -1 || _operadores.indexOf(ca) != -1)))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            } else if (ca != null && ca != '' && _operadores.indexOf(ca) != -1 &&
                (cd == '}' || cd == ')' ||
                    (cd != null && cd != '' && (_cadenas.indexOf(cd) != -1 || _operadores.indexOf(cd) != -1)))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            } else if ((cd == null || cd == '') &&
                (ca == '{' || ca == '(' ||
                    (ca != null && ca != '' && (_cadenas.indexOf(ca) != -1 || _operadores.indexOf(ca) != -1)))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            } else if ((ca == null || ca == '') &&
                (cd == '}' || cd == ')' ||
                    (cd != null && cd != '' && (_cadenas.indexOf(cd) != -1 || _operadores.indexOf(cd) != -1)))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            }

            var s = Seleccionado('txtFormula');
            if (!ValidarParentesis(s)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con este borrado se dañaría la expresión, operación cancelada',
                })
                return false;
            }

            InsEnCursor('txtFormula', '');
        }

        function InsertarParentesis() {
            var campo = document.getElementById('txtFormula');

            var ca = CaracterAntesCursor('txtFormula');
            var cd = CaracterDespuesCursor('txtFormula');

            if (ca == '}' || cd == '{') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre un campo y una expresión',
                })
                //alert('Debe haber un operador entre un campo y una expresión');
                return false;
            } else if (ca == ')' || cd == '(') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre dos expresiones'
                })
                //alert('Debe haber un operador entre dos expresiones');
                return false;
            } else if ((ca != null && ca != '' && _cadenas.indexOf(ca) != -1) ||
                (cd != null && cd != '' && _cadenas.indexOf(cd) != -1) ||
                ca == '{' || cd == '}') {
                //alert('Con esta acción se dañaría almenos un campo, operación cancelada');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Con esta acción se dañaría almenos un campo, operación cancelada'
                })
                return false;
            } else if ((ca != null && ca != '' && _numeros.indexOf(ca) != -1) ||
                (cd != null && cd != '' && _numeros.indexOf(cd) != -1)) {
                //alert('Debe haber un operador entre un número y una expresión');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber un operador entre un número y una expresión'
                })
                return false;
            }

            //IE support
            if (document.selection) {
                campo.focus();
                sel = document.selection.createRange();

                if (sel.text == null || sel.text == '') {
                    valor = '';
                } else if (_operadores.indexOf(sel.text.substr(0, 1)) != -1) {
                    valor = '' + sel.text;
                } else {
                    valor = sel.text;
                }

                sel.text = '(' + valor + ')';
            }
            //MOZILLA/NETSCAPE support
            else if (campo.selectionStart || campo.selectionStart == '0') {
                var startPos = campo.selectionStart;
                var endPos = campo.selectionEnd;
                var valor = '';

                if ((endPos - startPos) > 0) {
                    valor = campo.value.substring(startPos, endPos);
                }

                campo.value = campo.value.substring(0, startPos) +
                    '(' +
                    valor +
                    ')' +
                    campo.value.substring(endPos, campo.value.length);
            } else {
                campo.value = campo.value + '()';
            }
        }
        // insertar valor absoluto
        function InsertarAbs() {
            var campo = document.getElementById('txtFormula');
            //IE support
            if (document.selection) {
                campo.focus();
                sel = document.selection.createRange();
                sel.text = 'abs()';
            }
            //MOZILLA/NETSCAPE support
            else if (campo.selectionStart || campo.selectionStart == '0') {
                var startPos = campo.selectionStart;
                var endPos = campo.selectionEnd;
                var valor = '';

                campo.value = campo.value.substring(0, startPos) + 'abs()' +
                    campo.value.substring(endPos, campo.value.length);
            } else {
                campo.value = campo.value + 'abs()';
            }
        }

        function InsEnCursor(idcampo, valor) {
            var campo = document.getElementById(idcampo);
            if (campo.selectionStart || campo.selectionStart == '0') {
                var startPos = campo.selectionStart;
                var endPos = campo.selectionEnd;
                valorAct = campo.value.substring(0, startPos) + valor + campo.value.substring(endPos, campo.value.length);
            } else {
                valorAct = campo.value += valor;
            }
            @this.formulaStored = valorAct;
        }

        function Seleccionado(idcampo) {
            var campo = document.getElementById(idcampo);
            //IE support
            if (document.selection) {
                campo.focus();
                sel = document.selection.createRange();
                return sel.text;
            }
            //MOZILLA/NETSCAPE support
            else if (campo.selectionStart || campo.selectionStart == '0') {
                var startPos = campo.selectionStart;
                var endPos = campo.selectionEnd;
                return campo.value.substring(startPos, endPos);
            } else {
                return campo.value;
            }
        }

        function CaracterAntesCursor(idcampo) {
            var campo = document.getElementById(idcampo);
            //IE support
            if (document.selection) {
                campo.focus();
                sel = document.selection.createRange();
                sel.moveStart("character", -1);

                sel2 = document.selection.createRange();

                //Si se había seleccionado desde el principio de texto
                if (sel.text == sel2.text) {
                    return null;
                }
                return sel.text.substr(0, 1);
            }
            //MOZILLA/NETSCAPE support
            else if (campo.selectionStart || campo.selectionStart == '0') {
                var startPos = campo.selectionStart;

                if (startPos == 0) {
                    return null;
                }

                return campo.value.substr(startPos - 1, 1);
            }

            return null;
        }

        function CaracterDespuesCursor(idcampo) {
            var campo = document.getElementById(idcampo);
            //IE support
            if (document.selection) {
                campo.focus();
                sel = document.selection.createRange();
                sel.moveStart("character", sel.text.length);
                sel.moveEnd("character", 1);
                return sel.text.substr(0, 1);
            }
            //MOZILLA/NETSCAPE support
            else if (campo.selectionStart || campo.selectionStart == '0') {
                var endPos = campo.selectionEnd;

                if (endPos == campo.value.length - 1) {
                    return null;
                }

                return campo.value.substr(endPos, 1);
            }

            return null;
        }

        function ValidarParentesis(txtFormula) {
            var i;
            var parentesisAbiertos = 0;
            for (i = 0; i < txtFormula.length; i++) {
                if (txtFormula.substring(i, i + 1) == '(') {
                    parentesisAbiertos++;
                } else if (txtFormula.substring(i, i + 1) == ')') {
                    parentesisAbiertos--;
                }
            }
            // valida si hay paréntesis abiertos
            return parentesisAbiertos == 0;
        }

        function Validar() {
            var txtFormula = document.getElementById('txtFormula').value;
            var respuesta = {
                ok: 'success',
                msg: ''
            };
            // validar paréntesis y campos de variables
            var parentesisAbiertos = 0; // ()
            var camposVariablesAbiertos = 0; // {}
            var campoCierreVariables = ''; // :}
            for (var i = 0; i < txtFormula.length; i++) {
                var caracterActual = txtFormula.substring(i, i + 1);
                // validar paréntesis
                parentesisAbiertos += caracterActual == '(' ? 1 : 0;
                parentesisAbiertos -= caracterActual == ')' ? 1 : 0;
                // validar llaves, para campos de variables
                camposVariablesAbiertos += caracterActual == '{' ? 1 : 0;

                campoCierreVariables = campoCierreVariables == ':}' ? '' : campoCierreVariables;
                campoCierreVariables += (caracterActual == ':' || caracterActual == '}') ? caracterActual : '';
                camposVariablesAbiertos -= campoCierreVariables == ':}' ? 1 : 0;
            }
            if (txtFormula == '') {
                respuesta.ok = 'error';
                respuesta.msg = 'La fórmula no puede estar vacía';
            } else if (parentesisAbiertos != 0) {
                respuesta.ok = 'error';
                respuesta.msg = 'La fórmula tiene paréntesis sin cerrar';
            } else if (camposVariablesAbiertos != 0) {
                respuesta.ok = 'error';
                respuesta.msg = 'Ha ingresado varibles incorrectamente';
            } else {
                var caracterActual = '';
                var estado = 0;
                // quitar la palabra "abs" si utilizan la función abs()
                var txtFormulaAj = txtFormula.replace(/abs/g, '').replace(/\{([^{}]*)\}/g, '0');
                for (var i = 0; i < txtFormulaAj.length; i++) {
                    caracterActual = txtFormulaAj.length < i ? null : txtFormulaAj.substr(i, 1);
                    switch (estado) {
                        case 0:
                            switch (true) {
                                case EsNumero(caracterActual):
                                    estado = 2;
                                    continue;
                                case caracterActual == '-':
                                    estado = 3;
                                    continue;
                                case caracterActual == '(':
                                    estado = 0;
                                    continue;
                                case caracterActual == '{':
                                    estado = 1;
                                    continue;
                            }
                            console.log('err0r 0');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: 0-9, -, (, {' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 1:
                            switch (true) {
                                case EsLetra(caracterActual):
                                    estado = 7;
                                    continue;
                            }
                            console.log('err0r 1');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: a-z, A-Z, _ ' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 2:
                            switch (true) {
                                case EsNumero(caracterActual):
                                    estado = 2;
                                    continue;
                                case caracterActual == '-' || EsOperador(caracterActual):
                                    estado = 3;
                                    continue;
                                case caracterActual == ')':
                                    estado = 4;
                                    continue;
                                case caracterActual == '.':
                                    estado = 5;
                                    continue;
                            }
                            console.log('err0r 2');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: 0-9, ), ., -, *, +, /, ^' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 3:
                            switch (true) {
                                case EsNumero(caracterActual):
                                    estado = 2;
                                    continue;
                                case caracterActual == '(':
                                    estado = 0;
                                    continue;
                                case caracterActual == '{':
                                    estado = 1;
                                    continue;
                            }
                            console.log('err0r 3');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: 0-9, (, {' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 4:
                            switch (true) {
                                case caracterActual == '-' || EsOperador(caracterActual):
                                    estado = 3;
                                    continue;
                                case caracterActual == ')':
                                    estado = 4;
                                    continue;
                            }
                            console.log('err0r 4');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: ), -, *, +, /, ^' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 5:
                            switch (true) {
                                case EsNumero(caracterActual):
                                    estado = 6;
                                    continue;
                            }
                            console.log('err0r 5');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual + '", se esperaba uno de los siguientes: 0-9' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 6:
                            switch (true) {
                                case EsNumero(caracterActual):
                                    estado = 6;
                                    continue;
                                case caracterActual == '-' || EsOperador(caracterActual):
                                    estado = 3;
                                    continue;
                                case caracterActual == ')':
                                    estado = 4;
                                    continue;
                            }
                            console.log('err0r 6');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: 0-9, ), -, *, +, /, ^' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                        case 7:
                            switch (true) {
                                case EsLetra(caracterActual):
                                    estado = 7;
                                    continue;
                                case caracterActual == '}':
                                    estado = 4;
                                    continue;
                            }
                            console.log('err0r 7');
                            respuesta.ok = 'error';
                            respuesta.msg = 'Se encontró "' + caracterActual +
                                '", se esperaba uno de los siguientes: a-z, A-Z, _, }' +
                                ' \n en la posición : ' + (i + 1) + '';
                            break;
                    }
                }

                if (estado != 2 && estado != 4 && estado != 6) {
                    respuesta.ok = 'error';
                    respuesta.msg = 'La formula se encuentra incompleta';
                }
            }
            if (respuesta.ok == 'success') {
                window.livewire.emit('StoreFormula');
            } else {
                Swal.fire({
                    icon: respuesta.ok,
                    title: 'Fórmula con errores',
                    text: respuesta.msg
                })
            }
            return true;
        }

        function EsNumero(c) {
            if (_numeros.indexOf(c) != -1) {
                return true;
            }
            return false;
        }

        function EsLetra(c) {
            if (_cadenas.indexOf(c) != -1) {
                return true;
            }
            return false;
        }

        function EsOperador(c) {
            if (_operadores.indexOf(c) != -1) {
                return true;
            }
            return false;
        }
    </script>
</div>
