<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\FuncCall;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class SubAdminDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // ->addIndexColumn()
            ->addColumn('checkbox', function($record){
                return '<label class="custom-checkbox"><input type="checkbox" class="dt_cb location_cb" data-id="'.$record->uuid.'" /><span></span></label>';
            })
            ->editColumn('name', function($record){
                return $record->name ?? '';
            })
            
            ->addColumn('action', function($record){
                $actionHtml = '';
                if (Gate::check('sub_admin_edit')) {
                    $actionHtml .= '<button class="dash-btn sky-bg small-btn editLocationBtn" data-href="'.route('client-admins.edit', $record->uuid).'">'.__('global.edit').'</button><br>';
                }
                if (Gate::check('sub_admin_delete')) {
				    $actionHtml .= '<button class="dash-btn red-bg small-btn deleteLocationBtn" data-href="'.route('client-admins.destroy', $record->uuid).'">'.__('global.delete').'</button>';
                }
                return $actionHtml;
            })
            ->setRowId('id')
            ->rawColumns(['action', 'checkbox']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->whereHas('roles',function($query){
            $query->where('id',config('constant.roles.sub_admin'));
        })->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $orderByColumn = 1;
        if (Gate::check('sub_admin_delete')) {
            $orderByColumn = 2;
        }
        return $this->builder()
                    ->setTableId('client-admin-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy($orderByColumn)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [];
        if (Gate::check('sub_admin_delete')) {
            $columns[] = Column::make('checkbox')->title('<label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label>')->orderable(false)->searchable(false)->addClass('position-relative');
        } 
        $columns[] = Column::make('name')->title('<span>'.trans('cruds.client_admin.fields.name').'</span>');
        $columns[] = Column::make('email')->title('<span>'.trans('cruds.client_admin.fields.email').'</span>');
        $columns[] = Column::make('created_at')->title(trans('cruds.client_admin.fields.created_at'))->searchable(false)->visible(false);
        $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-center');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'client_admins_' . date('YmdHis');
    }
}
