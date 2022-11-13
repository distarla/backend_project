<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The events that belong to the client.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class,'schedules', 'client_id', 'event_id');
    }
}
