@extends('pos.layouts.master')

@push('head-css')
    <style>
        .link-stats{
            cursor: pointer;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
@endpush

@section('content') 

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content p-3 text-muted">                        
                            <div class="d-flex justify-content-center justify-content-md-end mb-3">
                                
                            </div>
                            
                            <div class="table-responsive">
                                <table id="bookings_table" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>     
                                            <th>Customer Number</th>      
                                            <th>Customer Name</th>
                                            <th>Services</th>
                                            <th>Therapist</th>
                                            <th>Total Amount</th>
                                            <th>Time Slot</th>
                                            <th>Appointment Date</th>
                                            <th>Receipt</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>                        
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-js')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <script>

        $(document).ready(function () {
            
            $('#bookings_table').dataTable();

        });

    </script>
@endpush
