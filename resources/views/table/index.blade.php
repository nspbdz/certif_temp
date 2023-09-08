@extends('layouts.app')
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
                            <div class="col-12 col-sm-8">
                                <h2 class="hk-pg-title font-weight-600">List Submission</h2>
                            </div>
                            <div class="col-12 col-sm-4 text-right">

                            </div>
                        </div>
                        @if ($errors->any())
                        <div class="alert alert-info">
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
                                        <th class="font-weight-bold">Sender Email</th>
                                        <th class="font-weight-bold">Sender Name</th>
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
<script src="{{ asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('vendors/jszip/dist/jszip.min.js') }}"></script>
<script src="{{ asset('vendors/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendors/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-waitingfor.min.js') }}"></script>
<script>
    var dtable;
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
                [0, "desc"]
            ],

            ajax: {
                url: "{{ route('backend.user_activity_datatable') }}",
                data: function(d) {
                    d.job = $('#job').val()
                    d.date = $('#date').val()
                }
            },
            columns: [

                {
                    targets: 1,
                    data: 'name',
                    name: 'name',
                    orderable: false,
                    searchable: true
                },
                {
                    targets: 2,
                    data: 'sender_email',
                    name: 'sender_email',
                    orderable: false,
                    searchable: true
                },
                {
                    targets: 3,
                    data: 'sender_name',
                    name: 'sender_name',
                    orderable: false,
                    searchable: true
                },
                {
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    "render": function(data, type, row) {
                        var id = row.id;
                        var url = "{{ route('backend.table.detail') }}"

                        console.log("row", id);
                        // return "-"
                        return ` <a href="${url}/${id}" type="button" id="btn-reset" class="btn btn-success btn-sm" style="flex-grow: 1;">
                                        
                                        Detail Input
                                    </a>`

                        ;

                    }
                }


            ],
        });
        $('#job').on("change", function() {
            dtable.ajax.reload();
        });
        $('#status_render').on("change", function() {
            dtable.ajax.reload();
        });
        $('#status_email').on("change", function() {
            dtable.ajax.reload();
        });


    })
</script>


@endpush