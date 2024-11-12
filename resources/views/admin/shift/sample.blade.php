<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table td {
                background-color: inherit;
            }
        }
    </style>
</head>

<body>
    <table class="table" border="0" style="max-width: 1024px; width: 100%;  margin: 0px auto; border-collapse: collapse; width: 100%; color: black; border-spacing: 0; border-color: #ddd; box-shadow: none;font-family: Arial, Helvetica, sans-serif; margin-bottom: 20px; font-size: 16px;">
        <thead>
            <tr>
                <th colspan="6" style="padding: 6px; background-color: #1d8524;"></th>
            </tr>
            <tr>
                <th colspan="6" style="text-align: center; padding: 15px 0px 10px;"><img src="{{asset('default/Shiftspan.png')}}" width="150px" alt="logo"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" style="border-bottom: 1px solid #fff; padding: 20px; background-color: #ebebeb;"><strong>@lang('cruds.shift.fields.manager_name'):</strong> {{ isset($shiftData->manager_name) ? $shiftData->manager_name : 'N/A' }}</td>
                <td colspan="3" style="border-bottom: 1px solid #fff; padding: 20px; background-color: #ebebeb; text-align: end;"><strong>@lang('cruds.staff.fields.created_at')</strong> : {{ isset($shiftData->created_at) ? $shiftData->created_at->format('d-m-Y, h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 5px 20px 20px;"><strong>@lang('cruds.shift.fields.staff_name')</strong> : {{isset($shiftData->user) && isset($shiftData->user->name) ? $shiftData->user->name : 'N/A'}}</td>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 5px;"><strong>@lang('cruds.shift.fields.shift_label')</strong> : {{isset($shiftData->shift) && isset($shiftData->shift->shift_label) ? $shiftData->shift->shift_label : 'N/A'}}</td>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 5px;"><strong>@lang('cruds.shift.fields.start_date')</strong> : {{ isset($shiftData->shift) && isset($shiftData->shift->start_date) ? \Carbon\Carbon::parse($shiftData->shift->start_date)->format('d-m-Y') : 'N/A' }}</td>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 5px;"><strong>@lang('cruds.shift.fields.end_date')</strong> : {{ isset($shiftData->shift) && isset($shiftData->shift->end_date) ? \Carbon\Carbon::parse($shiftData->shift->end_date)->format('d-m-Y') : 'N/A' }}</td>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 5px;"><strong>@lang('cruds.shift.fields.start_time')</strong> : {{isset($shiftData->shift) && isset($shiftData->shift->start_time) ? \Carbon\Carbon::parse($shiftData->shift->start_time)->format('h:i') : 'N/A'}}</td>
                <td style="background-color: #c6dec3; font-size: 14px; padding: 20px 20px 20px 5px;"><strong>@lang('cruds.shift.fields.end_time')</strong> : {{isset($shiftData->shift) && isset($shiftData->shift->end_time) ? \Carbon\Carbon::parse($shiftData->shift->end_time)->format('h:i') : 'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; padding: 100px 50px;"><img src="{{ $shiftData->authorized_signature_url ? $shiftData->authorized_signature_url : asset('images/manager-sign.png')}}" width="300" alt=""></td>
            </tr>
        </tbody>
    </table>
</body>

</html>