@extends('layouts.app')
@push('css')
<style>
    td {
        font-size: 14px;
    }
</style>
@endpush
@section('content')
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
                                <h2 class="hk-pg-title font-weight-600"> Template</h2>
                            </div>
                            <div class="col-12 col-sm-4 text-right">
                            </div>
                        </div>
                        <div class="col-sm-12 mb-4">
                            <div class="col-12 col-sm-8">
                                <a href="/template/create">
                                    <button class="btn btn-sm btn-primary">Add Template</button>
                                </a>

                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <form class="form-inline">
                                <div class="form-group col-sm-12">
                                    <label for="filter">Filter</label>
                                    <div class="col-sm-6">
                                        <select class="form-control @error('campaign') is-invalid @enderror select2" name="campaign" id="campaign" class="form-control @error('campaign') is-invalid @enderror" value="{{old('sender_email')}}">
                                            <option value="">All Campaign</option>
                                            @foreach($campaigns as $value)
                                            <option value="{{ $value->id }}" {{ old('campaign') == $value->id ? 'selected' : null }} @selected($value->id==request('campaign'))> {{$value->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </form>
                        </div>
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                    </div>

                    <div class="col-sm">
                        <div class="table-wrap table-responsive">
                            <table id="datatable" class="table table-hover w-100 display pb-30 table-responsive-sm table-responsive-xs table-responsive-lg table-responsive-md">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold">Nama</th>
                                        <th class="font-weight-bold">Email Type</th>
                                        <th class="font-weight-bold">Sender Email</th>
                                        <th class="font-weight-bold">Sender Name</th>
                                        <th class="font-weight-bold">Created Date</th>
                                        <th class="font-weight-bold">User</th>
                                        <th class="font-weight-bold">Image</th>
                                        <th class="font-weight-bold">Action</th>
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

<div class="modal fade" id="image-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Header Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="imageModal" src="#" width="100%">
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<!-- Data Table JavaScript -->
<script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script>
    let dtable;
    $(".select2").select2();
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
            // scrollX: true,
            order: [
                [4, "desc"]
            ],

            ajax: {
                url: "{{ route('backend.template.datatable') }}",
                data: function(d) {
                    d.campaign = $("#campaign").val();
                }
            },
            columns: [

                {
                    data: 'name',
                    name: 'name',
                    orderable: true,

                },
                {
                    data: 'email_type',
                    name: 'email_type',
                },
                {
                    data: 'sender_email',
                    name: 'sender_email',
                },
                {
                    data: 'sender_name',
                    name: 'sender_name',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    name: 'recipient',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var id = row.id;
                        var deleteUrl = "template/" + id; // Assuming this is the delete URL
                        return ` 
                                    <a href="{{route('backend.recipient.index')}}/${id}" type="button" id="btn-reset" class="btn-sm btn-success btn-icon-style-1">
                                    Recipent
                                    </a>
                                    `;

                    }
                },
                {
                    name: 'image',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var id = row.id;
                        var deleteUrl = "template/" + id; // Assuming this is the delete URL
                        var certif_button = '';
                        var header_image_button = '';
                        var getImageUrl = "template/image";
                        if (row.email_type == 'certificate' && row.certificate_image) {
                            certif_button = `<button class="btn btn-sm btn-primary certificate-image btn-icon-style-1 mb-1 mx-1" data-url="${getImageUrl}"  data-id="${id}"data-title="Certificate Image" id="certificate_image">
                                    Certificate
                                    </button> `;
                        }
                        if (row.header_image) {
                            header_image_button = ` 
                                    <button class="btn btn-sm btn-primary header-image btn-icon-style-1 mb-1 mx-1" data-url="${getImageUrl}" data-id="${id}" data-title="Header Image" id="header_image">
                                    Header Image
                                    </button>`;
                        }
                        return header_image_button + certif_button;
                    }
                },
                {
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var id = row.id;
                        var deleteUrl = "template/" + id; // Assuming this is the delete URL
                        return ` 
                                    <a href="template/${id}/edit" type="button" id="btn-reset" class="btn btn-sm btn-icon btn-success btn-icon-style-1 mb-1" style="flex-grow: 1;">
                                    <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-icon delete-template btn-icon-style-1 mb-1" data-url="${deleteUrl}">
                                    <span class="btn-icon-wrap"><i class="fa fa-trash"></i></span>
                                    </button>
                                    `;

                    }
                }


            ],
        });
    });

    $(document).on('change', '#campaign', function(e) {
        dtable.ajax.reload();
    });

    $(document).on('click', '.testbutton', function(e) {
        console.log(this.getAttribute('data'));
    });

    $(document).on('click', '.header-image, .certificate-image', function(e) {
        var url = $(this).data('url');
        var id = $(this).data('id');
        var title = $(this).data('title');
        var name = $(this).attr('id');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                id: id
            },
            success: function(response) {
                $(".modal-title").text(title);
                $("#imageModal").attr('src', "{{config('config.akcdn.full_url')}}" + response.data[name] + "?m=1");
                $("#image-modal").modal('show');
            },
        });
    });


    // Handle click event for the delete button
    $(document).on('click', '.delete-template', function() {
        var url = $(this).data('url');

        if (confirm('Are you sure you want to delete this template?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        alert('Template Deleted Success');
                        $('#datatable').DataTable().ajax.reload(); // Reload the DataTable                     
                    } else {
                        alert('Delete Failed');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle the error, show an error message, etc.
                }
            });
        }
    });
</script>


@endpush