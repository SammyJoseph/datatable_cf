<?php

namespace App\Exports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\FromCollection;

class ArticlesExport implements FromCollection
{
    public $articles;

    public function __construct($articles)
    {
        $this->articles = $articles; // recibe una colección de datos
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->articles; // devuelve una colección de datos
    }
}
