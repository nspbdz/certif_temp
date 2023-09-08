@extends('layouts.app')
@push('css')
<!-- Toastr CSS -->

<link href="{{ asset('vendors/jquery-toast-plugin/dist/jquery.toast.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
<!-- Container -->
<style>
    table.dataTable thead .sorting_asc {
        background-image: none !important;
    }

    table.dataTable thead .sorting_desc {
        background-image: none !important;
    }

    /* ditambahkan agar tidak menggunakan segitiga sort pada th  */
</style>

<div class="container mt-xl-50 mt-sm-30 mt-15">

    <div class="row">

        <div class="col-xl-12">
            <section class="hk-sec-wrapper">
                <div class="row">

                    <div class="col-sm-12 mb-4">
                        <div class="row justify-content-end">
                            <div class="col-12 col-sm-4">
                                <h2 class="hk-pg-title font-weight-600"> Campaign</h2>
                            </div>

                            <div class="col-12 col-sm-4 text-right">

                            </div>
                        </div>
                        <div class="col-sm-12 mb-4">

                            <div class="col-12 col-sm-8">

                                <a href="/campaign/create">
                                    <button class="btn btn-sm btn-primary">Add Campaign</button>
                                </a>
                            </div>
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
                                        <th class="font-weight-bold">Name</th>
                                        <th class="font-weight-bold">Sender Email</th>
                                        <th class="font-weight-bold">Sender Name</th>
                                        <th class="font-weight-bold">Created Date</th>
                                        <th class="font-weight-bold">Total Success Sent</th>
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
@endsection

@push('js')
<!-- Data Table JavaScript -->
<script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<!-- Toastr JS -->
<script src="{{ asset('vendors/jquery-toast-plugin/dist/jquery.toast.min.js') }}"></script>

<script>
    var dtable;
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        dtable = $('#datatable').DataTable({
            order: [
                [3, 'desc']
            ],
            processing: true,
            serverSide: true,
            responsive: false, // dirubah untuk menghilangkan arrow pada td 
            ajax: {
                url: "{{ route('backend.campaign.datatable') }}",

            },
            columns: [

                {
                    data: 'name',
                    name: 'name',
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
                    data: 'total_data',
                    name: 'total_data',
                },
                {
                    data: 'recipients_status',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    "render": function(data, type, row) {
                        var id = row.campaigns_id;
                        var deleteUrl = "campaign/" + id; // Assuming this is the delete URL

                        return `
                                <a href="campaign/${id}/edit" type="button" id="btn-reset" class="btn btn-icon btn-success btn-icon-style-1" style="flex-grow: 1;">
                                    <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span>
                                </a>
                                ${data !== 'success' ? 
                                    `<button type="button" class="btn btn-danger btn-icon delete-campaign btn-icon-style-1" data-url="${deleteUrl}">
                                        <span class="btn-icon-wrap"><i class="fa fa-trash"></i></span>
                                    </button>` 
                                    : ''}
                                `;

                    }
                }


            ],
        });


    })

    // Handle click event for the delete button
    $(document).on('click', '.delete-campaign', function() {
        var url = $(this).data('url');

        if (confirm('Are you sure you want to delete this campaign?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        $.toast().reset('all'); // menambahkan tost success 
                        $("body").removeAttr('class').removeClass("bottom-center-fullwidth").addClass("top-center-fullwidth");
                        $.toast({
                            text: '<i class="jq-toast-icon ti-face-smile"></i><p>' + response.message + '</p>',
                            position: 'top-center',
                            loaderBg: '#198754',
                            class: 'jq-has-icon jq-toast-dark bg-success',
                            hideAfter: 13500,
                            stack: 6,
                            showHideTransition: 'fade'
                        });
                        // return false;
                        // Campaign deleted successfully
                        // Perform any necessary DOM manipulation or show a success message
                        // For example, you can remove the row from the DataTable
                        $('#datatable').DataTable().ajax.reload(); // Reload the DataTable
                    }
                },
                error: function(xhr, status, error) {
                    alert('Campaign ini tidak dapat di delete, karena email sudah terkirim'); // Show an alert with the error message
                }

            });
        }
    });
</script>


@endpush