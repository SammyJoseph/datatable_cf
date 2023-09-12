<?php

namespace App\Http\Livewire;

use App\Exports\ArticlesExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ArticleTable extends DataTableComponent
{
    // protected $model = Article::class; // se utilizará el método builder() en lugar del modelo

    /* también se puede hacer desde configure() con el método setBulkActions() */
    /* public array $bulkActions = [ // acciones masivas
        'deleteSelected' => 'Eliminar seleccionados'
    ]; */

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

        $this->setBulkActions([ // acciones masivas
            'deleteSelected' => 'Eliminar',
            'exportSelected' => 'Exportar a Excel'
        ]);

        $this->setReorderStatus(true); // habilita el reordenamiento de las filas

        $this->setHideReorderColumnUnlessReorderingEnabled(); // oculta la columna de reordenamiento cuando no se está reordenando
    }

    public function columns(): array
    {
        return [
            Column::make("Pos.", "sort")
                ->sortable(),                

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

    public function deleteSelected()
    {
        if ($this->getSelected()) {
            Article::whereIn('id', $this->getSelected())->delete(); // elimina los registros seleccionados
            $this->clearSelected(); // limpia los registros seleccionados
        }
        else {
            $this->emit('error', 'No hay registros seleccionados');
        }
    }

    public function exportSelected()
    {
        if ($this->getSelected()) {
            $articles = Article::whereIn('id', $this->getSelected())->get(); // obtiene los registros seleccionados
            $this->clearSelected();
            return Excel::download(new ArticlesExport($articles), 'articles.xlsx');
        }
        else {
            /* si ninguna fila es seleccionada, descargar los registros que se están viendo en pantalla */
            return Excel::download(new ArticlesExport($this->getRows()), 'articles.xlsx'); // getRows() obtiene todos los registros que se están mostrando en la tabla
        }
    }

    public function reorder($items)
    {
        // dd ($items);
        foreach ($items as $item) { // $items es un array con los registros reordenados
            Article::find((int)$item['value'])->update([
                'sort' => (int)$item['order']
            ]);
        }
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Publicado') // filtros con select
                ->options([
                    ''  => 'Todos',
                    '1' => 'Sí',
                    '0' => 'No'
                ])
                ->filter(function(Builder $query, $value) {
                    if($value != ''){ // si hay un valor (1 o 0)
                        $query->where('is_published', $value); // mostrar los que coincidan con $value (1 o 0)
                    }
                }),
            DateFilter::make('Desde')
                ->config([
                    'min'   => '2023-01-01',
                ])
                ->filter(function($query, $value){
                    $query->whereDate('articles.created_at', '>=', $value);
                }),
            DateFilter::make('Hasta')
                ->config([
                    'min'   => '2023-01-01',
                ])
                ->filter(function($query, $value){
                    $query->whereDate('articles.created_at', '<=', $value);
                })
        ];
    }
}