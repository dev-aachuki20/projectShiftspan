<?php

namespace App\DataTables;

use App\Models\AuthorizedShift;
use App\Models\Shift;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class ShiftDataTable extends DataTable
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
        return (new EloquentDataTable($query->with(['client', 'clientDetail', 'staffs'])->select('shifts.*')))
            // ->addIndexColumn()
            ->addColumn('checkbox', function($record){
                return '<label class="custom-checkbox"><input type="checkbox" class="dt_cb shift_cb" data-id="'.$record->uuid.'" /><span></span></label>';
            })

            ->editColumn('client.name', function($record){
                if(auth()->user()->is_super_admin){
                    $clientName = $record->client ? $record->client->name : '';
                    $imagePath = asset('images/check-mark.png');
                    $removeImagePath = asset('images/remove.png');
                    $authData = $record->authorize ? $imagePath : $removeImagePath;
                    $clockimage = '';
                    if($record->clockIn->count() > 0 && $record->clockOut->count() > 0){
                        $clockimage = $imagePath;
                    }else{
                        $clockimage = $removeImagePath;
                    }


                    return $clientName . '<br>
                        <div class="shift-btns">
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.clock_in').'" class="clock-btns '.($record->clockIn->count() > 0 ? 'clockIn' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="'.($record->clockIn->count() > 0 ? asset('images/check-mark.png') : asset('images/remove.png')).'" alt="clock-in"><br>'.__('cruds.shift.fields.clock_in').'
                                </span>
                            </a>
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.clock_out').'" class="clock-btns '.($record->clockOut->count() > 0 ? 'clockOut' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="'.($record->clockOut->count() > 0 ? asset('images/check-mark.png') : asset('images/remove.png')).'" alt="clock-out"><br>'.__('cruds.shift.fields.clock_out').'
                                </span>
                            </a>
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.timesheet').'" class="clock-btns '.($record->authorize ? 'timeSheet' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="' . $authData . '" alt="Timesheet"><br>'.__('cruds.shift.fields.timesheet').'
                                </span>
                            </a>
                        </div>';
                }
            })

            ->editColumn('clientDetail.name', function($record){
                if(auth()->user()->is_sub_admin){
                    $clientDetailName = $record->clientDetail ? $record->clientDetail->name : '';
                    $imagePath = asset('images/check-mark.png');
                    $removeImagePath = asset('images/remove.png');
                    $authData = $record->authorize ? $imagePath : $removeImagePath;
                    $clockimage = '';
                    if($record->clockIn->count() > 0 && $record->clockOut->count() > 0){
                        $clockimage = $imagePath;
                    }else{
                        $clockimage = $removeImagePath;
                    }
                    return $clientDetailName. '<br>
                        <div class="shift-btns">
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.clock_in').'" class="clock-btns '.($record->clockIn->count() > 0 ? 'clockIn' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="'.($record->clockIn->count() > 0 ? asset('images/check-mark.png') : asset('images/remove.png')).'" alt="clock-in"><br>'.__('cruds.shift.fields.clock_in').'
                                </span>
                            </a>
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.clock_out').'" class="clock-btns '.($record->clockOut->count() > 0 ? 'clockOut' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="'.($record->clockOut->count() > 0 ? asset('images/check-mark.png') : asset('images/remove.png')).'" alt="clock-out"><br>'.__('cruds.shift.fields.clock_out').'
                                </span>
                            </a>
                            <a href="javascript:void(0)" title="'.__('cruds.shift.fields.timesheet').'" class="clock-btns '.($record->authorize ? 'timeSheet' : '').'" data-shift_id="'.$record->id.'">
                                <span>
                                    <img src="' . $authData . '" alt="Timesheet"><br>'.__('cruds.shift.fields.timesheet').'
                                </span>
                            </a>
                        </div>';
                }else{
                    return $record->clientDetail ? $record->clientDetail->name : '';
                }
            })

            // ->editColumn('staffs.name', function($record){
            //     $selectedStaffs = $record->staffs()->pluck('name')->toArray();
            //     return implode(', ', $selectedStaffs);
            // })

            ->editColumn('staffs.name', function($record) {
                $selectedStaffs = $record->staffs()->pluck('name')->toArray();
                $staffUuids = $record->staffs()->pluck('uuid')->toArray();

                $anchorTags = array_map(function ($staffName, $staffUuid) use ($record) {
                    return '<a class="viewStaffBtn" href="' . route('staffs.show', $staffUuid) . '" data-type="shift" data-href="' . route('staffs.show', $staffUuid) . '">' . $staffName . '</a>';
                }, $selectedStaffs, $staffUuids);

                return implode(', ', $anchorTags);
            })

            ->editColumn('start_date', function($record){
                return $record->start_date ? dateFormat($record->start_date, config('constant.date_format.date')) : '';
            })

            ->editColumn('end_date', function($record){
                return $record->end_date ? dateFormat($record->end_date, config('constant.date_format.date')) : '';
            })

            ->editColumn('start_time', function($record){
                return $record->start_time ? dateFormat($record->start_time, config('constant.date_format.time')) : '';
            })

            ->editColumn('end_time', function($record){
                return $record->end_time ? dateFormat($record->end_time, config('constant.date_format.time')) : '';
            })

            ->editColumn('picked_at', function($record){
                return $record->picked_at ? dateFormat($record->picked_at, config('constant.date_format.date_time')) : '';
            })

            ->editColumn('cancel_at', function($record){
                return $record->cancel_at ? dateFormat($record->cancel_at, config('constant.date_format.date_time')) : '';
            })

            ->editColumn('status', function($record){
                return config('constant.shift_status')[$record->status];
            })

            ->editColumn('rating', function($record){
                $rating = $record->rating;
                return '<div>
                    <i class="fa fa-star '.($rating >=1 ? 'checked' : '' ).'"></i>
                    <i class="fa fa-star '.($rating >=2 ? 'checked' : '' ).'"></i>
                    <i class="fa fa-star '.($rating >=3 ? 'checked' : '' ).'"></i>
                    <i class="fa fa-star '.($rating >=4 ? 'checked' : '' ).'"></i>
                    <i class="fa fa-star '.($rating >=5 ? 'checked' : '' ).'"></i>
                </div>';
            })
            
            ->addColumn('action', function($record){
                $actionHtml = '';

                if ($record->status == 'open') {
                    $actionHtml .= '<button class="dash-btn yellow-bg small-btn icon-btn cancelShiftBtn" title="'.__('global.cancel').'" data-href="'.route('shifts.cancel', $record->uuid).'">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.cancel').'">
                            '.(getSvgIcon('cancel')).'
                        </span>
                    </button>';
                }
                if ($record->status == 'complete') {
                    $actionHtml .= '<button class="dash-btn yellow-bg small-btn icon-btn ratingShiftBtn"  data-rating="'.$record->rating.'" data-href="'.route('shifts.rating', $record->uuid).'">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.trans('cruds.shift.fields.rating').'">
                            '.(getSvgIcon('rating')).'
                        </span>
                    </button>';
                }
                if($record->status != 'complete'){
                    if (Gate::check('shift_edit')) {
                        $actionHtml .= '<button class="dash-btn sky-bg small-btn icon-btn editShiftBtn" data-start_time="'.$record->start_time.'" data-href="'.route('shifts.edit', $record->uuid).'">
                            <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.edit').'">
                                '.(getSvgIcon('edit')).'
                            </span>
                        </button>';
                    }
                    
                    if (Gate::check('shift_delete')) {
                        $actionHtml .= '<button class="dash-btn red-bg small-btn icon-btn deleteShiftBtn" data-href="'.route('shifts.destroy', $record->uuid).'">
                            <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.__('global.delete').'">
                                '.(getSvgIcon('delete')).'
                            </span>
                        </button>';
                    }
                }
                return $actionHtml;
            })
            ->setRowId('id')

            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_date, '".config('constant.search_date_format.date')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })

            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_date, '".config('constant.search_date_format.date')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })

            ->filterColumn('start_time', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_time, '".config('constant.search_date_format.time')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })


            ->filterColumn('end_time', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_time, '".config('constant.search_date_format.time')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })

            ->filterColumn('picked_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(picked_at, '".config('constant.search_date_format.date_time')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })


            ->filterColumn('cancel_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(cancel_at, '".config('constant.search_date_format.date_time')."') like ?", ["%$keyword%"]); //date_format when searching using date
            })
            ->rawColumns([
                'action', 
                'checkbox',
                'rating', 
                auth()->user()->is_super_admin ? 'client.name' : '', 
                auth()->user()->is_sub_admin ? 'clientDetail.name' : '',
                'staffs.name'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Shift $model): QueryBuilder
    {
        $user = $this->authUser;
        if($user->is_super_admin){
            return $model->newQuery();
        } else {
            return $model->where('sub_admin_id', $user->id)->newQuery();
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        
        return $this->builder()
                    ->setTableId('shift-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(0)
                    ->lengthMenu([
                        [10, 25, 50, 100, /*-1*/], // Page length values
                        [10, 25, 50, 100, /*'All'*/]  // Display text for each value
                    ])                    
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
        $columns[] = Column::make('id')->title('')->searchable(false)->visible(false);
        if (Gate::check('shift_delete')) {
            $columns[] = Column::make('checkbox')->titleAttr('')->title('<label class="custom-checkbox"><input type="checkbox" id="dt_cb_all" ><span></span></label>')->orderable(false)->searchable(false)->addClass('pe-0 position-relative');
        }

        if($this->authUser->is_super_admin){
            $columns[] = Column::make('client.name')->title('<span>'.trans('cruds.shift.fields.client_name').'</span>')->titleAttr(trans('cruds.shift.fields.client_name'));
        }
        $columns[] = Column::make('clientDetail.name')->title('<span>'.trans('cruds.shift.fields.client_detail_name').'</span>')->titleAttr(trans('cruds.shift.fields.client_detail_name'));
        $columns[] = Column::make('staffs.name')->title('<span>'.trans('cruds.shift.fields.staff_name').'</span>')->titleAttr(trans('cruds.shift.fields.staff_name'));
        $columns[] = Column::make('start_date')->title('<span>'.trans('cruds.shift.fields.start_date').'</span>')->titleAttr(trans('cruds.shift.fields.start_date'));
        $columns[] = Column::make('end_date')->title('<span>'.trans('cruds.shift.fields.end_date').'</span>')->titleAttr(trans('cruds.shift.fields.end_date'));

        $columns[] = Column::make('start_time')->title('<span>'.trans('cruds.shift.fields.start_time').'</span>')->titleAttr(trans('cruds.shift.fields.start_time'));
        $columns[] = Column::make('end_time')->title('<span>'.trans('cruds.shift.fields.end_time').'</span>')->titleAttr(trans('cruds.shift.fields.end_time'));
        $columns[] = Column::make('picked_at')->title('<span>'.trans('cruds.shift.fields.picked_at').'</span>')->titleAttr(trans('cruds.shift.fields.picked_at'));
        $columns[] = Column::make('cancel_at')->title('<span>'.trans('cruds.shift.fields.cancel_at').'</span>')->titleAttr(trans('cruds.shift.fields.cancel_at'));
        $columns[] = Column::make('rating')->title('<span>'.trans('cruds.shift.fields.rating').'</span>')->titleAttr(trans('cruds.shift.fields.rating'))->searchable(false);
        $columns[] = Column::make('status')->title('<span>'.trans('cruds.shift.fields.status').'</span>')->titleAttr(trans('cruds.shift.fields.status'));
        
        $columns[] = Column::make('created_at')->title(trans('cruds.shift.fields.created_at'))->visible(false)->searchable(false);
        $columns[] = Column::computed('action')->exportable(false)->printable(false)->width(60)->addClass('text-center');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'shift_' . date('YmdHis');
    }
}
