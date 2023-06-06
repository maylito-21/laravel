<?php

namespace App\Http\Controllers;
use App\Models\Articulo;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Venta_inventario;
//use App\Models\VentaInventario;
use App\Models\User;
use App\Models\Venta;
use App\Models\Compra;
//use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function info(){
        return [
            "articulos"=>Articulo::where('estado',1)->get()->count(),
            "marcas"=>Marca::where('estado',1)->get()->count(),
            "categorias"=>Categoria::where('estado',1)->get()->count(),
            "usuarios"=>User::where('estado',1)->get()->count(),
            "ventas"=>Venta::where('estado',1)->get()->sum('total'),
            "compras"=>Compra::where('estado',1)->get()->sum('total'),
            "meses"=>$this->VentasMensual(),
            "mesesCompra"=>$this->ComprasMensual(),
            "masVendido"=>$this->masVendidos(),


        ];
    }
    // public function VentasMensual()
    // {
    //     $ventas= Venta::select(
    //         DB::raw('sum(total) as total'),
    //         DB::raw("DATE_FORMAT(created_at,'%M %Y') as mes")
    //     )->where('estado',1)
    //         ->groupBy("mes")
    //         ->orderBy('mes', 'DESC')
    //         ->get();
    //         return $ventas;
    // }

    public function VentasMensual()
    {
        $venta= DB::select(
            
            'SELECT 
            TMeses.mes,
            T1.total 
            FROM
             (SELECT 1 as IdMes , "Enero"     as mes UNION
             SELECT 2 as IdMes , "Febrero"    as mes UNION
             SELECT 3 as IdMes , "Marzo"      as mes UNION
             SELECT 4 as IdMes , "Abril"      as mes UNION
             SELECT 5 as IdMes , "Mayo"       as mes UNION
             SELECT 6 as IdMes , "Junio"      as mes UNION
             SELECT 7 as IdMes , "Julio"      as mes UNION
             SELECT 8 as IdMes , "Agosto"     as mes UNION
             SELECT 9 as IdMes , "Septiembre" as mes UNION
             SELECT 10 as IdMes, "Octubre"    as mes UNION
             SELECT 11 as IdMes, "Noviembre"  as mes UNION
             SELECT 12 as IdMes, "Diciembre"  as mes) TMeses
            LEFT JOIN
                (SELECT 
            SUM(total) AS total,            
            MONTH(created_at) AS mes
            
            
            FROM `ventas` 
            WHERE 
            estado = 1
            AND YEAR(created_at) = YEAR(CURRENT_DATE())

            
            GROUP BY
            MONTH(created_at)
            
            ORDER BY
            MONTH(created_at)) T1
            ON T1.Mes = TMeses.idMes

       --     WHERE
       --     T1.total  IS NOT NULL
            
            ' );

        return $venta;

    }

    public function ComprasMensual()
    {
        $compra= DB::select(
            
            'SELECT 
            TMeses.mes,
            T1.total 
            FROM
             (SELECT 1 as IdMes , "Enero"     as mes UNION
             SELECT 2 as IdMes , "Febrero"    as mes UNION
             SELECT 3 as IdMes , "Marzo"      as mes UNION
             SELECT 4 as IdMes , "Abril"      as mes UNION
             SELECT 5 as IdMes , "Mayo"       as mes UNION
             SELECT 6 as IdMes , "Junio"      as mes UNION
             SELECT 7 as IdMes , "Julio"      as mes UNION
             SELECT 8 as IdMes , "Agosto"     as mes UNION
             SELECT 9 as IdMes , "Septiembre" as mes UNION
             SELECT 10 as IdMes, "Octubre"    as mes UNION
             SELECT 11 as IdMes, "Noviembre"  as mes UNION
             SELECT 12 as IdMes, "Diciembre"  as mes) TMeses
            LEFT JOIN
                (SELECT 
            SUM(total) AS total,            
            MONTH(created_at) AS mes
            
            
            FROM `compras` 
            WHERE 
            estado = 1
            AND YEAR(created_at) = YEAR(CURRENT_DATE())

            
            GROUP BY
            MONTH(created_at)
            
            ORDER BY
            MONTH(created_at)) T1
            ON T1.Mes = TMeses.idMes

        --    WHERE
         --   T1.total  IS NOT NULL
            
            ' );

        return $compra;

    }

    public function masVendidos()
    {
        $mvendido= DB::select(
            
        'SELECT 
        m.nombre AS nMarca,
        a.nombre AS nArticulo,
        FORMAT(SUM(i.cantidad),0) AS cantidad,
        SUM(i.venta) AS total,
        (SELECT SUM(vv.total) FROM ventas AS vv WHERE vv.estado=1) AS tt,
        (SELECT SUM(ii.cantidad) FROM venta_inventarios AS ii WHERE ii.estado=1) AS tcantidad
 



                FROM
        marcas AS m
        INNER JOIN articulos AS a ON a.marca_id=m.id AND a.estado=1
        INNER JOIN inventarios AS i ON i.articulo_id=a.id AND i.estado=1 AND i.tipo=2
        INNER JOIN venta_inventarios AS vi ON vi.inventario_id=i.id AND vi.estado=1 
        INNER JOIN ventas AS v ON v.id=vi.venta_id AND v.estado=1 

        WHERE 
        m.estado=1
   


        GROUP BY 
        m.nombre,
        a.nombre

        ORDER BY SUM(i.cantidad) DESC


        LIMIT 10




            
            ' );

        return $mvendido;

    }


    
   
}
