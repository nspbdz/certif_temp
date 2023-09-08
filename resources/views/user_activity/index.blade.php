@extends('layouts.app')
@section('content')
<div class="container mt-xl-50 mt-sm-30 mt-15">

    <style>
        input,
        label {
            display: inline-block;
            vertical-align: middle;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            vertical-align: middle;
            margin: 10px 0;
        }
    </style>
    <div class="row">


        <div class="col-xl-12">
            <section class="hk-sec-wrapper">
                <div class="row">

                    <div class="col-sm-12 mb-4">
                        <div class="row justify-content-start">
                            <div class="col-12 col-sm-4">
                                <h2 class="hk-pg-title font-weight-600"> User Activity</h2>

                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="border col-lg-12 mb-4 ">

                            <form id="filter">
                                <div class="row mt-15">

                                    <div class="col-lg-3 mb-10 mt-10">
                                        <select id="username" name="username" class="form-control mt-10 w-10 select2">
                                            <option value="">--Select Username --</option>
                                            <option value="all">All User</option>
                                            @foreach ($data_username as $items) <option value="{{ $items->username }}">{{ $items->username }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-7 mb-10">
                                        <label for="start-date" class="col-form-label">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" min="1978-01-01" data-input="start_date" class="mt-20 focus:border-gh-red focus:ring-gh-red peer block w-full rounded border border-[#E6E6E6] pr-3 pl-2.5 pb-1.5 pt-3.5 text-sm text-gray-900" placeholder="dd / mm / yyyy" value="{{ old('start_date') }}" required>

                                        <label for="start-date" class="col-form-label">End Date</label>
                                        <input type="date" name="end_date" id="end_date" min="1978-01-01" data-input="end_date" class="mt-20 focus:border-gh-red focus:ring-gh-red peer block w-full rounded border border-[#E6E6E6] pr-3 pl-2.5 pb-1.5 pt-3.5 text-sm text-gray-900" placeholder="dd / mm / yyyy" value="{{ old('end_date') }}" required>

                                    </div>
                                    <div class="col-lg-2 mb-10 mt-10">
                                        <button type="submit" class="btn btn-sm btn-primary ">Filter</button>
                                        <button type="reset" class="btn btn-sm  btn-secondary" id="reset">Reset</button>
                                    </div>

                                </div>


                            </form>

                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-10">
                                <button id="download_excell" class="btn  btn-primary">download data</button>
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
                        <div class="table-wrap table-responsive">
                            <table id="datatable" class="table table-hover w-100 display pb-30 table-responsive-sm table-responsive-xs table-responsive-lg table-responsive-md">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold">Date</th>
                                        <th class="font-weight-bold">Username</th>
                                        <th class="font-weight-bold">Page</th>
                                        <th class="font-weight-bold">Activity</th>
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
</div>
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
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
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
            ajax: {
                url: "{{ route('backend.user_activity.datatable') }}",
                data: function(d) {
                    d.username = $('#username').val()
                    d.start_date = $('#start_date').val()
                    d.end_date = $('#end_date').val()
                }
            },
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                },

                {
                    data: 'username_dc',
                    name: 'username_dc',
                },

                {
                    data: null,
                    render: function(data, type, row) {
                        return row.page + '/' + row.action + ' ' + row.page;
                    }
                },

                {
                    data: null,
                    name: 'activity',
                    render: function(data, row) {
                        var activity = data.activity
                        var result = '';

                        for (var key in activity) {

                            // hasOwnProperty untuk untuk memeriksa apakah objek memiliki properti :disini key 
                            if (activity.hasOwnProperty(key)) {

                                if (key === 'extra') {
                                    result += '<br>' + activity[key] + '<br>';
                                } else if (key === 'id') {

                                    result += data.page + ' ' + key.toUpperCase() + ': ' + activity[key] + '<br>';

                                } else if (key === 'name') {
                                    result += data.page + ' ' + key.charAt(0).toUpperCase() + key.slice(1) + ': ' + activity[key] + '<br>';
                                } else {
                                    result += key + ': ' + activity[key] + '<br>';
                                }

                            }
                        }

                        // dan ditambahkan ke variabel result.
                        return result;
                    }
                },



            ],
        });


    })


    $(document).ready(function() {
        $('.select2').select2(); // Initialize Select2

        $('#reset').click(function() {
            $('#username').val(null).trigger('change');
        });
    });

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

                        $('#datatable').DataTable().ajax.reload(); // Reload the DataTable
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle the error, show an error message, etc.
                }
            });
        }
    });


    $('#filter').on("submit", function(event) {
        event.preventDefault(); // Mencegah reload halaman
        dtable.ajax.reload();
    });

    $(document).on('click', '#download_excell', function() {

        var username = $('#username').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        var url = "user_activity/export_excel/" + "?username=" + username + "&start_date=" + start_date + "&end_date=" + end_date;
        window.location.href = url;


    });
</script>


@endpush