<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desafio;

class DesafioController extends Controller
{
    public function index()
    {
        return Desafio::where('activo', true)->get();
    }
}
