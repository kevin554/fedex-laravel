@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <button class="btn btn-sm btn-outline-secondary m-2" onclick="showPackageForm({{$officesList}}, {{$packageTypesList}})">Registrar un paquete</button>

            <div class="col-md-12">
                <div class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>paquete</th>
                                <th>descripcion</th>
                                <th>sucursal inicio</th>
                                <th>sucursal fin</th>
                                <th>valor total</th>
                                <th>fecha</th>
                            </tr>
                        </thead>
                        <tbody id="packageContainer">
                            <tr id="packageTemplate" style="display: none;">
                                <td>{PQT_NOMBRE}</td>
                                <td>{PQT_DESCRIPCION}</td>
                                <td>{PQT_SUCURINI}</td>
                                <td>{PQT_SUCURFIN}</td>
                                <td>{PQT_VALORTOTAL}</td>
                                <td>{PQT_FECHA}</td>
                            </tr>

                            @foreach($packagesList as $package)
                            <tr>
                                <td>{{$package->PQT_NOMBRE}}</td>
                                <td>{{$package->PQT_DESCRIPCION}}</td>
                                <td>{{$package->PQT_SUCURINI}}</td>
                                <td>{{$package->PQT_SUCURFIN}}</td>
                                <td>{{$package->PQT_VALORTOTAL}}</td>
                                <td>{{$package->PQT_FECHA}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
    </div>
    <script type="text/javascript">
        function showPackageForm(officesList, packageTypesList) {
            var officesNames = officesList.map( office => {
                return office.IEP_NOMBRE;
            });

            var packageTypesNames = packageTypesList.map( packageType => {
                return packageType.PQE_DESC;
            });

            swal.mixin({
                input: 'text',
                confirmButtonText: 'Next &rarr;',
                showCancelButton: true,
                progressSteps: ['1', '2', '3', '4', '5', '6']
            }).queue([
                {
                    title: 'Nombre'
                },
                {
                    title: 'Descripción'
                },
                {
                    title: 'Sucursal inicio',
                    text: '(de dónde sale)',
                    input: 'select',
                    inputOptions: officesNames,
                    inputValue: ''
                },
                {
                    title: 'Sucursal fin',
                    text: '(a dónde va)',
                    input: 'select',
                    inputOptions: officesNames
                },
                {
                    title: 'Valor total',
                },
                {
                    title: 'Tipo de paquete',
                    input: 'select',
                    inputOptions: packageTypesNames
                }
                // estado en 1
            ]).then((result) => {
                if (result.value) {
                    /* an array of strings */
                    var name = result.value[0];
                    var description = result.value[1];
                    var startOffice = result.value[2];
                    var endOffice = result.value[3];
                    var totalValue = result.value[4];
                    var packageType = result.value[5];

                    var params = {
                        _token: '{{csrf_token()}}',
                        name: name,
                        description: description,
                        startOffice: officesList[startOffice].IEP_ID,
                        endOffice: officesList[endOffice].IEP_ID,
                        totalValue: totalValue,
                        packageType: packageTypesList[packageType].PQE_ID
                    };

                    $.ajax({
                        url: '/paquetes',
                        method: 'POST',
                        data: params,
                        success: function(data) {
                            data = JSON.parse(data);

                            if (data.success) {
                                var objPackage = data.response;

                                var tr = $('<tr></tr>');

                                var packageTemplate = $('#packageTemplate').clone();

                                var html = packageTemplate.html();
                                html = html.replace('{PQT_NOMBRE}', name);
                                html = html.replace('{PQT_DESCRIPCION}', description);
                                html = html.replace('{PQT_SUCURINI}', officesList[startOffice].IEP_NOMBRE);
                                html = html.replace('{PQT_SUCURFIN}', officesList[endOffice].IEP_NOMBRE);
                                html = html.replace('{PQT_VALORTOTAL}', totalValue);

                                var date = new Date();
                                var day = date.getDate();
                                var month = date.getMonth() + 1;
                                var year = date.getFullYear();

                                var formattedDate =  year + '-' + month + '-' + day;

                                html = html.replace('{PQT_FECHA}', formattedDate);

                                tr.html(html);

                                $('#packageContainer').append(tr);
                            } else {
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: data.message
                                });
                            }
                        }, error(e) {
                            console.log(e);
                        }
                    });
                }
            });
        }
    </script>
@endsection
