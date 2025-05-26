<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon.webp') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/favicon.webp') }}">
    <meta name="theme-color" content="#ffffff">


    <title>{{ $pageTitle . ' | ' . $settings->company_name}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --main-color: {{ $themeSettings->primary_color }};
            --active-color: {{ $themeSettings->secondary_color }};
            --sidebar-bg: {{ $themeSettings->sidebar_bg_color }};
            --sidebar-color: {{ $themeSettings->sidebar_text_color }};
            --topbar-color: {{ $themeSettings->topbar_text_color }};
        }

        nav.main-header #search .input-group-custom {
            max-width: 100%;
            width: 100%;
        }
        .visit_store
        {
            padding : 0px;
        }

        {!! $themeSettings->custom_css !!}
    </style>
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @stack('head-css')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-light border-bottom fixed-top align-items-sm-center align-items-start">
        <!-- Left navbar links -->
        <ul class="navbar-nav d-lg-none d-xl-none">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
            </li>
        </ul>
        <div class="row w-100">

            <div class="col-sm-10 mt-2">
                <div class="row">
                    <div class="col-sm-4 d-flex align-items-center visit_store">
                        <a class="visit-store hidden-sm hidden-xs" href="/" style="color: #fff;text-align: center;padding-left: 24px;font-size: 16px;font-weight: 500;margin-top: 5px;">
                            <i class="fa fa-desktop" style="padding-right: 8px;font-size: 16px;"></i> 
                            Visit Store
                        </a>                       
                    </div>

                    @if ($user->is_admin)
                        <div class="col-sm-8">
                            <form id="search" class="form-inline h-100 mx-3" action="{{ route('admin.search.store') }}" method="POST">
                                @csrf
                                <div class="input-group input-group-custom">
                                    <input name="search_key" id="search_key" class="form-control form-control-navbar" type="search" placeholder="@lang('front.searchBy')" aria-label="Search" autocomplete="off" required title="@lang('front.searchBy')" />
                                    <div class="input-group-append">
                                        <button id="search-button" class="btn btn-navbar" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @elseif($user->is_employee)
                        @if (Session::has('outlet_name'))
                            <div class="col-sm-8 d-flex align-items-center">
                                <h5 style="color: #fff; margin-top: 5px;">{{Session::get('outlet_name')}}</h5>
                            </div>
                        @endif                 
                    @endif
                    
                </div>
            </div>

            <div class="col-sm-2">
                <ul class="navbar-nav ml-auto pull-right">
                    <li class="dropdown">
                        <select class="form-control language-switcher">
                            @forelse($languages as $language)
                                <option value="{{ $language->language_code }}" @if($settings->locale == $language->language_code) selected @endif>
                                    {{ ucfirst($language->language_name) }}
                                </option>
                            @empty
                                <option value="en" @if($settings->locale == "en") selected @endif>
                                    English
                                </option>
                            @endforelse
                        </select>
                    </li>

                    <li class="profile-dropdown">
                        @if (Session::has('outlet_slug'))
                            <form id="logout-form" action="{{ route('outlet.logout', Session::get('outlet_slug') ?? '') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        @else
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        @endif
                        
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <img src="{{ $user->user_image_url }}" class="img img-circle" height="28em" width="28em" alt="User Image"> <i class="fa fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">                     
                            @if (Session::has('outlet_slug'))
                                <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                    <i class="fa fa-user mr-2"></i> @lang('menu.profile')
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('outlet.logout', Session::get('outlet_slug') ?? '') }}" title="Logout" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                    <i class="fa fa-power-off"></i>&nbsp; @lang('app.logout')
                                </a>
                            @else
                                <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                    <i class="fa fa-user mr-2"></i> @lang('menu.profile')
                                </a>

                                @permission('manage_settings')         
                                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                        <i class="nav-icon icon-settings"></i> @lang('menu.settings')
                                    </a>
                                @endpermission

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                    <i class="fa fa-power-off"></i>&nbsp; @lang('app.logout')
                                </a>
                            @endif
                        </div>
                    </li>
                </ul>
                <div class="row text-center">
                    <div class="col col-md-8">
                    </div>
                    <div class="col col-md-4">
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-danger">
        @if (Session::has('outlet_slug'))
            <!-- Brand Logo -->
            <a href="{{ route('outlet.dashboard', Session::get('outlet_slug')) }}" class="brand-link">
                <img src="{{ $settings->logo_url }}" alt=" Logo" class="brand-image">
                <span class="brand-text font-weight-light">&nbsp;</span>
            </a>
        @else
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ $settings->logo_url }}" alt=" Logo" class="brand-image">
                <span class="brand-text font-weight-light">&nbsp;</span>
            </a>
        @endif
        
        <!-- Sidebar -->
        <div class="sidebar">
            @include('layouts.sidebar')
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <div class="content pt-2">
            <div class="container-fluid">
                @yield('content')
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            <strong> &copy; {{ \Carbon\Carbon::today()->year }} {{ ucwords($settings->company_name) }}. </strong>
        </div>
        <!-- Default to the left -->
    </footer>
</div>
<!-- ./wrapper -->


{{--Ajax Medium Modal--}}
<div class="modal fade bs-modal-md in" id="application-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
                <button type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Medium Modal Ends--}}

{{--Ajax Large Modal--}}
<div class="modal fade bs-modal-lg in" id="application-lg-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-lg-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modalLgHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>
                <button type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
{{--Ajax Large Modal Ends--}}

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script>
    var redirecting = "{{ ' '.__('app.redirecting') }}"
</script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
    $('.select2').select2();
    $('.mytooltip').tooltip();

    $(window).resize(function () {
        $('.content').css('margin-top', $('nav.main-header').css('height'));
    }).resize();

    $('.language-switcher').change(function () {
        const code = $(this).val();
        let url = '{{ route('admin.changeLanguage', ':code') }}';
        url = url.replace(':code', code);

        $.easyAjax({
            url: url,
            type: 'POST',
            container: 'body',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.status == 'success') {
                    location.reload();
                }
            }
        })
    })

    $('#application-modal, #application-lg-modal').on('shown.bs.modal', function () {
        let firstTextInput = $(this).find('.form-group>input[type="text"]').first();

        if (firstTextInput.length > 0) {
            if (firstTextInput.val() !== '') {
                $(this).find('.form-group>input[type="text"]').first().trigger('select');
            }
            else {
                $(this).find('.form-group>input[type="text"]').first().trigger('focus');
            }
        }
    })

    function languageOptions() {
        return {
            processing:     "@lang('modules.datatables.processing')",
            search:         "@lang('modules.datatables.search')",
            lengthMenu:    "@lang('modules.datatables.lengthMenu')",
            info:           "@lang('modules.datatables.info')",
            infoEmpty:      "@lang('modules.datatables.infoEmpty')",
            infoFiltered:   "@lang('modules.datatables.infoFiltered')",
            infoPostFix:    "@lang('modules.datatables.infoPostFix')",
            loadingRecords: "@lang('modules.datatables.loadingRecords')",
            zeroRecords:    "@lang('modules.datatables.zeroRecords')",
            emptyTable:     "@lang('modules.datatables.emptyTable')",
            paginate: {
                first:      "@lang('modules.datatables.paginate.first')",
                previous:   "@lang('modules.datatables.paginate.previous')",
                next:       "@lang('modules.datatables.paginate.next')",
                last:       "@lang('modules.datatables.paginate.last')",
            },
            aria: {
                sortAscending:  "@lang('modules.datatables.aria.sortAscending')",
                sortDescending: "@lang('modules.datatables.aria.sortDescending')",
            },
        }
    }

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
        return true;
    }


</script>

@stack('footer-js')

</body>
</html>
