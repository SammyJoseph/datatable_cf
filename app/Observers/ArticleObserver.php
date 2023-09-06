<?php

namespace App\Observers;

/* registrar este observer en el archivo app/Providers/EventServiceProvider.php */
class ArticleObserver
{
    public function creating(\App\Models\Article $article) // se ejecuta al crear un artículo
    {
        /* $article es el artículo que se está creando
        Guardar en la propiedad sort el último valor de sort + 1 */
        $article->sort = \App\Models\Article::max('sort') + 1;
    }
}
