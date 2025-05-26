@extends('layouts.master')

@push('head-css')
    <style>
        .widget-user .widget-user-image>img {
            width: 7em;
            height: 7em;
        }
    </style>
@endpush

@section('content')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-light">
                <div class="card-header">
                    <div class="card-body">
                        @permission('create_customer')
                            <div class="d-flex justify-content-center justify-content-md-end mb-3">
                                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-1"><i
                                        class="fa fa-plus"></i> @lang('app.createNew')</a>
                            </div>
                        @endpermission
                    </div>
                    
                    <form action="" method="get" id="customer_search_filter_form">
                        <div class="row">
                            <div class="col-lg-4">
                                {{-- <div class="form-group">                             
                                    <input class="form-control form-control-lg" type="text" name="customer_search" id="customer-search" placeholder="@lang('modules.customer.search')" @isset($customer_search) value="{{ $customer_search }}" @endisset>                            
                                </div> --}}
                                
                                <div class="form-group d-flex align-items-center">                             
                                    <input class="form-control form-control-lg mr-2" type="text" name="customer_search" id="customer-search" placeholder="@lang('modules.customer.search')" @isset($customer_search) value="{{ $customer_search }}" @endisset>                            
                                    <button type="button" class="btn btn-primary btn-lg" id="search_btn">Search</button>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" name="filter_oulet_id" id="filter_oulet_id" onchange="this.form.submit()">
                                        <option value="">Filter by Outlet</option>
                                        @foreach ($outlet as $item)
                                            <option value="{{$item->id}}" @isset($filter_oulet_id) @if ($filter_oulet_id == $item->id) selected @endif @endisset>{{$item->outlet_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" name="customer_sort" id="customer_sort" onchange="this.form.submit()">
                                        <option value="">Sort By</option>
                                        <option value="newest" @isset($customer_sort) @if ($customer_sort == 'newest') selected @endif @endisset>Newest</option>
                                        <option value="oldest" @isset($customer_sort) @if ($customer_sort == 'oldest') selected @endif @endisset>Oldest</option>
                                        <option value="alphabetically_asc" @isset($customer_sort) @if ($customer_sort == 'alphabetically_asc') selected @endif @endisset>Alphabetically (A-Z)</option>
                                        <option value="alphabetically_desc" @isset($customer_sort) @if ($customer_sort == 'alphabetically_desc') selected @endif @endisset>Alphabetically (Z-A)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>                   
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="row" id="customer-list">
                        @forelse($customers as $customer)
                            <div class="col-md-3">
                                <!-- Widget: user widget style 1 -->
                                <div class="card card-widget widget-user customer-card"
                                    onclick="location.href='{{ route('admin.customers.show', $customer->id) }}'">
                                    <!-- Add the bg color to the header using any of the bg-* classes -->
                                    <div class="widget-user-header text-white" style="background-color: var(--active-color)">
                                        
                                        @if($customer->status == "inactive")
                                            <span class="badge badge-danger" style="position: absolute; top: 10px; right: 10px;">Inactive</span>
                                        @endif
                                        
                                        {{-- <h5 class="widget-user-username">{{ ucwords($customer->fname) }}
                                            {{ ucwords($customer->lname) }}</h5> --}}
                                        <h5 class="widget-user-username">{{ ucwords($customer->name) }}</h5>
                                        <h6 class="widget-user-desc"><i class="fa fa-envelope"></i>
                                            {{ $customer->email ?? '--' }}</h6>
                                        <h6 class="widget-user-desc"><i class="fa fa-phone"></i>
                                            {{ $customer->mobile ? $customer->formatted_mobile : '--' }}</h6>
                                    </div>
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ $customer->user_image_url }}"
                                            alt="User Avatar">
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-sm-6 border-right">
                                                <div class="description-block">
                                                    <h5 class="description-header">{{ count($customer->completedBookings) }}
                                                    </h5>
                                                    <span class="description-text">@lang('menu.bookings')</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-sm-6">
                                                <div class="description-block">
                                                    <h5 class="description-header">
                                                        {{ $customer->created_at->translatedFormat($settings->date_format) }}
                                                    </h5>
                                                    <span class="description-text">@lang('modules.customer.since')</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                            <!-- /.col -->

                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                                <!-- /.widget-user -->
                            </div>
                        @empty
                            <div class="col-md-4">
                                @lang('messages.noRecordFound')
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-js')
    {{-- <script>
        $(document).ready(function() {


            const showCustomerList = (take = {{ $recordsLoad }}) => {
                let param = $('#customer-search').val();

                $.easyAjax({
                    type: 'GET',
                    url: '{{ route('admin.customers.index') }}',
                    data: {'param': param, 'take': take},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#customer-list').html(response.view);
                        }
                    }
                });
            };

            $('#customer-search').keyup(function () {
                showCustomerList();
            });

            $('body').on('click', '#load-more', function () {
                let take = $(this).data('take');
                showCustomerList(take);
            });

            showCustomerList();

        });
    </script> --}}

    <script>
        $("body").on('click', '#search_btn', function(){

            $('#customer_search_filter_form').submit();

        });
    </script>
@endpush
