<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $model = Articulo::where('estado', 1)->get();
         $list = [];
         foreach ($model as $m) {
             $list[] = $this->kardex($m);
         }
         return $list;
        //return Articulo::with(['Marca','Medida','Categoria'])->where('estado',1)->get();
    }
    public function kardex(Articulo $articulo)
    {
        
        $articulo->marca = $articulo->Marca;
        $articulo->medida = $articulo->Medida;
        $articulo->categoria = $articulo->Categoria;
        $articulo->image = $articulo->ArticuloImages()->get()->first();
        if( $articulo->image!=null){
            $articulo->image->url = $articulo->image->image->UrlImage();
        }
        $articulo->inventarios = $articulo->Inventarios()->where('estado', 1)->get();
        $articulo->ingresos = $articulo->inventarios->where('tipo', 1)->sum('cantidad');
        $articulo->egresos = $articulo->inventarios->where('tipo', 2)->sum('cantidad');
        $articulo->stock = $articulo->ingresos - $articulo->egresos;
        $articulo->valorizado = $articulo->stock * $articulo->venta;
        $articulo->inversion = $articulo->stock * $articulo->compra;
        $articulo->ganancia = $articulo->valorizado - $articulo->inversion;
        return $articulo;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inventario = new Inventario();
        $inventario->articulo_id = $request->articulo_id;
        $inventario->tipo = $request->tipo;
        $inventario->compra = $request->compra;
        $inventario->venta = $request->venta;
        $inventario->cantidad = $request->cantidad;
        $inventario->motivo = $request->motivo;
        $inventario->save();
        return $inventario;
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventario $inventario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
        //
        $inventario->estado = 0;
        $inventario->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventario $inventario)
    {
        //Desactivar registro
        $inventario->estado = 0;
        $inventario->save();
    }
}
