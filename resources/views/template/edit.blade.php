@extends('layouts.app')
@push('css')
<!-- Daterangepicker CSS -->
<link href="{{ asset('vendors/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<div class="hk-wrapper hk-vertical-nav">

    <div class="container mt-100">

        <section class="hk-sec-wrapper">
            <h5 class="hk-sec-title">Edit Template</h5>
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('backend.template.update', $data->id) }}" method="POST" enctype=multipart/form-data>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Campaign</label>
                                    <div class="col-md-8">
                                        <select class="form-control @error('campaign') is-invalid @enderror select2" name="campaign" id="campaign" class="form-control @error('campaign') is-invalid @enderror" value="{{old('sender_email')}}" disabled>
                                            <option value="">-- Select Campaign --</option>
                                            @foreach($campaigns as $value)
                                            <option value="{{ $value->id }}" @selected($value->id==$data->campaign_id) {{ old('campaign') == $value->id ? 'selected' : null }}> {{$value->name}} </option>
                                            @endforeach
                                        </select>
                                        @error('campaign')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Template Name</label>
                                    <div class="col-md-8">
                                        <input name="name" placeholder="Name" id="name" value="{{$data->name}}" class="form-control @error('name') is-invalid @enderror" maxlength="60">
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label">Sender Email</label>
                                    <div class="col-md-8">
                                        <input name="sender_email" placeholder="Sender Email" id="sender_email" value="{{$data->sender_email}}" class="form-control @error('sender_email') is-invalid @enderror" disabled>
                                        @error('sender_email')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label">Sender Name</label>
                                    <div class="col-md-8">
                                        <input name="sender_name" placeholder="Sender Name" id="sender_name" value="{{$data->sender_name}}" class="form-control @error('sender_name') is-invalid @enderror" disabled>
                                        @error('sender_name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Email Subject</label>
                                    <div class="col-md-8">
                                        <input name="email_subject" placeholder="Email Subject" id="email_subject" value="{{$data->email_subject}}" class="form-control @error('email_subject') is-invalid @enderror" maxlength="60">
                                        @error('email_subject')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="email_type" class="col-sm-4 col-form-label required">Email Type</label>
                                    <div class="col-md-8">
                                        <select class="form-control @error('email_type') is-invalid @enderror" name="email_type" id="email_type" class="form-control @error('email_type') is-invalid @enderror" value="{{old('email_type')}}" disabled>
                                            <option value="">-- Select Email Type --</option>
                                            @foreach($email_type as $value)
                                            <option @selected($value==$data->email_type) value="{{ $value }}" {{ old('email_type') == $value ? 'selected' : null }}> {{$value}} </option>
                                            @endforeach
                                        </select>
                                        @error('email_type')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="header_image" class="col-sm-4 col-form-label required">Upload Header Image</label>
                                    <div class="col-md-8">
                                        <input type="file" name="header_image" id="header_image" accept=".jpg,.jpeg" class="form-control @error('header_image') is-invalid @enderror">
                                        @error('header_image')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                        <small id="header_image_helper" class="form-text text-muted">
                                            Maximum file size 100kb
                                        </small>
                                        @if(!empty($data->header_image))
                                        @php

                                        $image = config('config.akcdn.full_url') . $data->header_image . '?m=1';
                                        @endphp
                                        <img class="img-fluid mt-3" src="{{ $image }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Start Date</label>
                                    <div class="col-md-8">
                                        <input name="start_date" placeholder="YYYY-MM-DD HH:mm:ss" id="start_date" class="form-control @error('start_date') is-invalid @enderror datepicker" value="{{$data->start_date}}" autocomplete="off" required>
                                        @error('start_date')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">End Date</label>
                                    <div class="col-md-8">
                                        <input name="end_date" placeholder="YYYY-MM-DD HH:mm:ss" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{$data->end_date}}" autocomplete="off" required>
                                        @error('end_date')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div id="certificate">
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Font Type</label>
                                        <div class="col-md-8">
                                            <select class="form-control @error('font_type') is-invalid @enderror" name="font_type" id="font_type" class="form-control @error('font_type') is-invalid @enderror" value="{{old('font_type')}}">
                                                <option value="">-- Select Font Type --</option>
                                                @foreach($font_type as $key => $value)
                                                <option value="{{ $key }}" @selected($key==$data->font_type) {{ old('font_type') == $key ? 'selected' : null }}> {{$value}} </option>
                                                @endforeach
                                            </select>
                                            @error('font_type')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Font Color</label>
                                        <div class="col-md-8">
                                            <input name="font_color" type="color" placeholder="Font Color" id="font_color" value="{{$data->font_color}}" class="form-control @error('font_color') is-invalid @enderror">
                                            @error('font_color')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Text Position</label>
                                        <div class="col-md-8">
                                            <select class="form-control @error('text_position') is-invalid @enderror" name="text_position" id="text_position" class="form-control @error('text_position') is-invalid @enderror" value="{{old('text_position')}}">
                                                <option value="">-- Select Text Position --</option>
                                                @foreach($text_position as $value)
                                                <option value="{{ $value }}" @selected($value==$data->text_position) {{ old('text_position') == $value ? 'selected' : null }}> {{$value}} </option>
                                                @endforeach
                                            </select>
                                            @error('text_position')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Certificate Name</label>
                                        <div class="col-md-8">
                                            <input name="certificate_name" placeholder="Certificate Name" id="certificate_name" value="{{$data->certificate_name}}" class="form-control @error('certificate_name') is-invalid @enderror" maxlength="65">
                                            @error('certificate_name')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">PDF Author</label>
                                        <div class="col-md-8">
                                            <input name="certificate_author" placeholder="PDF Author" id="certificate_author" value="{{$data->certificate_author}}" class="form-control @error('certificate_author') is-invalid @enderror" maxlength="15">
                                            @error('certificate_author')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">PDF Title</label>
                                        <div class="col-md-8">
                                            <input name="pdf_title" placeholder="PDF Title" id="pdf_title" value="{{$data->pdf_title}}" class="form-control @error('pdf_title') is-invalid @enderror" maxlength="65">
                                            @error('pdf_title')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Certificate Image</label>
                                        <div class="col-md-8">
                                            <input type="file" name="certificate_image" id="certificate_image" accept=".jpg,.jpeg" class="form-control @error('certificate_image') is-invalid @enderror">
                                            @error('certificate_image')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                            <small id="certificate_image_helper" class="form-text text-muted">
                                                Maximum file size 300kb
                                            </small>
                                            @if(!empty($data->certificate_image))
                                            @php

                                            $image = config('config.akcdn.full_url') . $data->certificate_image . '?m=1';
                                            @endphp
                                            <img class="img-fluid mt-3" src="{{ $image }}">
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label for="">Email Body</label>
                                <div class="tinymce-wrap">
                                    <textarea name="email_body" id="email_body" class="tinymce" id="myeditorinstancess" maxlength="1000">{{$data->email_body}}</textarea>
                                </div>
                                <div id="characterCount"></div>
                                @error('email_body')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <table class="table table-success table-bordered table-hover">
                                    <thead class="thead-success">
                                        <tr>
                                            <th colspan="2" style="text-align:center">Snippet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="snippetName">
                                            <td>Label Name</td>
                                            <td>$$name</td>
                                        </tr>
                                        <tr id="snippetCode">
                                            <td>Ticket Code</td>
                                            <td>$$ticketcode</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-success">Save</button>

                    </form>
                </div>



            </div>
        </section>

    </div>

</div>



@endsection

@push('js')
<script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('vendors/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tinymce JavaScript -->
<script src="{{ asset('vendors/tinymce/tinymce.min.js') }}"></script>
<script>
    url = "{{ route('backend.campaign.data') }}";
    $(".select2").select2();
    showCertificateDetail();

    function showCertificateDetail() {
        if ($("#email_type").val() == 'certificate') {
            $("#certificate").show();
            $('#snippetCode').hide();
        } else {
            $("#certificate").hide();
            $('#snippetCode').show();
        }
    }

    if ($('#start_date, #end_date').length) {
        // check if element is available to bind ITS ONLY ON HOMEPAGE
        var currentDate = moment().format("YYYY-MM-DD");

        $('#start_date, #end_date').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            timePicker24Hour: true,
            timePicker: true,
            alwaysShowCalendars: true,
            minDate: currentDate,
            autoUpdateInput: false

        }, function(start, end, label) {
            // Lets update the fields manually this event fires on selection of range
            var selectedStartDate = start.format('YYYY-MM-DD HH:mm:ss'); // selected start
            var selectedEndDate = end.format('YYYY-MM-DD HH:mm:ss'); // selected end

            $checkinInput = $('#start_date');
            $checkoutInput = $('#end_date');

            // Updating Fields with selected dates
            $checkinInput.val(selectedStartDate);
            $checkoutInput.val(selectedEndDate);

            // Setting the Selection of dates on calender on CHECKOUT FIELD (To get this it must be binded by Ids not Calss)
            var checkOutPicker = $checkoutInput.data('daterangepicker');
            checkOutPicker.setStartDate(selectedStartDate);
            checkOutPicker.setEndDate(selectedEndDate);

            // Setting the Selection of dates on calender on CHECKIN FIELD (To get this it must be binded by Ids not Calss)
            var checkInPicker = $checkinInput.data('daterangepicker');
            checkInPicker.setStartDate(selectedStartDate);
            checkInPicker.setEndDate(selectedEndDate);

        });

    } // End Daterange Picker

    $(function() {
        $('form').on('submit', function() {
            $('select').removeAttr('disabled');
        });
    });

    $(document).ready(function() {

        function snippetName() {
            event.preventDefault();
            let editorBody = tinymce.activeEditor.getBody();
            tinymce.activeEditor.selection.setContent("$$name");
            characterCount();
        }
        var buttonName = document.getElementById('snippetName');
        buttonName.addEventListener('click', snippetName, false);

        function snippetCode() {
            event.preventDefault();
            let editorBody = tinymce.activeEditor.getBody();
            tinymce.activeEditor.selection.setContent("$$ticketcode");
            characterCount();
        }
        var buttonCode = document.getElementById('snippetCode');
        buttonCode.addEventListener('click', snippetCode, false);

    });

    function characterCount() {
        var editorContent = tinymce.get('email_body').getContent({
            format: 'text'
        });
        var charCount = editorContent.replace(/\s/g, '').length;
        document.getElementById('characterCount').textContent = "Character Count : " + charCount;
    }

    $(function() {
        "use strict";



        tinymce.init({
            selector: '.tinymce', // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: 'code lists link wordcount',
            height: 300,

            toolbar: 'bold italic underline | bullist numlist | link | code ',
            setup: function(editor) {
                editor.on('init', function() {
                    characterCount();
                });
                editor.on('keyup input', function() {
                    characterCount();
                });
                editor.on('paste', function() {
                    characterCount();
                });
                editor.on('change', function() {
                    characterCount();
                });
            }
        });

    });

    $(function() {
        $('#certificate_name').on('keypress', function(e) {
            if (e.which == 32) {
                return false;
            }
        });
    });
</script>
@endpush