<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/paquetes', function () {
    // $sendTypesList = \Illuminate\Support\Facades\DB::select("select [PQE_CONTAINID],[PQE_NOMBRE] from dbo.PQEV_TIPOSENVIOPAQT");
    // $categoriesList = \Illuminate\Support\Facades\DB::select("SELECT [IEX_ID],[IEX_DESC],[IEX_NOMBRE] FROM [FEDEX].[dbo].[IMEX_CATEGORIAPAQT]");
    $packagesList = \Illuminate\Support\Facades\DB::select("SELECT [PQT_ID],[PQT_NOMBRE],[PQT_DESCRIPCION],[PQT_SUCURINI],[PQT_SUCURFIN],[PQT_VALORTOTAL],[PQT_FECHA],[PQT_ESTADO],[PQT_TIPOPAQUETE] FROM [FEDEX].[dbo].[PQEV_PAQUETES]");
    $officesList = \Illuminate\Support\Facades\DB::select("SELECT IEP_NOMBRE,IEP_ID,IEP_PAIS,IEP_LONGITUD,IEP_LATITUD,IEP_CODPOSTAL,IEP_DEPARTAMENTO  FROM [FEDEX].[dbo].[IMEX_SUCURSAL]");
    $packageTypesList = \Illuminate\Support\Facades\DB::select("SELECT TOP (1000) [PQE_ID],[PQE_DESC],[PQE_IDTIPOCONTAIN],[PQE_IDCATEGORIAIN] FROM [FEDEX].[dbo].[PQEV_TIPOPAQT]");

    $packagesList = collect($packagesList);
    $officesList = collect($officesList);
    $packageTypesList = collect($packageTypesList);

    foreach ($packagesList as $package) {
        foreach ($officesList as $office) {
            if ($office->IEP_ID == $package->PQT_SUCURINI) {
                $package->PQT_SUCURINI = $office->IEP_NOMBRE;
            }

            if ($office->IEP_ID == $package->PQT_SUCURFIN) {
                $package->PQT_SUCURFIN = $office->IEP_NOMBRE;
            }
        }
    }

    return view('packages.home', compact('packagesList', 'officesList', 'packageTypesList'));
});

Route::post('/paquetes', function (\Illuminate\Http\Request $request) {
    $name = $request->get('name');
    $description = $request->get('description');
    $startOffice = $request->get('startOffice');
    $endOffice = $request->get('endOffice');
    $totalValue = $request->get('totalValue');
    $state = 1;
    $packageType =$request->get('packageType');

    \Illuminate\Support\Facades\DB::insert("INSERT INTO [dbo].[PQEV_PAQUETES]
           ([PQT_NOMBRE]
           ,[PQT_DESCRIPCION]
           ,[PQT_SUCURINI]
           ,[PQT_SUCURFIN]
           ,[PQT_VALORTOTAL]
           ,[PQT_FECHA]
           ,[PQT_ESTADO]
           ,[PQT_TIPOPAQUETE])
     VALUES
           (?, ?, ?, ?, ?, ?, ?, ?)",
        [$name, $description, $startOffice, $endOffice, $totalValue, date("Y-m-d"), $state, $packageType]);

    $response = [
        'success' => true,
        'response' => '',
        'message' => ''
    ];

    return json_encode($response);
});
