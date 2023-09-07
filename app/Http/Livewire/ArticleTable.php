<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class ArticleTable extends DataTableComponent
{
    // protected $model = Article::class; // se utilizará el método builder() en lugar del modelo

    public function configure(): void
    {
        $this->setPrimaryKey('id'); // clave primaria de la tabla
    }

    public function columns(): array
    {
        return [
            Column::make("Ordenar", "sort")
                ->sortable(),
            Column::make("Título", "title")
                ->searchable()
                ->sortable(),
            Column::make("Usuario", "user.name")
                ->searchable()
                ->sortable(),
            BooleanColumn::make("Publicado", "is_published")
                ->sortable(),
            Column::make("Creado", "created_at"),

            ButtonGroupColumn::make("Acciones")
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
                ]),

            
        ];
    }

    public function builder(): Builder
    {
        return Article::query()
                    ->with('user');
    }
}
