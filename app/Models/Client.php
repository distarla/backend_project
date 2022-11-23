<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable=['name','address', 'zip', 'city', 'country', 'phone', 'email', 'idCard', 'expiry', 'nif', 'event_id'];

    public function regras($id=-1) {
        return [
            "name"=>"required",
            "email"=>"email:strict",
            "idCard"=>"required|unique:clients,idCard,$id",
            "expiry"=>"date_format:Y-m-d"
        ];
    }

    public function feedback() {
        return [
            "required"=>"O campo :attribute é obrigatório",
            "email"=>"O Email indicado não é válido",
            "idCard.unique"=>"O BI indicado já existe",
            "expiry"=>"A data de validade não é válida"
        ];
    }

    /**
     * The events that belong to the client.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class,'schedules', 'client_id', 'event_id')->withTimestamps();
    }
}
