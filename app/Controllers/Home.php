<?php

namespace App\Controllers;

use App\Models\EtudiantModel;

class Home extends BaseController
{
    public function index(): string
    {
        $etudiant = new EtudiantModel();
        return view('pages/login', [
            'title' => 'EtuNote — Connexion',
            'useAppLayout' => false,
        ]);
    }
}
