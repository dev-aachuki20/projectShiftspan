<?php

namespace App\DataTables;

use App\Models\Occupation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Gate;

class OccupationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', function($record){
                return '<label class="custom-checkbox"><input type="checkbox" class="dt_cb occupation_cb" data-id="'.$record->uuid.'" /><span></span></label>';
            })
            ->editColumn('name', function($record){
                return $record->name ?? '';
            })
            
            ->addColumn('action', function($record){
                $actionHtml = '';
                if (Gate::check('occupation_edit')) {
                    $actionHtml .= '<button class="dash-btn sky-bg small-btn editOccupationBtn" data-href="'.route('occupations.edit', $record->uuid).'">'.__('global.edit').'</button><br>';
                }
                if (Gate::check('occupation_delete')) {
				    $actionHtml .= '<button class="dash-btn red-bg small-btn deleteOccupationBtn" data-href="'.route('occupations.destroy', $record->uuid).'">'.__('global.delete').'</button>';
                }
                return $actionHtml;
            })

            ->setRowId('id')
            ->rawColumns(['action', 'checkbox']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Occupation $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $orderByColumn = 1;
        if (Gate::check('occupation_delete')) {
            $orderByColumn = 2;
        }
        return $this->builder()
                    ->setTableId('occupation-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy($orderByColumn)
                    ->parameters([
                        'responsive' => true,
                        "scrollCollapse" => true,
                        'autoWidth' => true,
                        'language' => [
                            "sZeroRecords" => __('cruds.data_not_found'),
                            "sProcessing" => __('cruds.processing'),
                            "sLengthMenu" => __('cruds.show') . " _MENU_ " . __('cruds.entries'),
                            "sInfo" => config('app.locale') == 'en' ?
                                __('cruds.showing') . " _START_ " . __('cruds.to') . " _END_ " . __('cruds.of') . " _TOTAL_ " . __('cruds.entries') :
                                __('cruds.showing') . "_TOTAL_" . __('cruds.to') . __('cruds.of') . "_START_-_END_" . __('cruds.entries'),
                            "sInfoEmpty" => __('cruds.showing') . " 0 " . __('cruds.to') . " 0 " . __('cruds.of') . " 0 " . __('cruds.entries'),
                            "search" => __('cruds.search'),
                            "paginate" => [
                                "first" => __('cruds.first'),
                                "last" => __('cruds.last'),
                                "next" => __('cruds.next'),
                                "previous" => __('cruds.previous'),
                            ],
                            "autoFill" => [
                                "cancel" => __('message.cancel'),
                            ],
                        ],
                    ])
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [];
        if (Gate::check('occupation_delete')) {
            $columns[] = Column::make('checkbox')->title('<label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label>')->orderable(false)->searchable(false)->addClass('position-relative');
        } 
        $columns[] = Column::make('name')->title('<span>'.trans('cruds.occupation.title_singular').' '.trans('cruds.occupation.fields.name').'</span>');
        $columns[] = Column::make('created_at')->title(trans('cruds.occupation.fields.created_at'))->searchable(false)->visible(false);
        $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-center');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Occupation_' . date('YmdHis');
    }
}
