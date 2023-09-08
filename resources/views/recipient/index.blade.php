@extends('layouts.app')
@section('content')
<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-light bg-transparent">
        <li class="breadcrumb-item active" aria-current="page">{{$template->campaign_name}}</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{route('backend.template.index')}}?campaign={{$template->campaign_id}}">{{$template->name}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Recipient</li>

    </ol>
</nav>
<!-- Container -->
<div class="container mt-xl-50 mt-sm-30 mt-15">

    <!-- Row -->
    <div class="row">
        <div class="col-xl-12">
            <section class="hk-sec-wrapper">
                <div class="row">
                    <div class="col-sm-12 mb-4">
                        <div class="row justify-content-end">
                            <div class="col-12 col-sm-4">
                                <h2 class="hk-pg-title font-weight-600"> </h2>
                            </div>
                            <div class="col-12 col-sm-4 text-right">
                            </div>
                        </div>
                        <div class="col-sm-12 mb-4">
                            <div class="progress progress-bar-rounded mb-20">
                                <div class="progress-bar bg-success" id="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p style=" text-align: center;">
                                <span>{{$recipients['success']}}</span>
                                /
                                <span>{{$recipients['all']}}</span>
                            </p>
                            <div class="col-12 col-sm-8">

                                <div class="actions">
                                    <label for="file-upload" class="custom-file-upload">
                                        <a class="btn m-b-s w-s btn-primary" id="btn-import">Add Recipient</a>
                                    </label> <span class="feather-icon" data-toggle="tooltip" data-placement="right" data-html="true" data-original-title="File must be in XLS or <br> XLSX format <br> Max filesize 5MB"><i data-feather="info"></i></span>
                                    <form name="myForm" action="{{route('backend.recipient.import')}}/{{$template->id}}" method="post" autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input id="file-upload" name="import" onchange="this.form.submit();" style="display:none" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                    </form>
                                </div>

                            </div>
                            <div class="col-12 col-sm-12">
                                <a href="{{route('backend.recipient.download')}}?type={{$template->email_type}}" class="text-primary">
                                    Download Sample Usert List {{ucfirst($template->email_type)}}
                                </a>
                            </div>
                            <div class="row">
                                <div class="col-6 col-sm-6">
                                    <form class="form-inline">
                                        <div class="form-group col-sm-12">
                                            <label for="filter"> Filter </label>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="status">
                                                    <option value="">-- All Status --</option>
                                                    @foreach($status as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-6 col-sm-6" style="text-align: right;">
                                    <button class="btn btn-danger delete-button">Delete Pending Data</button>

                                    <button class="btn btn-success send-button">Send</button>

                                </div>
                            </div>
                        </div>

                        @if (session('error'))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach(session('error') as $msg)
                                <li>{{$msg}}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <div class="col-sm">
                        <div class="table-wrap">
                            <table id="datatable" class="table table-hover w-100 display pb-30">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold">Nama</th>
                                        <th class="font-weight-bold">Email</th>
                                        <th class="font-weight-bold">Ticket Code</th>
                                        <th class="font-weight-bold">Created Date</th>
                                        <th class="font-weight-bold">Action</th>
                                        <th class="font-weight-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- /Row -->
</div>
<!-- /Container -->
{{-- modal --}}
@endsection

@push('js')
<!-- Data Table JavaScript -->
<script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('vendors/jszip/dist/jszip.min.js') }}"></script>
<script src="{{ asset('vendors/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendors/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script>
    var dtable;

    var progress = document.querySelector("#progress");
    var pending = "{{$recipients['pending']}}";
    var i = "{{$recipients['success']}}";
    var all = "{{$recipients['all']}}";
    var reporter = document.querySelector("p > span");
    var allprogress = document.querySelector("p > span:last-child");


    progress.style.width = Math.floor(i * 100 / all) + "%";

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        dtable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            scrollX: true,
            order: [
                [0, "asc"]
            ],

            ajax: {
                url: "{{ route('backend.recipient.datatable') }}",
                data: function(d) {
                    d.status = $('#status').val()
                    d.templateId = "{{request()->route('id')}}"
                    d.emailType = "{{$template->email_type}}"
                }
            },
            columns: [

                {
                    data: 'name',
                    name: 'name',
                    orderable: true,

                },
                {
                    data: 'email',
                    name: 'email',
                },

                {
                    data: 'ticket_code',
                    name: 'ticket_code',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var id = row.id;
                        var deleteUrl = "template/" + id; // Assuming this is the delete URL
                        var active = Number("{{$template->active}}");

                        var retry_button = '';
                        if ((row.status == 'success' || row.status == 'failed') && active) {
                            retry_button = ` 
                                    <button data-id="${row.id}" id="retry-button" class="btn btn-success btn-icon-style-1 retry-button" style="flex-grow: 1;">
                                    <span class="btn-icon-wrap">Retry</span>
                                    </button>
                                    `;
                        }
                        return retry_button;

                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        switch (row.status) {
                            case 'pending':
                                return '<span class="badge badge-info badge-rounded">pending</span>';
                                break;
                            case 'sending':
                                return '<span class="badge badge-warning badge-rounded">sending</span>';
                            case 'success':
                                return '<span class="badge badge-success badge-rounded">success</span>';
                            case 'failed':
                                return '<span class="badge badge-danger badge-rounded">failed</span>';
                            default:
                                return '<span class="badge badge-info badge-rounded">pending</span>';
                                break;
                        }
                    }

                }


            ],
        });

        function toggleColumnVisibility() {
            var columnIndexToHide = 2; // Indeks kolom yang akan disembunyikan
            var hideColumn = false;

            // Logika kondisi untuk menyembunyikan/menampilkan kolom
            type = "{{$template->email_type}}";
            if (type == 'certificate')
                hideColumn = true;

            // Menyembunyikan atau menampilkan kolom berdasarkan kondisi
            dtable.column(columnIndexToHide).visible(!hideColumn);
        }

        toggleColumnVisibility();

        // Event listener untuk mengupdate tampilan saat ada perubahan dalam tabel
        dtable.on('draw', function() {
            toggleColumnVisibility();
        });

    })

    // Handle click event for the delete button
    $(document).on('click', '.delete-button', function() {
        var url = "{{route('backend.recipient.delete')}}";

        if (confirm('Are you sure you want to delete this recipients?')) {
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: "{{$template->id}}"
                },
                success: function(response) {
                    if (response.success) {
                        // template deleted successfully
                        // Perform any necessary DOM manipulation or show a success message
                        // For example, you can remove the row from the DataTable
                        $('#datatable').DataTable().ajax.reload(); // Reload the DataTable
                        updateProgress();
                        checkSendEmailButton();
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle the error, show an error message, etc.
                }
            });
        }
    });

    $(document).on('click', '.send-button', function() {
        var url = "{{route('backend.recipient.send')}}";
        var active = '{{$template->active}}';
        if (active == '0') {
            alert('The sending button is currently not active due to the template not yet starting or already being over. Please wait for the template to begin or check the template schedule for the designated sending time.');
            return false;
        }

        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                id: "{{$template->id}}"
            },
            success: function(response) {
                if (response.success) {
                    // template deleted successfully
                    // Perform any necessary DOM manipulation or show a success message
                    // For example, you can remove the row from the DataTable
                    $('#datatable').DataTable().ajax.reload(); // Reload the DataTable
                    updateProgress();
                    checkSendEmailButton();
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Handle the error, show an error message, etc.
            }
        });
        // }
    });

    $(document).on('click', '.retry-button', function() {
        var url = "{{route('backend.recipient.retry')}}";
        var id = $(this).data('id');
        // if (confirm('Are you sure you want to delete this recipients?')) {
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                id: id,
                template_id: "{{$template->id}}"
            },
            success: function(response) {
                if (response.success) {
                    // template deleted successfully
                    // Perform any necessary DOM manipulation or show a success message
                    // For example, you can remove the row from the DataTable
                    $('#datatable').DataTable().ajax.reload(); // Reload the DataTable
                    updateProgress();
                    checkSendEmailButton();
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Handle the error, show an error message, etc.
            }
        });
        // }
    });

    $(document).on('change', '#status', function(e) {
        dtable.ajax.reload();
        updateProgress();
        checkSendEmailButton();
    });

    updateProgress();

    function updateProgress() {
        let template_id = "{{$template->id}}";
        $.ajax({
            url: "{{route('backend.recipient.get_status_total',$template->id)}}",
            type: "GET",
            success: function(data) {
                progress.style.width = Math.floor(data.success * 100 / data.all) + "%";
                pending = data.pending;
                allprogress.textContent = data.all;

                checkSendEmailButton();
            },
            error: function(data) {
                allprogress.textContent = 'error';
                clearInterval(interval);
            },
        });
    }

    checkSendEmailButton();

    function checkSendEmailButton() {
        if (Number(pending) == 0) {
            $('.send-button').attr('disabled', 'disabled');
            $('.delete-button').attr('disabled', 'disabled');
        } else {
            $('.send-button').removeAttr('disabled');
            $('.delete-button').removeAttr('disabled');
        }
    }
</script>


@endpush