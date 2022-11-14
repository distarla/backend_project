<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;
use App\Models\Price;

class PriceController extends Controller
{
    public function __construct(Price $price)
    {
        $this->price=$price;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //api/prices?attr=id,value,...
        if ($request->has('attr_menu')) {
            $attr_menu=$request->attr_menu;
        }
        if ($request->has('attr_range')) {
            $attr_range=$request->attr_range;
        }
        if ($request->has('attr_events')) {
            $attr_events=$request->attr_events;
        }
        $menu = $request->has('attr_menu') ? 'menu:id,'.$attr_menu : 'menu';
        $range = $request->has('attr_range') ? 'range:id,'.$attr_range : 'range';
        $event = $request->has('attr_events') ? 'events:id,'.$attr_events : 'events';
        $prices=$this->price->with($menu, $range, $event);

        if ($request->has('attr')) {
            //with tem de ter o atributo 'menu_id', 'range_id', 'events_id' nos attr caso contrário devolve nulo
            $prices=$prices->selectRaw($request->attr)->get();
        } else {
            $prices=$prices->get();
        }
        return response()->json($prices,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->price->regras(),$this->price->feedback());

        $price= $this->price->create($request->all());
        return response()->json($price,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $price=$this->price->with('menu', 'range', 'events')->find($id);
        if ($price===null)
            return response()->json(["erro"=>"O Preço pesquisado não existe!"],404);
        else
            return response()->json($price,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $price=$this->price->find($id);
        if ($price===null)
            return response()->json(["erro"=>"O Preço pesquisado não existe!"],404);
        else {
            if ($request->method() === 'PATCH') {
                $regrasDinamicas=array();

                //Percorrer todas as regras do Model
                foreach($price->regras() as $input=>$regra)  {
                    //adiciona no array regrasdinamicas as regras correspondentes aos campos submetidos
                    if(array_key_exists($input,$request->all()))
                        $regrasDinamicas[$input]=$regra;
                }
                $request->validate($regrasDinamicas,$this->price->feedback());
            }
            else
                $request->validate($this->price->regras($id),$this->price->feedback());

            $price->fill($request->all());
            $price->save();
            return response()->json($price,200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price=$this->price->find($id);
        if ($price===null)
            return response()->json(["erro"=>"O Preço pesquisado não existe!"],404);
        else {
            $price->delete();
            return response()->json(["msg"=>"O Preço foi apagado com sucesso!"],200);;
        }
    }
}
