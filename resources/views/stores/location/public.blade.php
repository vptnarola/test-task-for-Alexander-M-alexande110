@extends('layouts.public')

@section('content')
<div class="text-center mt-5">
    <h1>Welcome To {{ $location['location_name'] }}</h1>
</div>
<div class="row">
    <div class="offset-3 col-6">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Check-In</h5>
            </div>
            <form action="{{ route('public.checkIn', ['location' => $location->uuid ]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" maxlength="191">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" maxlength="191">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" maxlength="191">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <button class="btn btn-success float-end">Check-In</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection