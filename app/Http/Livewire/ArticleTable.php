<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class ArticleTable extends DataTableComponent
{
    // protected $model = Article::class; // se utilizará el método builder() en lugar del modelo

    public function configure(): void
    {
        $this->setPrimaryKey('id') // clave primaria de la tabla
            ->setTableRowUrl(function($row){ // url de toda la fila
                return route('dashboard', ['article' => $row->id]);
            })
            ->setTableRowUrlTarget(function(){ // abre la url en una nueva pestaña
                return '_blank';
            });
        $this->setDefaultSort('title', 'asc'); // orden por defecto
        
        $this->setSingleSortingDisabled(); // permite ordenar por diferentes columnas a la vez

        $this->setPageName('pag'); // nombre de la paginación

        $this->setPerPageAccepted([5, 20, 50, 100, -1]); // cantidad de registros por página (-1 para mostrar todos los registros)
        $this->setPerPage(5); // cantidad de registros por página (por defecto)
        
        // $this->setPerPageVisibilityStatus(false); // deshabilita la opción de elegir la cantidad de registros por página
        // $this->setPaginationStatus(false); // deshabilita la paginación
    }

    public function columns(): array
    {
        return [
            /* Column::make("Pos.", "sort")
                ->sortable(), */
            Column::make("Id")
                ->sortable()
                ->collapseOnTablet(), // oculta la columna en dispositivos móviles (tablets y celulares)
            
            /* esta columna no usa collapseOnTablet() para que se muestre en dispositivos móviles ↑↓ */
            Column::make("Título", "title")
                ->searchable()
                ->sortable()
                /* búsqueda personalizada ↓ */
                // ->searchable(fn($query, $searchTerm) => $query->orWhere('title', 'like', '%' . $searchTerm . '%'))
                ->format(function ($value) {
                    return strlen($value) > 40 ? substr($value, 0, 40) . '...' : $value;
                }),
            
            Column::make("Autor", "user.name")
                ->searchable()
                ->sortable()
                ->collapseOnTablet()
                ->unclickable(), // deshabilita aquí el enlace de la columna que se definió en setTableRowUrl()
            
            BooleanColumn::make("Publicado", "is_published")
                ->sortable()
                ->collapseOnTablet(),

            ImageColumn::make("Imagen")
                ->location(fn($row) => 'https://picsum.photos/seed/' . $row->id . '/200/200')
                ->collapseOnTablet(),

            Column::make("Creado", "created_at")
                ->format(fn($value) => $value->format('d/m/Y'))
                ->collapseOnTablet(),

            /* ButtonGroupColumn::make("Acciones")
                ->buttons([
                    LinkColumn::make("Acciones")
                        ->title(fn() => "Editar")
                        ->location(fn($row) => route('welcome', ['article' => $row->sort])) // $row solo puede acceder a las columnas anteriores
                        ->attributes(fn() => [
                            'class' => 'btn-blue-gradient' // esta clase se creó en resources/css/components.css
                        ]),
                    LinkColumn::make("Acciones")
                        ->title(fn() => "Borrar")
                        ->location(fn($row) => route('welcome', ['article' => $row->sort])) // $row solo puede acceder a las columnas anteriores
                        ->attributes(fn() => [
                            'class' => 'btn-red-gradient'
                        ])
                ]), */
            
            /* Para poder utilizar el botón Eliminar, se requiere agregar HTML ↑↓ */
            /* Column::make("Acciones", 'id')
                ->format(fn($value) => "<a href='/dashboard?id={$value}' class='btn-red-gradient'>
                                <svg class='inline w-5' fill='currentColor' xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'><path d='m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z'/></svg>
                            </a>")
                ->html(), */

            /* También se puede retornar una vista ↑↓ */
            /* Column::make("Acciones", 'id')
                ->format(fn($value) => view('articles.tables.action', [
                    'id' => $value
                ])), */

            /* Usando labels ↑↓ */
            Column::make("Acciones")
                ->label(fn($row) => view('articles.tables.action', [
                    'id' => $row->id // ->id (o cualquier otra propiedad) debe mostrarse en la tabla para que se pueda acceder a él
                ]))
                ->collapseOnTablet()
                ->unclickable(), // deshabilita aquí el enlace de la columna que se definió en setTableRowUrl()
        ];
    }

    public function builder(): Builder
    {
        return Article::query()
                    ->with('user');
    }
}
