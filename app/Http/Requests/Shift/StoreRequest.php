<?php

namespace App\Http\Requests\Shift;

use App\Rules\UserHasRole;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('shift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

     
    public function rules(): array
    {
        $rules = [];
        $rules['shift_label'] = ['required', 'string','max:15'];
        if($this->has('sub_admin_id')){
            $rules['sub_admin_id'] = ['required', 'exists:users,uuid,deleted_at,NULL', new UserHasRole(config('constant.roles.sub_admin'), '', 'uuid')];
        }
        $rules['client_detail_id'] = ['required', 'exists:client_details,uuid,deleted_at,NULL'];
        $rules['occupation_id'] = ['required', 'exists:occupations,uuid,deleted_at,NULL'];        

        $rules['shifts'] = ['required', 'array'];

        $rules['shifts.*.start_date'] = ['required', 'date', function ($attribute, $value, $fail) {
            $today = Carbon::today();
            if (Carbon::parse($value)->lt($today)) {
                $fail('The start date must be greater than or equal to today.');
            }
        }];
        $rules['shifts.*.end_date'] = ['required', 'date',  'after_or_equal:shifts.*.start_date'];
        
        $rules['shifts.*.start_time'] = [
            'required', 
            'date_format:' . config('constant.date_format.time'), 
            function ($attribute, $value, $fail) {
                $today = Carbon::today()->format('d-m-Y');
                $currentTime = Carbon::now()->format('H:i');
                $shiftIndex = $this->getIndexFromAttribute($attribute);
                $shiftStartDate = $this->input("shifts.$shiftIndex.start_date");
    
                // Check if start_date is today and start_time is less than current time
                if ($shiftStartDate === $today && $value < $currentTime) {
                    $fail('The Start Time must be greater than or equal to the current time.');
                }
            }
        ];
    
        // Validate end_time
        $rules['shifts.*.end_time'] = [
            'required', 
            'date_format:' . config('constant.date_format.time'), 
            function ($attribute, $value, $fail) {
                $shiftIndex = $this->getIndexFromAttribute($attribute);
                $startDate = $this->input("shifts.$shiftIndex.start_date");
                $endDate = $this->input("shifts.$shiftIndex.end_date");
                $startTime = $this->input("shifts.$shiftIndex.start_time");
    
                // Ensure end_time is greater than start_time if dates are the same
                if ($startDate === $endDate && ($value <= $startTime)) {
                    $fail('The End Time must be greater than the start time and cannot be equal to it.');
                }
            }
        ];

        // $rules['end_time'] = ['required', 'date_format:'.config('constant.date_format.time'), 'after:start_time'];       

        $rules['shifts.*.assign_staff'] = ['nullable', 'exists:users,uuid,deleted_at,NULL', new UserHasRole(config('constant.roles.staff'), '', 'uuid'), function ($attribute, $value, $fail)  {  
                $user = User::where('uuid',$value)->first();
                $shiftIndex = $this->getIndexFromAttribute($attribute);

                $startDate = $this->input("shifts.$shiftIndex.start_date");
                $endDate = $this->input("shifts.$shiftIndex.end_date");
                $startTime = $this->input("shifts.$shiftIndex.start_time");
                $endTime = $this->input("shifts.$shiftIndex.end_time");

                $isShiftWithinAssignedShifts = $user->assignShifts()->whereIn('status', ['picked'])->where(function ($query) use($startDate, $endDate, $startTime,$endTime) {
                $query->where(function ($q) use($startDate, $endDate){
                    $q->whereBetween('start_date', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')])
                        ->orWhereBetween('end_date', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')]);
                })
                ->where(function ($q) use($startTime,$endTime) {
                        $q->where('start_time', '<=', $endTime)
                        ->where('end_time', '>=', $startTime);
                });
                })->exists();                
                if($isShiftWithinAssignedShifts){
                    $fail("The shift's time slot overlaps with the staff member's assigned shifts");
                } 
            }];

        return $rules;
    }

    public function messages()
    {
        return [
            // Messages for the shifts array
            'shifts.required' => 'The shifts are required.',
            'shifts.array' => 'The shifts must be an array.',
            
            // Shift start_date validation messages
            'shifts.*.start_date.required' => 'The start date is required for each shift.',
            'shifts.*.start_date.date' => 'The start date must be a valid date.',
            
            // Shift end_date validation messages
            'shifts.*.end_date.required' => 'The end date is required for each shift.',
            'shifts.*.end_date.date' => 'The end date must be a valid date.',
            'shifts.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            
            // Shift start_time validation messages
            'shifts.*.start_time.required' => 'The start time is required for each shift.',
            'shifts.*.start_time.date_format' => 'The start time must be in the format HH:MM.',
            
            // Shift end_time validation messages
            'shifts.*.end_time.required' => 'The end time is required for each shift.',
            'shifts.*.end_time.date_format' => 'The end time must be in the format HH:MM.',
        ];
    }

    public function attributes()
    {
        return [
            'sub_admin_id' => 'Client name',
            'client_detail_id' => 'Listed Sites Name',
            'location_id' => 'Location',
            'occupation_id' => ' Occupation',

            'shifts.*.start_date' => 'Start date',
            'shifts.*.end_date' => 'End date',
            'shifts.*.start_time' => 'Start time',
            'shifts.*.end_time' => 'End time',
        ];
    }

    private function getIndexFromAttribute($attribute)
    {
        return explode('.', $attribute)[1]; // Extract the index of the current shift
    }
}
