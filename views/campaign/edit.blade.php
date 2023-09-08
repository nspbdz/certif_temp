@extends('layouts.app')
@section('content')

<div class="hk-wrapper hk-vertical-nav">

    <div class="container mt-100">

        <section class="hk-sec-wrapper">
            <h5 class="hk-sec-title">Edit Campaign</h5>
            <div class="row">
                <div class="col-sm-9">
                    <form action="{{ route('backend.campaign.update', $data->id) }}" method="POST" enctype='multipart/form-data'>

                        {{csrf_field()}}
                        @method('PUT')

                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Campaign Name <span style="color: red; display: inline;">*</span></label>
                            <div class="col-sm-9">
                                <input value="{{ $data->name }}" name="name" placeholder="Campaign Name" id="name" class="form-control @error('name') is-invalid @enderror" required maxlength="50">
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sender_email" class="col-sm-3 col-form-label">Sender Email <span style="color: red; display: inline;">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="sender_email" id="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{old('sender_email')}}">
                                    @foreach($data_email as $value)
                                    <option @selected($value==$data->sender_email)
                                        value="{{ $value }}">{{ $value }}</option>
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
                                <input value="{{ $data->sender_name }}" name="sender_name" placeholder="Sender Name" id="sender_name" class="form-control @error('sender_name') is-invalid @enderror" required maxlength="50">
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
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="col-sm-3">

                    <div class="card">
                        <div class="card-body">
                            Action edit hanya akan memengaruhi pengiriman setelahnya,dan tidak akan mengubah data yang sebelumnya sudah terkirim kepada user

                        </div>
                    </div>

                </div>


            </div>
        </section>

    </div>

</div>



@endsection