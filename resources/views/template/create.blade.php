@extends('layouts.app')
@push('css')
<!-- Daterangepicker CSS -->
<link href="{{ asset('vendors/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<div class="hk-wrapper hk-vertical-nav">

    <div class="container mt-100">

        <section class="hk-sec-wrapper">
            <h5 class="hk-sec-title">Add Template</h5>
            <div class="row">
                <div class="col-sm-12">

                    <form id="templateForm" action="{{ route('backend.template.store') }}" method="POST" enctype=multipart/form-data>
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Campaign</label>
                                    <div class="col-md-8">
                                        <select class="form-control @error('campaign') is-invalid @enderror select2" name="campaign" id="campaign" class="form-control @error('campaign') is-invalid @enderror" value="{{old('sender_email')}}" required>
                                            <option value="">-- Select Campaign --</option>
                                            @foreach($campaigns as $value)
                                            <option value="{{ $value->id }}" {{ old('campaign') == $value->id ? 'selected' : null }}> {{$value->name}} </option>
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
                                        <input name="name" placeholder="Name" id="name" class="form-control @error('name') is-invalid @enderror" maxlength="60" value="{{old('name')}}" required>
                                        @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label">Sender Email</label>
                                    <div class="col-md-8">
                                        <input name="sender_email" placeholder="Sender Email" id="sender_email" class="form-control @error('sender_email') is-invalid @enderror" value="{{old('sender_email')}}" disabled>
                                        @error('sender_email')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label">Sender Name</label>
                                    <div class="col-md-8">
                                        <input name="sender_name" placeholder="Sender Name" id="sender_name" class="form-control @error('sender_name') is-invalid @enderror" value="{{old('sender_name')}}" disabled>
                                        @error('sender_name')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Email Subject</label>
                                    <div class="col-md-8">
                                        <input name="email_subject" placeholder="Email Subject" id="email_subject" class="form-control @error('email_subject') is-invalid @enderror" maxlength="60" value="{{old('email_subject')}}" required>
                                        @error('email_subject')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="email_type" class="col-sm-4 col-form-label required">Email Type</label>
                                    <div class="col-md-8">
                                        <select class="form-control @error('email_type') is-invalid @enderror" name="email_type" id="email_type" class="form-control @error('email_type') is-invalid @enderror" value="{{old('email_type')}}" required>
                                            <option value="">-- Select Email Type --</option>
                                            @foreach($email_type as $value)
                                            <option value="{{ $value }}" {{ old('email_type') == $value ? 'selected' : null }}> {{$value}} </option>
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
                                        <input type="file" name="header_image" id="header_image" accept=".jpg,.jpeg" class="form-control @error('header_image') is-invalid @enderror" required>
                                        @error('header_image')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                        <small id="header_image_helper" class="form-text text-muted">
                                            Maximum file size 100kb
                                        </small>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">Start Date</label>
                                    <div class="col-md-8">
                                        <input name="start_date" placeholder="YYYY-MM-DD HH:mm:ss" id="start_date" class="form-control @error('start_date') is-invalid @enderror datepicker" autocomplete="off" value="{{old('start_date')}}" required>
                                        @error('start_date')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="form-group row">

                                    <label for="" class="col-sm-4 col-form-label required">End Date</label>
                                    <div class="col-md-8">
                                        <input name="end_date" placeholder="YYYY-MM-DD HH:mm:ss" id="end_date" class="form-control @error('end_date') is-invalid @enderror" autocomplete="off" value="{{old('end_date')}}" required>
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
                                            <select class="form-control @error('font_type') is-invalid @enderror" name="font_type" id="font_type" class="form-control @error('font_type') is-invalid @enderror" value="{{old('font_type')}}" required>
                                                <option value="">-- Select Font Type --</option>
                                                @foreach($font_type as $key => $value)
                                                <option value="{{ $key }}" {{ old('font_type') == $key ? 'selected' : null }}> {{$value}} </option>
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
                                            <input name="font_color" type="color" placeholder="Font Color" id="font_color" class="form-control @error('font_color') is-invalid @enderror" required>
                                            @error('font_color')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Text Position</label>
                                        <div class="col-md-8">
                                            <select class="form-control @error('text_position') is-invalid @enderror" name="text_position" id="text_position" class="form-control @error('text_position') is-invalid @enderror" value="{{old('text_position')}}" required>
                                                <option value="">-- Select Text Position --</option>
                                                @foreach($text_position as $value)
                                                <option value="{{ $value }}" {{ old('text_position') == $value ? 'selected' : null }}> {{$value}} </option>
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
                                            <input name="certificate_name" placeholder="Certificate Name" id="certificate_name" class="form-control @error('certificate_name') is-invalid @enderror" value="{{old('certificate_name')}}" maxlength="65" required>
                                            @error('certificate_name')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">PDF Author</label>
                                        <div class="col-md-8">
                                            <input name="certificate_author" placeholder="PDF Author" id="certificate_author" class="form-control @error('certificate_author') is-invalid @enderror" maxlength="15" value="{{old('certificate_author')}}" required>
                                            @error('certificate_author')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">PDF Title</label>
                                        <div class="col-md-8">
                                            <input name="pdf_title" placeholder="PDF Title" id="pdf_title" class="form-control @error('pdf_title') is-invalid @enderror" maxlength="65" value="{{old('pdf_title')}}" required>
                                            @error('pdf_title')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group row">

                                        <label for="" class="col-sm-4 col-form-label required">Certificate Image</label>
                                        <div class="col-md-8">
                                            <input type="file" name="certificate_image" placeholder="PDF Title" id="certificate_image" accept=".jpg,.jpeg" class="form-control @error('certificate_image') is-invalid @enderror">
                                            @error('certificate_image')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                            <small id="certificate_image_helper" class="form-text text-muted">
                                                Maximum file size 300kb
                                            </small>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label for="">Email Body</label>
                                <div class="tinymce-wrap">
                                    <textarea name="email_body" class="tinymce" id="email_body" maxlength="1000">{{old('email_body')}}</textarea>
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

<!-- Slimscroll JavaScript -->
<script src="{{ asset('dist/js/jquery.slimscroll.js') }}"></script>

<!-- Fancy Dropdown JS -->
<script src="{{ asset('dist/js/dropdown-bootstrap-extended.js') }}"></script>

<!-- Tinymce JavaScript -->
<script src="{{ asset('vendors/tinymce/tinymce.min.js') }}"></script>

<script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('vendors/daterangepicker/daterangepicker.js') }}"></script>
<script>
    $(".select2").select2();
    url = "{{ route('backend.campaign.data') }}";
    checkCertificateForm();
    $(document).on('change', '#campaign', function(e) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                id: this.value
            },
            success: function(response) {
                if (response.success) {

                    $("#sender_name").val(response.data.sender_name);
                    $("#sender_email").val(response.data.sender_email);

                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Handle the error, show an error message, etc.
            }
        });
    });

    $(document).on('change', '#email_type', function(e) {
        checkCertificateForm();
    });

    function checkCertificateForm() {
        if ($("#email_type").val() == 'certificate') {
            $("#certificate").show().find("input,select").prop("required", true);
            $('#snippetCode').hide();
        } else {
            $("#certificate").hide().find("input,select").prop("required", false);
            $('#snippetCode').show();
        }
    }

    $(document).ready(function() {

        function snippetName() {
            // event.preventDefault();
            // let editorBody = tinymce.activeEditor.getBody();
            // tinymce.activeEditor.selection.setContent("$$name");
            // Mendapatkan referensi ke objek editor TinyMCE
            var editor = tinymce.get('email_body'); // Ganti 'editor-id' dengan ID sesuai dengan editor yang Anda miliki

            // Mengatur nilai atau teks ke editor
            var newText = "$$name";
            editor.insertContent(newText);
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
        "use strict";

        tinymce.init({
            selector: '.tinymce', // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: 'code lists link wordcount paste',
            height: 300,

            toolbar: 'bold italic underline | bullist numlist | link | code',

            setup: function(editor) {
                editor.on('init', function() {
                    characterCount();
                    editor.on('input', function() {
                        // Mendapatkan konten terbaru dan menghitung karakter
                        characterCount();
                    });

                    editor.on('keyup input', function() {
                        // Mendapatkan konten terbaru dan menghitung karakter
                        characterCount();
                    });

                    editor.on('paste', function() {
                        // Mendapatkan konten terbaru dan menghitung karakter
                        characterCount();
                    });
                    editor.on('change', function() {
                        // Mendapatkan konten terbaru dan menghitung karakter
                        characterCount();
                    });

                });
                
            }
        });
    });


    form = document.getElementById('templateForm');
    form.addEventListener("submit", function() {
        $("#sender_email").prop("disabled", false);
        $("#sender_name").prop("disabled", false);
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