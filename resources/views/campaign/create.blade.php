@extends('layouts.app')
@section('content')


<div class="hk-wrapper hk-vertical-nav">

    <div class="container mt-100">

        <section class="hk-sec-wrapper">
            <h5 class="hk-sec-title">Add Campaign</h5>
            <div class="row">
                <div class="col-sm-10">
                    <form action="{{ route('backend.campaign.store') }}" method="POST">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Campaign Name <span style="color: red; display: inline;">*</span></label>
                            <div class="col-sm-9">
                                <input name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="50">
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sender_email" class="col-sm-3 col-form-label">Sender Email <span style="color: red; display: inline;">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="sender_email" id="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{old('sender_email')}}" required>
                                    <option value="">Sender Email</option>
                                    @foreach($data_email as $value)
                                    <option value="{{ $value }}" {{ old('sender_email') == $value ? 'selected' : null }}> {{$value}} </option>
                                    @endforeach
                                </select>
                                @error('sender_email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sender_name" class="col-sm-3 col-form-label">Sender Name <span style="color: red; display: inline;">*</span></label>
                            <div class="col-sm-9">
                                <input name="sender_name" id="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{ old('sender_name') }}" required maxlength="50">
                                @error('sender_name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-sm-10">
                                <a href="/campaign" type="button" id="btn-cancel" class="btn btn-danger " style="flex-grow: 1;">
                                    Cancel
                                </a>
                                <button class="btn btn-success">Save</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </section>

    </div>

</div>



@endsection