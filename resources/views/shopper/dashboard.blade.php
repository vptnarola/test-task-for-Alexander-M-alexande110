@extends('layouts.public')

@section('content')

{{-- @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif --}}
<div class="text-center mt-5">
    <h1>Welcome To {{ $shopper->location['location_name'] }}</h1>
</div>
<hr>
<div class="row">
    <div class="offset-3 col-6">
        <div class="row">
        	<div class="col-12 mb-3">
        		<h4 class="text-center">Hello, {{ $shopper->first_name.' '.$shopper->last_name }}</h4>
        	</div>
        	<div class="col-12 mb-3">
                @if($shopper->status->name == 'Pending')
                    <h3 class="text-center">Your waiting number is 
                        @if($shopper_waiting_number < 3)
                            <span class="badge bg-success">{{ $shopper_waiting_number }}</span>
                        @elseif($shopper_waiting_number < 5)
                            <span class="badge bg-warning">{{ $shopper_waiting_number }}</span>
                        @else
                            <span class="badge bg-danger">{{ $shopper_waiting_number }}</span>
                        @endif
                    </h3>
                @elseif($shopper->status->name == 'Active')
                    <h3 class="text-center text-success">Keep your shopping</h3>
                @else
                    <h3 class="text-center text-primary">Thank you for shopping. please, visit again.</h3>
                @endif
        	</div>
        	<div class="col-12 text-center">
        		<a href="{{ route('shopper.check_out', ['location' => $shopper->location->uuid, 'shopper' => $shopper->uuid]) }}" class="btn btn-danger">Check Out</a>
                <button class="btn btn-primary" onclick="location.reload();">Reload</button>
        	</div>
        </div>
    </div>
</div>
@endsection