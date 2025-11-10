<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';
    
    protected $fillable = [
        'nombre',
    ];
}
