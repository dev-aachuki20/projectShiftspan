<?php

namespace App\DataTables;

use App\Models\ClientDetail;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class SubAdminDetailDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */

    private $authUser;

    public function __construct()
    {
        $this->authUser = auth()->user();
    }


    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['client'])->select("client_details.*")))
            // ->addIndexColumn()
            ->addColumn('checkbox', function($record){
                return '<label class="custom-checkbox"><input type="checkbox" class="dt_cb sub_admin_detail_cb" data-id="'.$record->uuid.'" /><span></span></label>';
            })

            ->editColumn('building_image', function($record){
                return '<div class="staff-img"><img src="'.($record->building_image_url ? $record->building_image_url : asset(config('constant.default.building-image'))).'" alt=""></div>';
            })

            ->editColumn('client.name', function($record){
                return $record->client ? $record->client->name : '';
            })

            ->editColumn('name', function($record){
                return '<div>'.($record->name ?? '').'</div>';
            })

            ->editColumn('address', function($record){
                return '<div>'.($record->address ?? '').'</div>';
            })

            ->editColumn('shop_description', function($record){
                return '<div>'.($record->shop_description ?? '').'</div>';
            })

            ->editColumn('travel_info', function($record){
                return '<div>'.($record->travel_info ?? '').'</div>';
            })
            
            ->addColumn('action', function($record){
                $actionHtml = '';
                if (Gate::check('sub_admin_detail_view')) {
                    $actionHtml .= '<button class="dash-btn yellow-bg small-btn icon-btn viewSubAdminDetailBtn"  data-href="'.route('client-details.show', $record->uuid).'" title="View">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.view').'">
                            '.(getSvgIcon('view')).'
                        </span>
                    </button>';
                }
                if (Gate::check('sub_admin_detail_edit')) {
                    $actionHtml .= '<button class="dash-btn sky-bg small-btn icon-btn editSubAdminDetailBtn"  data-href="'.route('client-details.edit', $record->uuid).'" title="Edit">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.edit').'">
                            '.(getSvgIcon('edit')).'
                        </span>
                    </button>';
                }
                if (Gate::check('sub_admin_detail_delete')) {
				    $actionHtml .= '<button class="dash-btn red-bg small-btn icon-btn deleteSubAdminDetailBtn" data-href="'.route('client-details.destroy', $record->uuid).'" title="Delete">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.delete').'">
                            '.(getSvgIcon('delete')).'
                        </span>
                    </button>';
                }
                return $actionHtml;
            })
            ->setRowId('id')
            ->rawColumns(['action', 'checkbox', 'building_image', 'shop_description', 'travel_info', 'address', 'name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ClientDetail $model): QueryBuilder
    {
        $user = $this->authUser;
        if($user->is_super_admin){
            return $model->newQuery();
        } else {
            return $model->where('sub_admin_id', $user->id)->with('client')->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('client-detail-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(0)                    
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
        $columns[] = Column::make('created_at')->title('')->visible(false)->searchable(false);
        if (Gate::check('sub_admin_detail_delete')) {
            $columns[] = Column::make('checkbox')->titleAttr('')->title('<label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label>')->orderable(false)->searchable(false)->addClass('pe-0 position-relative');
        }

        $columns[] = Column::make('building_image')->title(trans('cruds.client_detail.fields.building_image'))->searchable(false)->orderable(false);
        if($this->authUser->is_super_admin){
            $columns[] = Column::make('client.name')->title('<span>'.trans('cruds.client_detail.fields.client_name').'</span>')->titleAttr(trans('cruds.client_detail.fields.client_name'));
        }
        $columns[] = Column::make('name')->title('<span>'.trans('cruds.client_detail.fields.name').'</span>')->titleAttr(trans('cruds.client_detail.fields.name'))->addClass('');
        $columns[] = Column::make('address')->title('<span>'.trans('cruds.client_detail.fields.address').'</span>')->titleAttr(trans('cruds.client_detail.fields.address'))->addClass('mw-160 white-space-normal line-clamp-3');
        $columns[] = Column::make('shop_description')->title('<span>'.trans('cruds.client_detail.fields.shop_description').'</span>')->titleAttr(trans('cruds.client_detail.fields.shop_description'))->addClass('mw-250 white-space-normal line-clamp-3');
        $columns[] = Column::make('travel_info')->title('<span>'.trans('cruds.client_detail.fields.travel_info').'</span>')->titleAttr(trans('cruds.client_detail.fields.travel_info'))->addClass('mw-250 white-space-normal line-clamp-3');    
        
        $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Client_details_' . date('YmdHis');
    }
}
