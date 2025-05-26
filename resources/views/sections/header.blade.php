<header class="header">
    <div class="head-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-12 my-lg-0 my-2">
                    <ul class="head-contact-left">
                        <li>
                            <i class="fa fa-phone"></i>
                            {{ $settings->company_phone }}
                        </li>

                    </ul>
                </div>
                <div class="col-lg-8 col-12 my-lg-0 my-2">
                    <ul class="head-contact-right">
                        <li class="location-search mb-3">
                            <span class="mr-2"> @lang('front.location')</span>
                            <div class="location-dropdown">
                                <div id="scrollable-dropdown-menu" class="input-wrap">
                                    <i class="fa fa-map-marker"></i>
                                    <select id="location" class="select2" name="location">
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="language-drop mb-3">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle text-capitalize" data-toggle="dropdown">
                                    <i class="fa fa-globe"></i> @lang('front.language')
                                </a>
                                <div class="dropdown-menu">
                                    @forelse ($languages as $language)
                                        <a class="dropdown-item" data-lang-code="{{ $language->language_code }}" href="javascript:;">{{ $language->language_name }}</a>
                                    @empty
                                        <a class="dropdown-item active" href="javascript:;" data-lang-code="en">English</a>
                                    @endforelse
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            @if($user)
                                <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <div class="dropdown add-items">
                                        <a href="javascript:;" class="dropdown-toggle"
                                           data-toggle="dropdown" aria-expanded="false">{{ $user->name }}<span class="fa fa-caret-down"></span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                                <i class="fa fa-user"></i> @lang('front.myAccount')</a>
                                            <a class="dropdown-item" href="javascript:;" onclick="logoutUser(event)">
                                                <i class="fa fa-sign-out mr-2"> </i>@lang('app.logout')</a>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <a href="{{ route('login') }}">
                                    <i class="fa fa-user mr-2"> </i>@lang('app.signIn')
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <nav class="topbar">
        <div class="container">
            <div class="row h-center">
                <div class="col-lg-5 col-md-3 col-12">
                    <div class="logo">
                        <a href="{{ route('front.index') }}">
                            <img src="{{ $frontThemeSettings->logo_url }}" alt="logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-7 col-md-9 col-12">
                    <ul class="d-flex h-center justify-content-md-end py-3 ml-md-5 ml-0">
                        <li class="search-form">
                            <form id="searchForm" action="{{ route('front.searchServices') }}" method="GET">
                                <span class="input-wrap">
                                    <i class="fa fa-search"></i>
                                    <input type="text" class="form-control" name="search_term" id="search_term"
                                        placeholder="@lang('front.searchHere')" autocomplete="none">
                                </span>
                                <button type="submit" class="submit btn btn-custom br-0 btn-dark w-100">
                                    @lang('front.search')</button>
                            </form>
                        </li>
                        <li title="@lang('front.cart')" class="top-cart">
                            <a href="{{ route('front.cartPage') }}">
                                <i class="fa fa-shopping-bag"></i>
                                <span class="cart-badge">{{ $productsCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

@push('footer-script')
    <script>
        var substringMatcher = function(strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function(i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };

        $(function () {

            // var countries = {!! $locations !!}.map(location => location.name);
            // countries.unshift('@lang('front.allLocations')');

            $('.select2').select2();

            if (localStorage.getItem('location')) {
                $('#location.select2').val(localStorage.getItem('location')).trigger('change');
            }
            // else {
                //     $('#location.select2').val(0);
                //     localStorage.setItem('location', 0);
                // }

                $('#location.select2').on('change', function() {
                    localStorage.setItem('location', $(this).val());

                    if (localStorage.getItem('location') !== '' && location.protocol+'//'+location.hostname+location.pathname == '{{ route('front.searchServices') }}') {
                        $('#searchForm').submit();
                    }

                    if (localStorage.getItem('location') !== '' && location.href == '{{ route('front.index').'/' }}') {
                        var url = '{{ route('front.index', ['location' => 'variable']) }}';
                        url = url.replace('variable', localStorage.getItem('location'));

                        $.easyAjax({
                            url: url,
                            type: 'GET',
                            success: function (response) {

                            // $('.cart-badge').text('0');

                            var deals = '';

                            if (response.deals.length > 0) {

                                $('#deals_section').show();
                                response.deals.forEach(deal => {
                                    var deal_type = deal.deal_type=='' ? "{{__('app.offer')}}" : "{{__('app.combo')}}";

                                    deals += `
                                    <div class="owl-item">
                                    <div class="listing-item">
                                        <div class="img-holder" style="background-image: url('${deal.deal_image_url}')">
                                            <div class="category-name">
                                                <i class="flaticon-fork mr-1">${deal_type}</i>
                                            </div>
                                        </div>
                                        <div class="list-content">
                                            <h5 class="mb-2">
                                            <a href="${deal.deal_detail_url}">${deal.title}</a>
                                            </h5>
                                            <div><i class="fa fa-map-marker"></i> ${deal.location.name} </div>
                                            <ul class="ctg-info centering h-center v-center">
                                                <li class="mt-2">
                                                    <div class="service-price">
                                                        <s class="h6 text-danger">${deal.original_amount}</s>
                                                        <span class="unit">{{ $settings->currency->currency_symbol }}</span>${deal.deal_amount}
                                                    </div>
                                                </li>
                                                <li class="mt-2">
                                                    <div style="margin-left: 2.5em" class="dropdown add-items center-h">

                                                        <a href="${deal.deal_detail_url}" class="btn-custom btn-blue dropdown-toggle">
                                                            @lang('app.view') @lang('app.detail')
                                                        </a>

                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>`
                                });

                                $('.owl-stage .owl-item').each(function(){
                                    $('.listing-item').remove();
                                });

                                $('.owl-carousel').owlCarousel('destroy');
                                $(".owl-carousel").css('class','owl-loaded');

                                $('#owl-carousel').html('<div class="owl-carousel owl-theme owl-loaded"><div class="owl-stage-outer"><div class="owl-stage">'+deals+'</div></div></div>');

                                $(".owl-carousel").owlCarousel({
                                    // loop:true,
                                    margin:20,
                                    dots:true,
                                    responsiveClass: true,
                                    responsive: {
                                        0: {
                                            items: 1
                                        },
                                        480: {
                                            items: 2
                                        },
                                        768: {
                                            items: 3
                                        },
                                        1024: {
                                            items: 4
                                        }
                                    }
                                });
                            }
                            else {
                                $('#deals_section').hide();
                            }


                            var categories = `
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-30 categories-list">
                                    <div class="ctg-item" style="background-image:url('{{ asset('assets/img/pl-slide1.jpg') }}')">
                                        <a href="javascript:;">
                                            <div class="icon-box">
                                                <i class="flaticon-fork"></i>
                                            </div>
                                            <div class="content-box">
                                                <h5 class="mb-0">
                                                    @lang('front.all')
                                                </h5>
                                            </div>
                                        </a>
                                    </div>
                                </div>`;

                            response.categories.forEach(category => {
                                if (category.services.length > 0) {
                                    var defaultAsset = '{{ asset('assets/img/pl-slide1.jpg') }}';
                                    var asset = '{{ asset('user-uploads/category/').'/' }}'+category.image;
                                    var url = category.image ?  asset : defaultAsset;
                                    categories += `
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-30 categories-list" data-category-id="${category.id}">
                                    <div class="ctg-item" style="background-image:url('${url}')">
                                        <a href="javascript:;">
                                            <div class="icon-box">
                                                <i class="flaticon-fork"></i>
                                            </div>
                                            <div class="content-box">
                                                <h5 class="mb-0">
                                                    ${category.name}
                                                </h5>
                                            </div>
                                        </a>
                                    </div>
                                </div>`
                                }
                            });

                            $('#categories').html(categories);


                            var services = '';

                            if (response.services.length > 0) {
                                response.services.forEach(service => {
                                    var defaultAsset = '{{ asset('assets/img/pl-slide1.jpg') }}';
                                    var asset = "{{ asset('user-uploads/service/').'/' }}"+service.id+'/'+service.default_image;
                                    var url = service.image ?  asset : defaultAsset;

                                    services += `
                                    <div class="col-lg-4 col-md-6 col-12 mb-30 services-list service-category-${service.category_id}">
                                        <div class="listing-item">
                                            <div class="img-holder">
                                                <img src="${url}" alt="list">
                                                <div class="category-name">
                                                    <a href="#" class="c-black">
                                                        <i class="flaticon-fork mr-1"></i>${service.category.name}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="list-content">
                                                <h5 class="mb-2">
                                                    <a href="${service.service_detail_url}">${service.name}</a>
                                                </h5>
                                                <ul class="ctg-info centering h-center v-center">
                                                    <li class="mt-1">
                                                        <div class="service-price">
                                                            <span class="unit">{{ $settings->currency->currency_symbol }}</span>${service.discounted_price}
                                                        </div>
                                                    </li>
                                                    <li class="mt-1">
                                                        <div class="dropdown add-items">
                                                            <a href="javascript:;" class="btn-custom btn-blue dropdown-toggle add-to-cart"
                                                        data-service-price="${service.discounted_price}"
                                                        data-service-id="${service.id}"
                                                        data-service-name="${service.name}"
                                                        aria-expanded="false">
                                                                @lang('app.add')
                                                                <span class="fa fa-plus"></span>
                                                            </a>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>`
                                });

                            services += `
                            <div class="col-12 text-right">
                                <a href="javascript:;" onclick="goToPage('GET', '{{ route('front.bookingPage') }}')" class="btn btn-custom">
                                    @lang('front.selectBookingTime')
                                    <i class="fa fa-angle-right ml-1"></i>
                                </a>
                            </div>`;
                            }
                            else {
                                services += `
                                <div class="col-12 text-center mb-5">
                                    <h3 class="no-services">
                                        <i class="fa fa-exclamation-triangle"></i> @lang('front.noSearchRecordFound')
                                    </h3>
                                </div>`
                            }

                            $('#services').html(services);

                            $('html, body').animate({
                                scrollTop: $(".categories").offset().top
                            }, 1000);
                        }
                    })
                }
            });

            let searchParams = new URLSearchParams(window.location.search);
            if (searchParams.has('search_term')) {
                $('#search_term').val(searchParams.get('search_term'));
            }

            setActiveClassToLanguage();
        });

        function logoutUser(e) {
            e.preventDefault();
            $('#logoutForm').submit();
        }

        function setActiveClassToLanguage() {
            // language switcher
            if ('{{ \Cookie::has('appointo_language_code') }}') {
                $('.language-drop .dropdown-item').filter(function () {
                    return $(this).data('lang-code') === '{{ \Cookie::get('appointo_language_code') }}'
                }).addClass('active');
            }
            else {
                $('.language-drop .dropdown-item').filter(function () {
                    return $(this).data('lang-code') === '{{ $settings->locale }}'
                }).addClass('active');
            }
        }

        $('#searchForm').on('submit', function (e) {
            var searchTerm = $('#search_term').val();

            if (searchTerm.length == 0) {
                return false;
            }

            $("<input />").attr("type", "hidden")
                .attr("name", "location")
                .attr("value", localStorage.getItem('location'))
                .appendTo("#searchForm");
        });

        $('.language-drop .dropdown-item').click(function () {
            let code = $(this).data('lang-code');

            let url = '{{ route('front.changeLanguage', ':code') }}';
            url = url.replace(':code', code);

            if (!$(this).hasClass('active')) {
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
                            setActiveClassToLanguage();
                        }
                    }
                })
            }
        });

    </script>
@endpush
