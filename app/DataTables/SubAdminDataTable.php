<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Support\Str;
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
                return '<label class="custom-checkbox"><input type="checkbox" class="dt_cb client_admin_cb" data-id="'.$record->uuid.'" /><span></span></label>';
            })
            ->editColumn('name', function($record){
                return $record->name ?? '';
            })

            ->editColumn('is_active', function($record){
                $currentStatus = config('constant.user_status')[$record->is_active];
                $statusHtml = '<div class="custom-select position-relative">
                    <div class="select-styled main-select-box">'.$currentStatus.'</div>
                    <div class="select-options">
                        <ul class="">';
                            $statusOptions = [];
                            foreach(config('constant.user_status') as $key => $value){
                                $statusOptions[] = '<li class="select-option changeSubAdminStatus" data-selected_value="'.$record->is_active.'" data-val="'.$key.'" data-id="'.$record->uuid.'">'.$value.'</li>';
                            }
                            $statusHtml .= implode('', $statusOptions);
                        $statusHtml .= '</ul>
                    </div>
                </div>';
                return $statusHtml;
            })
            
            ->addColumn('action', function($record){
                $actionHtml = '';
                if (Gate::check('sub_admin_edit')) {
                    $actionHtml .= '<button class="dash-btn sky-bg small-btn icon-btn editSubAdminBtn" data-href="'.route('client-admins.edit', $record->uuid).'" title="Edit">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.edit').'">
                            '.(getSvgIcon('edit')).'
                        </span>
                    </button>';
                }
                if (Gate::check('sub_admin_delete')) {
                    $actionHtml .= '<button class="dash-btn red-bg small-btn icon-btn deleteSubAdminBtn" data-href="'.route('client-admins.destroy', $record->uuid).'" title="Delete">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.delete').'">
                            '.(getSvgIcon('delete')).'
                        </span>
                    </button>';
                }
                return $actionHtml;
            })
            ->setRowId('id')
            ->filterColumn('is_active', function ($query, $keyword) {
                $statusSearch  = null;
                if (Str::contains('active', strtolower($keyword))) {
                        $statusSearch = 1;
                } else if (Str::contains('deactive', strtolower($keyword))) {
                        $statusSearch = 0;
                }
                $query->where('is_active', $statusSearch); 
            })
            ->rawColumns(['action', 'checkbox','is_active']);
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
        $orderByColumn = 2;
        if (Gate::check('sub_admin_delete')) {
            $orderByColumn = 3;
        }
        return $this->builder()
                    ->setTableId('client-admin-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy($orderByColumn)
                    ->selectStyleSingle()
                    ->lengthMenu([
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'All']
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [];
        if (Gate::check('sub_admin_delete')) {
            $columns[] = Column::make('checkbox')->titleAttr('')->title('<label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label>')->orderable(false)->searchable(false)->addClass('pe-0 position-relative');
        } 
        $columns[] = Column::make('name')->title('<span>'.trans('cruds.client_admin.fields.name').'</span>')->titleAttr(trans('cruds.client_admin.fields.name'));
        $columns[] = Column::make('email')->title('<span>'.trans('cruds.client_admin.fields.email').'</span>')->titleAttr(trans('cruds.client_admin.fields.email'));
        $columns[] = Column::make('created_at')->title(trans('cruds.client_admin.fields.created_at'))->searchable(false)->visible(false);
        $columns[] = Column::make('is_active')->title('<span>'.trans('cruds.client_admin.fields.status').'</span>')->titleAttr(trans('cruds.client_admin.fields.status'));

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
