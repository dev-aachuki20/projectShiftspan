@forelse ($notification as $item)
    <li>
        <a href="#" class="read {{-- un-read --}}">
            <h6>{{$item->subject}}</h6>
            <p>{{$item->message}}</p>
        </a>
    </li>
@empty
    <li>
        <h6></h6>
        <p>@lang('global.data_not_found')</p>
    </li>
@endforelse