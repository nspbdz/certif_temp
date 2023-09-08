@extends('layouts.app')

@section('content')

<style>
    .scroll {
        max-height: 300px;
        width: 400px;
        overflow-y: auto;
    }
</style>
<div class="container mt-xl-50 mt-sm-30 mt-15">
    <div class="row">
        <div class="col-md-6">

            <h3>Latest Campaign</h3>
            <br>
            <div class="card scroll">
                <div class="card-body">

                    @if($dataCampaign)
                    @foreach($dataCampaign as $value )
                    <div class="campaign-info">
                        <span class="font-weight-500 text-dark text-capitalize">Campaign Name</span> <span class="pl-5"> : {{$value->campaign_name}}</span><br>
                        <span class="font-weight-500 text-dark text-capitalize">Template Name</span> <span class="pl-5"> : {{$value->template_name}}</span><br>
                        <span class="font-weight-500 text-dark text-capitalize">Created date</span> <span class="pl-5"> : {{$value->templates_created_at}}</span><br>
                        <span class="font-weight-500 text-dark text-capitalize">Jumlah</span> <span class="pl-5"> : {{$value->total_success}} / {{$value->total_data}}</span><br>
                        <hr style="border-top: 1px solid #5E7D8A">
                    </div>
                    @endforeach
                    @else
                    kosong
                    @endif
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <h3>Change Log</h3>
            <br>
            <div class="card card-sm scroll">
                <div class="card-body">

                    @if($dataLog)
                    @foreach($dataLog as $item )
                    <div class="campaign-info">
                        <span class="font-weight-500 text-dark text-capitalize">Nama User</span>
                        <span class="pl-5"> : {{$item->username}}</span><br>
                        <span class="font-weight-500 text-dark text-capitalize">Action</span>
                        <span class="pl-5"> : {{$item->page}} / {{$item->action}}</span><br>
                        <span class="font-weight-500 text-dark text-capitalize">Change date and time</span>
                        <span class="pl-5"> : {{$item->created_at}}</span><br>
                        <hr style="border-top: 1px solid #5E7D8A">
                    </div>
                    @endforeach
                    @else
                    kosong
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="hk-row">
                <div class="col-sm-12">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection