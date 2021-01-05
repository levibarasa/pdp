@extends('scafold.admin')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                    <li class="breadcrumb-item"><a href="dashboard-one.html#">PDP</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Volunteers</li>
                </ol>
            </nav>
        </div>

    </div>
    <div class="container pd-x-0">



        <div data-label="owners" class="df-example demo-table">
            <table id="owners" class="table">
                <thead>
                <tr>
                    <th class="wd-15p">First Name</th>
                    <th class="wd-15p">Last Name</th>
                    <th class="wd-15p">Phone Number</th>
                    <th class="wd-15p">Email</th>
                    <th class="wd-15p">County</th>
                    <th class="wd-22p"> Volunteer Contacted</th>
                    <th class="wd-20p">Action</th>
                </tr>
                </thead>
            </table>
        </div>


        @stop
        @push("scripts")

            <script src="../lib/prismjs/prism.js"></script>
            <script src="../lib/datatables.net/js/jquery.dataTables.min.js"></script>
            <script src="../lib/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
            <script src="../lib/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
            <script src="../lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js"></script>
            <script src="../lib/select2/js/select2.min.js"></script>
            <script src="../lib/parsleyjs/parsley.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>


                <script>
                $(function () {

                    'use strict'


                    var url = "{!! url('volunteers')!!}";
                    $('#owners').DataTable({
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        ajax: url,
                        language: {
                            searchPlaceholder: 'Search...',
                            sSearch: '',
                            lengthMenu: '_MENU_ items/page',
                        },
                        /*"columnDefs": [
                            {"width": "40%", "targets": 3},
                            {"width": "10%", "targets": 1}
                        ],
                        "pageLength": 50,
                        'order': [[4, 'desc']],*/

                        columns: [

                            /*{data: 'project_id', name: 'project_id'},*/
                            {data: 'firstname', name: 'firstname'},
                            {data: 'lastname', name: 'lastname'},
                            {data: 'phonenumber', name: 'phonenumber'},
                            {data: 'email', name: 'email'},
                            {data: 'county', name: 'county'},
                            {
                            data: 'status',
                            render:function (data) {
                                if(data == '1'){
                                    return '<span class="badge badge-success"> Yes</span>';
                                }
                                if(data == '0'){
                                    return '<span class="badge badge-danger"> No</span>';
                                }
                        },
                    },
                            {data: 'action', name: 'action', orderable: false, searchable: false}
                        ],
                        "drawCallback": function () {

                        },
                        "initComplete": function () {

                        }
                    });









                });
            </script>

            <script>

                $("#select2").select2({
                    dropdownParent: $("#usersModal"),
                    placeholder: 'Choose one',
                    allowClear: true,
                    maximumSelectionLength: 2
                });


            </script>
            <script>
        function deleteData(dt){
            if(confirm("Are you sure you want to Disable this Event?")){
                $.ajax({
                    Type:'DELETE',
                    url:$(dt).data("url"),
                    data:{
                        "_token":"{{ csrf_token() }}"
                    },
                    success:function(response){
                        if(response.status){
                            location.reload();
                            console.log(response);
                        }
                    },
                    error:function(response){
                        console.log(response);
                    }
                });
            }
            return false;
        }
    </script>

    @endpush
