@extends('layouts.master')

@push('head-css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <style>
        .dropify-wrapper, .dropify-preview, .dropify-render img {
            background-color: var(--sidebar-bg) !important;
        }

        #carousel-image-gallery .card .img-holder {
            height: 150px;
            overflow: hidden;
        }

        #carousel-image-gallery .card .img-holder img {
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        .note-group-select-from-files {
            display: none;
        }
        .select2-container .select2-selection--single {
            height: 39px;
        }
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d2d1d1;
            border-radius: 4px;
        }


        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 37px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px;
            position: absolute;
            top: 5px;
            right: 1px;
            width: 20px;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                 aria-orientation="vertical">
                <a class="nav-link active" href="#general" data-toggle="tab" id="general-tab">@lang('menu.general')</a>
                <a class="nav-link" href="#times" data-toggle="tab">@lang('menu.bookingSettings')</a>
                <a class="nav-link" href="#tax" data-toggle="tab">@lang('app.tax') @lang('menu.settings')</a>
                <a class="nav-link" href="#currency" data-toggle="tab">@lang('app.currency') @lang('menu.settings')</a>
                <a class="nav-link" href="#language" data-toggle="tab">@lang('app.language') @lang('menu.settings')</a>
                <a class="nav-link" href="#email" data-toggle="tab">@lang('app.email') @lang('menu.settings')</a>
                <a class="nav-link" href="#admin-theme" data-toggle="tab">@lang('menu.adminThemeSettings')</a>
                <a class="nav-link" href="#front-theme" data-toggle="tab">@lang('menu.frontThemeSettings')</a>
                <a class="nav-link" href="#front-pages" data-toggle="tab">@lang('menu.pages')</a>
                <a class="nav-link" href="#role-permission" data-toggle="tab">@lang('menu.rolesPermissions')</a>
                <a class="nav-link" href="#payment"
                   data-toggle="tab">@lang('app.paymentCredential') @lang('menu.settings')</a>
                <a class="nav-link" href="#sms-settings"
                   data-toggle="tab">@lang('app.smsCredentials') @lang('menu.settings')</a>
                <a class="nav-link" href="#update" data-toggle="tab">
                    @lang('menu.updateApp')
                    @if($newUpdate == 1)
                        <span class="badge badge-success">{{ $lastVersion }}</span>
                    @endif
                </a>
                <a class="nav-link" href="#privacy-policy" data-toggle="tab">@lang('menu.updatePrivacy')</a>
                <a class="nav-link" href="#who-we-are" data-toggle="tab">@lang('menu.whoWeAre')</a>
                <a class="nav-link" href="#terms-and-conditions" data-toggle="tab">@lang('menu.T&C')</a>
                <a class="nav-link" href="#service-banner-1" data-toggle="tab">Service Banner 1</a>
                <a class="nav-link" href="#service-banner-2" data-toggle="tab">Service Banner 2</a>
                <a class="nav-link" href="#loyalty-program-settings" data-toggle="tab">Loyalty Program Settings</a>
                <a class="nav-link" href="#loyalty-point-settings" data-toggle="tab">Loyalty Coin Settings</a>
                <a class="nav-link" href="#notification-settings" data-toggle="tab">Notification Settings</a>           
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="active tab-pane" id="general">

                                    <form class="form-horizontal ajax-form" id="general-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.company') @lang('app.name')</label>

                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_name" name="company_name"
                                                           value="{{ $settings->company_name }}">
                                                </div>

                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.company') @lang('app.email')</label>

                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_email" name="company_email"
                                                           value="{{ $settings->company_email }}">
                                                </div>

                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.company') @lang('app.phone')</label>

                                                    <input type="text" class="form-control  form-control-lg"
                                                           id="company_phone" name="company_phone"
                                                           value="{{ $settings->company_phone }}">
                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">@lang('app.logo')</label>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <input type="file" id="input-file-now" name="logo"
                                                                   accept=".png,.jpg,.jpeg" class="dropify"
                                                                   data-default-file="{{ $settings->logo_url }}"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">@lang('app.address')</label>
                                                    <textarea class="form-control form-control-lg" name="address" id=""
                                                              cols="30" rows="5">{!! $settings->address !!}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="date_format" class="control-label">
                                                                @lang('app.date_format')
                                                            </label>

                                                            <select name="date_format" id="date_format"
                                                                    class="form-control form-control-lg select2">
                                                                @foreach($dateFormats as $key => $dateFormat)
                                                                    <option value="{{ $key }}" @if($settings->date_format == $key) selected @endif>{{
                                                                        $key.' ('.$dateObject->format($key).')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="time_format" class="control-label">
                                                                @lang('app.time_format')
                                                            </label>

                                                            <select name="time_format" id="time_format"
                                                                    class="form-control form-control-lg select2">
                                                                @foreach($timeFormats as $key => $timeFormat)
                                                                    <option value="{{ $key }}" @if($settings->time_format == $key) selected @endif>{{
                                                                        $key.' ('.$dateObject->format($key).')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.company') @lang('app.website')</label>

                                                    <input type="text" class="form-control form-control-lg" id="website"
                                                           name="website" value="{{ $settings->website }}">
                                                </div>

                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.timezone')</label>

                                                    <select name="timezone" id="timezone"
                                                            class="form-control form-control-lg select2">
                                                        @foreach($timezones as $tz)
                                                            <option @if($settings->timezone == $tz) selected @endif>{{
                                                                $tz }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.currency')</label>

                                                    <select name="currency_id" id="currency_id"
                                                            class="form-control  form-control-lg">
                                                        @foreach($currencies as $currency)
                                                            <option
                                                                @if($currency->id == $settings->currency_id) selected
                                                                @endif
                                                                value="{{ $currency->id }}">{{ $currency->currency_symbol.' ('.$currency->currency_code.')' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>


                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.language')</label>

                                                    <select name="locale" id="locale"
                                                            class="form-control form-control-lg">
                                                        @forelse($enabledLanguages as $language)
                                                            <option value="{{ $language->language_code }}"
                                                                    @if($settings->locale == $language->language_code) selected @endif >
                                                                {{ $language->language_name }}
                                                            </option>
                                                        @empty
                                                            <option @if($settings->locale == "en") selected
                                                                    @endif value="en">English
                                                            </option>
                                                        @endforelse
                                                    </select>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-general" type="button" class="btn btn-success"><i
                                                    class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="times">
                                    <form id="booking-times-form" method="post">
                                        @csrf
                                        <div class="row">
                                            <h4 class="col-md-12">@lang('app.booking') @lang('app.option') <hr></h4> <br><br><br>

                                            <div class="col-md-6">
                                                <h5 class="text-primary">@lang('app.multiTaskingEmployee')</h5>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.assignMultipleEmployeeAtSameTimeSlot')</label>
                                                    <br>
                                                    <label class="switch" style="margin-top: .2em">
                                                        <input type="checkbox" name="multi_task_user" id="multi_task_user" value="enabled" @if ( $settings->multi_task_user=='enabled') checked @endif onchange="multiTaskingEmpChanged()">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <h5 class="text-primary">@lang('app.limit') @lang('app.booking')</h5>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.maxBookingPerCustomer')</label>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                        <input onkeypress="return isNumberKey(event)" class="form-control" type="number" name="no_of_booking_per_customer" min="0" value="{{$settings->booking_per_day}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <h5 class="text-primary">@lang('app.allowEmployeeSelection')</h5>
                                                <div class="form-group">
                                                    <label class="control-label">@lang('messages.allowEmployeeSelectionMSG')</label>
                                                    <br>
                                                    <label class="switch" style="margin-top: .2em">
                                                        <input value="enabled" type="checkbox" name="employee_selection" @if ( $settings->employee_selection=='enabled') checked @endif>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <h5 class="text-primary">@lang('app.disableSlotDurationAsPerServiceDuration')</h5>
                                                <div class="form-group">
                                                    <label class="control-label">Booking time will be calculated based on selected service</label>
                                                    <br>
                                                    <label class="switch" style="margin-top: .2em">
                                                        <input @if ( $settings->disable_slot=='enabled') checked @endif value="enabled" type="checkbox" name="disable_slot" id="disable_slot" onchange="disableSlotChanged()">
                                                        <span class="slider round"></span>
                                                    </label>

                                                    <div class="row" id="div_disable_slot" @if ( $settings->disable_slot=='disabled' || $settings->disable_slot=='') style="display: none" @endif>
                                                        <br>
                                                        <div class="col-md-8">
                                                        <label class="radio-inline pl-lg-2"><input type="radio" @if($settings->booking_time_type == 'sum') checked @endif
                                                            onchange="getDriverValue(this);"
                                                            value="sum" name="booking_time_type" class="booking_time_type"> @lang('app.sum')</label>
                                                        <label class="radio-inline pl-lg-2"><input type="radio"
                                                            @if($settings->booking_time_type == 'avg') checked @endif
                                                            onchange="getDriverValue(this);"
                                                            value="avg" name="booking_time_type" class="booking_time_type"> @lang('app.average')</label>
                                                        <label class="radio-inline pl-lg-2"><input type="radio"
                                                            @if($settings->booking_time_type == 'max') checked @endif
                                                            onchange="getDriverValue(this);"
                                                            value="max" name="booking_time_type" class="booking_time_type"> @lang('app.maximum')</label>
                                                        <label class="radio-inline pl-lg-2"><input type="radio"
                                                            @if($settings->booking_time_type == 'min') checked @endif
                                                            onchange="getDriverValue(this);"
                                                            value="min" name="booking_time_type" class="booking_time_type"> @lang('app.minimum')</label>
                                                        </div>
                                                        <div class="col-12 alert alert-info" role="alert" id="info-msg">

                                                            @if($settings->booking_time_type == 'sum') @lang('messages.sumOfServiceTime').
                                                            @endif
                                                            @if($settings->booking_time_type == 'max') @lang('messages.MaxServiceTime').
                                                            @endif
                                                            @if($settings->booking_time_type == 'min') @lang('messages.MinServiceTime').
                                                            @endif
                                                            @if($settings->booking_time_type == 'avg')@lang('messages.AvgOfServiceTime').form-control-sm
                                                            @endif


                                                        </div>
                                                        <div class="col-12 alert alert-warning" role="alert">
                                                            @lang('messages.disablePaymentsFromFront').
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success" id="save-booking-times-field"><i
                                                    class="fa fa-check"></i>@lang('app.save')</button>
                                            </div>

                                        </div>
                                        <hr><br>

                                        <div class="row">
                                            <div class="col-md">
                                                <h4>@lang('app.booking') @lang('app.schedule')</h4><br>
                                                <div class="table-responsive">
                                                    <table class="table table-condensed">
                                                        <tr>
                                                            <th style="width: 10px">#</th>
                                                            <th>@lang('app.day')</th>
                                                            <th>@lang('modules.settings.openTime')</th>
                                                            <th>@lang('modules.settings.closeTime')</th>
                                                            <th>@lang('modules.settings.allowBooking')?</th>
                                                            <th>@lang('app.action')</th>
                                                        </tr>
                                                        @foreach($bookingTimes as $key=>$bookingTime)
                                                            <tr>
                                                                <td>{{ $key+1 }}</td>
                                                                <td>@lang('app.'.$bookingTime->day)</td>
                                                                <td>{{ $bookingTime->start_time }}</td>
                                                                <td>{{ $bookingTime->end_time }}</td>
                                                                <td>
                                                                    <label class="switch">
                                                                        <input type="checkbox" class="time-status"
                                                                            data-row-id="{{ $bookingTime->id }}"
                                                                            @if($bookingTime->status == 'enabled') checked @endif
                                                                        >
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" data-row-id="{{ $bookingTime->id }}"
                                                                    class="btn btn-primary btn-rounded btn-sm edit-row"><i
                                                                            class="icon-pencil"></i> @lang('app.edit')</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="tax">
                                    <form class="form-horizontal ajax-form" id="tax-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="tax_name"
                                                           class="control-label">@lang('app.tax') @lang('app.name')</label>

                                                    <input type="text" class="form-control form-control-lg"
                                                           id="tax_name" name="tax_name"
                                                           value="{{ $tax->tax_name }}">
                                                </div>

                                                <div class="form-group">
                                                    <label for="percent"
                                                           class="control-label">@lang('app.percent')</label>

                                                    <input type="number" min="0" step="0.01" value="{{ $tax->percent }}"
                                                           class="form-control form-control-lg" id="percent"
                                                           name="percent">
                                                </div>
                                                <div class="form-group">
                                                    <label for="percent"
                                                           class="control-label">@lang('app.status')</label>

                                                    <select name="status" id="status"
                                                            class="form-control form-control-lg">
                                                        <option
                                                            @if($tax->status == 'active') selected @endif
                                                        value="active">@lang('app.active')</option>
                                                        <option
                                                            @if($tax->status == 'deactive') selected @endif
                                                        value="deactive">@lang('app.deactive')</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button id="save-tax" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>

                                            </div>

                                        </div>

                                    </form>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="currency">
                                    <h4>@lang('app.add') @lang('app.currency')</h4>
                                    <form class="form-horizontal ajax-form" id="currency-form" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label
                                                        class="control-label">@lang('app.currency') @lang('app.name')</label>

                                                    <input type="text" class="form-control form-control-lg"
                                                           id="currency_name" name="currency_name">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.currencySymbol')</label>

                                                    <input type="text" class="form-control form-control-lg"
                                                           id="currency_symbol" name="currency_symbol">
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('app.currencyCode')</label>

                                                    <input type="text" class="form-control form-control-lg"
                                                           id="currency_code" name="currency_code">
                                                </div>

                                                <div class="form-group">
                                                    <button id="save-currency" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>

                                        </div>


                                    </form>

                                    <h4 class="mt-4">@lang('app.currency')</h4>
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.currency') @lang('app.name')</th>
                                                    <th>@lang('app.currencySymbol')</th>
                                                    <th>@lang('app.currencyCode')</th>
                                                    <th><i class="fa fa-gear"></i></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($currencies as $key=>$currency)
                                                    <tr id="currency-{{ $currency->id }}">
                                                        <td>{{ ($key+1) }}</td>
                                                        <td>{{ ucwords($currency->currency_name) }}</td>
                                                        <td>{{ $currency->currency_symbol }}</td>
                                                        <td>{{ $currency->currency_code }}</td>
                                                        <td>
                                                            <button data-row-id="{{ $currency->id }}"
                                                                    class="btn btn-primary btn-circle edit-currency"
                                                                    type="button"><i
                                                                    class="fa fa-pencil" data-toggle="tooltip" data-original-title="@lang('app.edit')"></i>
                                                            </button>
                                                            @if ($settings->currency->id !== $currency->id)
                                                                <button data-row-id="{{ $currency->id }}"
                                                                        class="btn btn-danger btn-circle delete-currency"
                                                                        type="button"><i
                                                                        class="fa fa-times" data-toggle="tooltip" data-original-title="@lang('app.delete')"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="language">
                                    @include('admin.language.index')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="email">
                                    <h4>@lang('app.email') @lang('menu.settings')</h4>
                                    <form class="form-horizontal ajax-form" id="email-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div id="alert">
                                            @if($smtpSetting->mail_driver =='smtp')
                                                @if($smtpSetting->verified)
                                                    <div
                                                        class="alert alert-success">{{__('messages.smtpSuccess')}}</div>
                                                @else
                                                    <div class="alert alert-danger">{{__('messages.smtpError')}}</div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="form-group">
                                                    <label>@lang("modules.emailSettings.mailDriver")</label>
                                                    <div class="form-group">
                                                        <label class="radio-inline"><input type="radio"
                                                                                           class="checkbox"
                                                                                           onchange="getDriverValue(this);"
                                                                                           value="mail"
                                                                                           @if($smtpSetting->mail_driver == 'mail') checked
                                                                                           @endif name="mail_driver"> Mail</label>
                                                        <label class="radio-inline pl-lg-2"><input type="radio"
                                                                                                   onchange="getDriverValue(this);"
                                                                                                   value="smtp"
                                                                                                   @if($smtpSetting->mail_driver == 'smtp') checked
                                                                                                   @endif name="mail_driver"> SMTP</label>


                                                    </div>
                                                </div>
                                                <div id="smtp_div">
                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailHost")</label>
                                                        <input type="text" name="mail_host" id="mail_host"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_host }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailPort")</label>
                                                        <input type="text" name="mail_port" id="mail_port"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_port }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.emailSettings.mailUsername")</label>
                                                        <input type="text" name="mail_username" id="mail_username"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_username }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label
                                                            class="control-label">@lang("modules.emailSettings.mailPassword")</label>
                                                        <input type="password" name="mail_password"
                                                               id="mail_password"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smtpSetting->mail_password }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            class="control-label">@lang("modules.emailSettings.mailEncryption")</label>
                                                        <select class="form-control form-control-lg"
                                                                name="mail_encryption"
                                                                id="mail_encryption">
                                                            <option
                                                                @if($smtpSetting->mail_encryption == 'none') selected @endif>
                                                                none
                                                            </option>
                                                            <option
                                                                @if($smtpSetting->mail_encryption == 'tls') selected @endif>
                                                                tls
                                                            </option>
                                                            <option
                                                                @if($smtpSetting->mail_encryption == 'ssl') selected @endif>
                                                                ssl
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="control-label">@lang("modules.emailSettings.mailFrom")</label>
                                                    <input type="text" name="mail_from_name" id="mail_from_name"
                                                           class="form-control form-control-lg"
                                                           value="{{ $smtpSetting->mail_from_name }}">
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="control-label">@lang("modules.emailSettings.mailFromEmail")</label>
                                                    <input type="text" name="mail_from_email" id="mail_from_email"
                                                           class="form-control form-control-lg"
                                                           value="{{ $smtpSetting->mail_from_email }}">
                                                </div>


                                                <div class="form-group">
                                                    <button id="save-email" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>


                                            </div>

                                            <!--/span-->
                                        </div>

                                    </form>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="admin-theme">
                                    <h4>@lang('menu.adminThemeSettings')</h4>
                                    <section class="mt-3 mb-3">
                                        <form class="form-horizontal ajax-form" id="theme-form" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.colorPallette')</h6>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.primaryColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="primary_color"
                                                               value="{{ $themeSettings->primary_color }}">
                                                        <div
                                                            style="background-color: {{ $themeSettings->primary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.secondaryColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="secondary_color"
                                                               value="{{ $themeSettings->secondary_color }}">
                                                        <div
                                                            style="background-color: {{ $themeSettings->secondary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarBgColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="sidebar_bg_color"
                                                               value="{{ $themeSettings->sidebar_bg_color }}">
                                                        <div
                                                            style="background-color: {{ $themeSettings->sidebar_bg_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.sidebarTextColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="sidebar_text_color"
                                                               value="{{ $themeSettings->sidebar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $themeSettings->sidebar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.topbarTextColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="topbar_text_color"
                                                               value="{{ $themeSettings->topbar_text_color }}">
                                                        <div
                                                            style="background-color: {{ $themeSettings->topbar_text_color }}"
                                                            class="border border-light">&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>

                                            <div class="row mb-3">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.customCss')</h6>

                                                <div class="col-md-12">
                                                    <div id="admin-custom-css">@if(!$themeSettings->custom_css)@lang('modules.theme.defaultCssMessage')@else{!! $themeSettings->custom_css !!}@endif</div>
                                                </div>

                                                <input id="admin-custom-input" type="hidden" name="admin_custom_css">
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-theme" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="front-theme">
                                    <h4>@lang('menu.frontThemeSettings')</h4>
                                    <section class="mt-3 mb-3">
                                        <form class="form-horizontal ajax-form" id="front-theme-form" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.colorPallette')</h6>
                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.primaryColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="primary_color"
                                                               value="{{ $frontThemeSettings->primary_color }}">
                                                        <div
                                                            style="background-color: {{ $frontThemeSettings->primary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>@lang('modules.theme.secondaryColor')</label>
                                                        <input type="text" class="form-control color-picker"
                                                               name="secondary_color"
                                                               value="{{ $frontThemeSettings->secondary_color }}">
                                                        <div
                                                            style="background-color: {{ $frontThemeSettings->secondary_color }}"
                                                            class=" border border-light">&nbsp;
                                                        </div>
                                                    </div>

                                                </div>
                                                <!--/span-->
                                            </div>
                                            <div class="row mb-3">
                                                <h6 class="col-md-12">@lang('modules.theme.subheadings.customCss')</h6>

                                                <div class="col-md-12">
                                                    <div id="front-custom-css">@if(!$frontThemeSettings->custom_css)@lang('modules.theme.defaultCssMessage')@else{!! $frontThemeSettings->custom_css !!}@endif</div>
                                                </div>

                                                <input id="front-custom-input" type="hidden" name="front_custom_css">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6 class="col-md-12">@lang('app.logo')</h6>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <input type="file" id="front-input-file-now"
                                                                           name="front_logo"
                                                                           accept=".png,.jpg,.jpeg" class="dropify"
                                                                           data-default-file="{{ $frontThemeSettings->logo_url }}"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <button id="save-front-theme" type="button" class="btn btn-success">
                                                        <i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                    <section class="mt-3 mb-3">
                                        <h6>@lang('modules.theme.subheadings.carouselImages')</h6>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <form id="theme-carousel-form">
                                                    @csrf
                                                    <div class="form-group">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <input type="file" id="carousel-images" name="images[]"
                                                                       accept=".png,.jpg,.jpeg" class="dropify"
                                                                       data-allowed-formats="landscape"
                                                                       multiple
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <h6 class="text-danger">@lang('modules.theme.recommendedResolutionNote')</h6>
                                        <div id="carousel-image-gallery" class="row">
                                            @include('partials.carousel_images')
                                        </div>
                                    </section>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="front-pages">
                                    @include('admin.page.index')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="role-permission">
                                    @include('admin.role-permission.index')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="payment">
                                    <h4>@lang('app.paymentCredential') @lang('menu.settings')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="payment-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h5 class="text-primary">@lang('app.offlinePaymentMethod')</h5>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang("modules.paymentCredential.allowOfflinePayment")</label>
                                                            <br>
                                                            <label class="switch">
                                                                <input type="checkbox" name=""
                                                                       @if($credentialSetting->offline_payment == 1) checked
                                                                       @endif  class="offline-payment">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md">
                                                        <h5 class="text-secondary">@lang('app.showPaymentOptions')</h5>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang("modules.paymentCredential.allowCustomerPayment")</label>
                                                            <br>
                                                            <label class="switch">
                                                                <input type="checkbox" value="show" name="show_payment_options"
                                                                    @if($credentialSetting->show_payment_options == 'show') checked
                                                                    @endif class="show_payment_options">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <br>

                                                <h5 class="text-info">@lang('app.paypalCredential') </h5>
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        @lang("modules.paymentCredential.paypalCredentialStatus")
                                                    </label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="paypal_status" id="paypal_status"
                                                                @if($credentialSetting->paypal_status == 'active')
                                                                    checked
                                                                @endif  value='active' onchange="toggle('#paypal-credentials');">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                <div id="paypal-credentials">
                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.paypalClientID")</label>
                                                        <input type="text" name="paypal_client_id" id="paypal_client_id"
                                                               class="form-control form-control-lg"
                                                               value="{{ $credentialSetting->paypal_client_id }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.paypalSecret")</label>
                                                        <input type="password" name="paypal_secret" id="paypal_secret"
                                                               class="form-control form-control-lg"
                                                               value="{{ $credentialSetting->paypal_secret }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.paypalMode")</label>
                                                        <select class="form-control" name="paypal_mode" id="paypal_mode">
                                                            <option @if ($credentialSetting->paypal_mode === 'sandbox')
                                                                selected
                                                            @endif value="sandbox">Sandbox</option>
                                                            <option @if ($credentialSetting->paypal_mode === 'live')
                                                                selected
                                                            @endif value="live">Live</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr>
                                                <br>

                                                <h5 class="text-warning">@lang('app.stripeCredential') </h5>
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        @lang("modules.paymentCredential.stripeCredentialStatus")
                                                    </label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="stripe_status" id="stripe_status"
                                                                @if($credentialSetting->stripe_status == 'active')
                                                                    checked
                                                                @endif value="active" onchange="toggle('#stripe-credentials');">
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <input type="hidden" name="offline_payment"
                                                            @if($credentialSetting->offline_payment == 1) value="1"
                                                            @else value="0" @endif id="offlinePayment">

                                                </div>
                                                <div id="stripe-credentials">
                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.stripelClientID")</label>
                                                        <input type="text" name="stripe_client_id" id="stripe_client_id"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->stripe_client_id }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.stripeSecret")</label>
                                                        <input type="password" name="stripe_secret" id="stripe_secret"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->stripe_secret }}">
                                                    </div>
                                                </div>

                                                <hr>
                                                <br>

                                                <h5 class="text-success">@lang('app.razorpayCredential') </h5>
                                                <div class="form-group d-flex flex-column">
                                                    <label class="control-label">
                                                        @lang("modules.paymentCredential.razorpayCredentialStatus")
                                                    </label>
                                                    <div class="d-flex">
                                                        <label class="switch mr-2">
                                                            <input type="checkbox" name="razorpay_status" id="razorpay_status"
                                                                    @if($credentialSetting->razorpay_status == 'active')
                                                                        checked
                                                                    @endif value="active" onchange="toggleRazorPay('#razorpay-credentials');">
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <span class="text-danger wrong-currency-message">
                                                            @lang('modules.paymentCredential.changeCurrencyToINR') ( <a href="#general" onclick="$('#general-tab').trigger('click');">@lang('menu.general')</a> )
                                                        </span>
                                                    </div>
                                                </div>
                                                <div id="razorpay-credentials">
                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.razorpayKey")</label>
                                                        <input type="text" name="razorpay_key" id="razorpay_key"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->razorpay_key }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.paymentCredential.razorpaySecret")</label>
                                                        <input type="password" name="razorpay_secret" id="razorpay_secret"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->razorpay_secret }}">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <button id="save-payment" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>

                                            <!--/span-->
                                        </div>

                                    </form>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="sms-settings">
                                    <h4>@lang('app.smsCredentials') @lang('menu.settings')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="sms-setting-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <h5 class="text-info">@lang('app.nexmoCredential') </h5>
                                                <div class="form-group">
                                                    <label class="control-label">
                                                            @lang("modules.nexmoCredential.status")
                                                    </label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox" name="nexmo_status" id="nexmo_status"
                                                                @if($smsSetting->nexmo_status == 'active')
                                                                    checked
                                                                @endif value="active" onchange="toggle('#nexmo-credentials');">
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                <div id="nexmo-credentials">
                                                    <div class="form-group">
                                                        <label>@lang("modules.nexmoCredential.key")</label>
                                                        <input type="text" name="nexmo_key" id="nexmo_key"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smsSetting->nexmo_key }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.nexmoCredential.secret")</label>
                                                        <input type="password" name="nexmo_secret" id="nexmo_secret"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smsSetting->nexmo_secret }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>@lang("modules.nexmoCredential.from")</label>
                                                        <input type="text" name="nexmo_from" id="nexmo_from"
                                                               class="form-control form-control-lg"
                                                               value="{{ $smsSetting->nexmo_from }}">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <button id="save-sms-settings" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="update">
                                    <h4>@lang('menu.updateApp')</h4>
                                    @include('vendor.froiden-envato.update.update_blade')


                                    <hr>
                                    @include('vendor.froiden-envato.update.changelog')
                                    <!--/row-->
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="privacy-policy">
                                    @include('admin.settings.tabs.update-privacy-policy-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="who-we-are">
                                    @include('admin.settings.tabs.update-who-we-are-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="terms-and-conditions">
                                    @include('admin.settings.tabs.terms-and-conditions-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="service-banner-1">
                                    @include('admin.settings.tabs.service-banner-1-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="service-banner-2">
                                    @include('admin.settings.tabs.service-banner-2-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="loyalty-program-settings">
                                    @include('admin.settings.tabs.loyalty-program-settings-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="loyalty-point-settings">
                                    @include('admin.settings.tabs.loyalty-point-settings-tab')
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="notification-settings">
                                    @include('admin.settings.tabs.notification-settings-tab')
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>
@endsection

@push('footer-js')
    <script src="{{ asset('/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script src="{{ asset('assets/ace/ace.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    
    <script>
        // who we are desciption
        var ckeditorInstance;

        ClassicEditor.create(document.querySelector('#who_are_we_decsription'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            },
            removePlugins: [
                'Image',
                'ImageToolbar',
                'ImageCaption',
                'ImageStyle',
                'ImageResize',
                'ImageUpload',
                'CKFinder',
                'MediaEmbed',
                'Table',
                'BlockQuote',
                'EasyImage'
            ]
        })
        .then(editor => {
            ckeditorInstance = editor;
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });

        // terms and conditions description

        var ckeditorInstance_terms;

        ClassicEditor.create(document.querySelector('#terms_condition_description'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            },
            removePlugins: [
                'Image',
                'ImageToolbar',
                'ImageCaption',
                'ImageStyle',
                'ImageResize',
                'ImageUpload',
                'CKFinder',
                'MediaEmbed',
                'Table',
                'BlockQuote',
                'EasyImage'
            ]
        })
        .then(editor => {
            ckeditorInstance_terms = editor;
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });

        $(function () {
            $('.wrong-currency-message').hide();
            $('#paypal_status').is(':checked') ? $('#paypal-credentials').show() : $('#paypal-credentials').hide();
            $('#stripe_status').is(':checked') ? $('#stripe-credentials').show() : $('#stripe-credentials').hide();
            $('#razorpay_status').is(':checked') ? $('#razorpay-credentials').show() : $('#razorpay-credentials').hide();
            $('#nexmo_status').is(':checked') ? $('#nexmo-credentials').show() : $('#nexmo-credentials').hide();

            $('#v-pills-tab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                $("html, body").scrollTop(0);
            });

            // store the currently selected tab in the hash value
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                var id = $(e.target).attr("href").substr(1);
                window.location.hash = id;
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#v-pills-tab a[href="' + hash + '"]').tab('show');
        });

        var frontCssEditor = ace.edit('front-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });
        var adminCssEditor = ace.edit('admin-custom-css', {
            mode: 'ace/mode/css',
            theme: 'ace/theme/twilight'
        });

        function checkCurrencyCode(currency_code) {
            if ( currency_code === 'INR') {
                return true;
            }
            else {
                return false;
            }
        }

        $(function() {
            $('#privacy-policy-textarea').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ["view", ["fullscreen"]]
                ]
            });
        });

        $(function() {
            $('.description').summernote({
                dialogsInBody: true,
                height: 300,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ["view", ["fullscreen"]]
                ]
            });
        });

        $('.edit-row').click(function () {
            var id = $(this).data('row-id');
            var url = '{{ route('admin.booking-times.edit', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('@lang('app.edit') @lang('menu.bookingTimes')');
            $.ajaxModal('#application-modal', url);
        });

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('.color-picker').colorpicker({
            format: 'hex'
        });

        $('.time-status').change(function () {
            var id = $(this).data('row-id');
            var url = "{{route('admin.booking-times.update', ':id')}}";
            url = url.replace(':id', id);

            if ($(this).is(':checked')) {
                var status = 'enabled';
            } else {
                var status = 'disabled';
            }

            $.easyAjax({
                url: url,
                type: "POST",
                data: {'_method': 'PUT', '_token': "{{ csrf_token() }}", 'status': status}
            })
        });

        $('.offline-payment').change(function () {
            if ($(this).is(':checked')) {
                $('#offlinePayment').val(1);
            } else {
                $('#offlinePayment').val(0);
            }
        });

        function toggle(elementBox) {
            var elBox = $(elementBox);
            elBox.slideToggle();
        }

        function toggleRazorPay(elementBox) {
            var elBox = $(elementBox);
            if (checkCurrencyCode('{{ $settings->currency->currency_code }}')) {
                elBox.slideToggle();
                $('.wrong-currency-message').fadeOut();
            }
            else {
                $('.wrong-currency-message').fadeIn();
                $('#razorpay_status').prop('checked', false);
            }
        }

        $('#save-general').click(function () {
            $.easyAjax({
                url: '{{route('admin.settings.update', $settings->id)}}',
                container: '#general-form',
                type: "POST",
                file: true,
                data: $('#general-form').serialize()
            })
        });

        $('#save-tax').click(function () {
            $.easyAjax({
                url: '{{route('admin.tax-settings.update', $tax->id)}}',
                container: '#tax-form',
                type: "POST",
                data: $('#tax-form').serialize()
            })
        });

        $('#update-privacy-policy').click(function () {
            $.easyAjax({
                url: '{{route('admin.update-privacy-policy', $privacypolicy->id)}}',
                container: '#privacy-policy-update-form',
                type: "POST",
                data: $('#privacy-policy-update-form').serialize()
            })
        });

        $('#update-terms-condition').click(function () {
            // Manually update the textarea with CKEditor content
            $('#terms_condition_description').val(ckeditorInstance_terms.getData());

            $.easyAjax({
                url: '{{route('admin.update-terms-condition', $terms->id)}}',
                container: '#terms-condition-update-form',
                type: "POST",
                data: $('#terms-condition-update-form').serialize()
            })
        });

        $("#update-who-we-are-content").on('click', function(){
            // Manually update the textarea with CKEditor content
            $('#who_are_we_decsription').val(ckeditorInstance.getData());

            var formData = new FormData($("#who-we-are-update-form")[0]);

            $.ajax({
                url: '{{ route("admin.who-we-are-content.update") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    alert("Updated Successfully");
                    location.reload();
                },
            });
        });

        // *** service banner image 1 start ***

        $("#service_banner_btn_1").on('click', function(){
            var formData = new FormData($("#service_banner_form_1")[0]);

            formData.append("banner_type", 1);

            $.ajax({
                url: '{{ route("admin.service-banner.store") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    console.log(response);

                    if(response.status == 'error')
                    {
                        $.each(response.errors, function (key, value) { 
                            $.showToastr(value, 'error');
                        });
                    }
                    else
                    {
                        $.showToastr(response.message, 'success');
                    }

                    location.reload();
                },
                error: function(response){
                    console.log(response);
                },
            });
        });

        $('body').on('click', '.delete_service_banner_1_btn', function(){
            var id = $(this).data('row_id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
            .then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('admin.service-banner.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token},
                        success: function (response) {
                            if (response.status == "success") {
                                location.reload();
                            }
                        }
                    });
                }
            });
        });

        // *** service banner image 1 end ***

        // *** service banner image 2 start ***

        $("#service_banner_btn_2").on('click', function(){
            var formData = new FormData($("#service_banner_form_2")[0]);

            formData.append("banner_type", 2);

            $.ajax({
                url: '{{ route("admin.service-banner.store") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    console.log(response);

                    if(response.status == 'error')
                    {
                        $.each(response.errors, function (key, value) { 
                            $.showToastr(value, 'error');
                        });
                    }
                    else
                    {
                        $.showToastr(response.message, 'success');
                    }

                    location.reload();
                },
                error: function(response){
                    console.log(response);
                },
            });
        });

        $('body').on('click', '.delete_service_banner_2_btn', function(){
            var id = $(this).data('row_id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
            .then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('admin.service-banner.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token},
                        success: function (response) {
                            if (response.status == "success") {
                                location.reload();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '#service_banner_two_status', function(){
            var banner_type = $(this).data('banner_type');
            var status = $(this).prop('checked') == true ? 1 : 2;
            var url = "{{ route('admin.service-banner.update-status') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {'_token': "{{ csrf_token() }}", 'status': status, 'banner_type': banner_type},
                success: function(response){
                    console.log(response);

                    if(response.status == 'success')
                    {
                        $.showToastr(response.message, 'success');
                    }
                    else
                    {
                        $.showToastr(response.message, 'error');
                    }
                },
                error: function(response){
                    console.log(response);
                },
            });

        });

        // *** service banner image 2 end ***

        $('#save-currency').click(function () {
            $.easyAjax({
                url: '{{route('admin.currency-settings.store')}}',
                container: '#currency-form',
                type: "POST",
                data: $('#currency-form').serialize()
            })
        });

        $('#save-booking-times-field').click(function () {
            $.easyAjax({
                url: '{{route('admin.save-booking-times-field')}}',
                container: '#booking-times-form',
                type: "POST",
                data: $('#booking-times-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });


        $('#save-payment').click(function () {
            $.easyAjax({
                url: '{{route('admin.credential.update', $credentialSetting->id)}}',
                container: '#payment-form',
                type: "POST",
                data: $('#payment-form').serialize()
            })
        });

        $('#save-sms-settings').click(function () {
            $.easyAjax({
                url: '{{route('admin.sms-settings.update', $smsSetting->id)}}',
                container: '#sms-setting-form',
                type: "POST",
                data: $('#sms-setting-form').serialize()
            })
        });

        $('#save-theme').click(function () {
            $('#admin-custom-input').val(adminCssEditor.getValue());
            $.easyAjax({
                url: '{{route('admin.theme-settings.update', $themeSettings->id)}}',
                container: '#theme-form',
                type: "POST",
                data: $('#theme-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });

        $('#save-front-theme').click(function () {
            $('#front-custom-input').val(frontCssEditor.getValue());
            $.easyAjax({
                url: '{{route('admin.front-theme-settings.update', $frontThemeSettings->id)}}',
                container: '#front-theme-form',
                type: "POST",
                file: true
            })
        });


        $('#carousel-images').change(function (e) {
            $.easyAjax({
                url: '{{route('admin.front-theme-settings.store')}}',
                container: '#theme-carousel-form',
                type: "POST",
                file: true,
                success: function (response) {
                    $('#carousel-image-gallery').html(response.view);
                }
            });
        });

        $('body').on('click', '.delete-carousel-row', function () {
            var id = $(this).attr('id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.front-theme-settings.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    $('#carousel-image-gallery').html(response.view);
                                }
                            }
                        });
                    }
                });
        });

        $('body').on('click', '.delete-currency', function () {
            var id = $(this).data('row-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.currency-settings.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    $('#currency-' + id).remove();
                                }
                            }
                        });
                    }
                });
        });


        $('.edit-currency').click(function () {
            var id = $(this).data('row-id');
            var url = '{{ route('admin.currency-settings.edit', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('@lang('app.edit') @lang('menu.currency')');
            $.ajaxModal('#application-modal', url);
        });

        $('#save-email').click(function () {

            $.easyAjax({
                url: '{{route('admin.email-settings.update', $smtpSetting->id)}}',
                container: '#email-form',
                type: "POST",
                data: $('#email-form').serialize(),
                messagePosition: "inline",
                success: function (response) {
                    if (response.status == 'error') {
                        $('#alert').prepend('<div class="alert alert-danger">{{__('messages.smtpError')}}</div>')
                    } else {
                        $('#alert').show();
                    }
                }
            })
        });

        $('#send-test-email').click(function () {
            $('#testMailModal').modal('show')
        });

        $('#send-test-email-submit').click(function () {
            $.easyAjax({
                url: '{{route('admin.email-settings.sendTestEmail')}}',
                type: "GET",
                messagePosition: "inline",
                container: "#testEmail",
                data: $('#testEmail').serialize()

            })
        });


        function getDriverValue(sel) {
            if (sel.value == 'mail') {
                $('#smtp_div').hide();
                $('#alert').hide();
            } else {
                $('#smtp_div').show();
                $('#alert').show();
            }
        }

        @if ($smtpSetting->mail_driver == 'mail')
        $('#smtp_div').hide();
        $('#alert').hide();
        @endif
    </script>
    <script>
        var table = langTable = '';
        $(document).ready(function() {
            // pages table
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.pages.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( table );

            $('body').on('click', '.edit-page', function () {
                var slug = $(this).data('slug');
                var url = '{{ route('admin.pages.edit', ':slug')}}';
                url = url.replace(':slug', slug);

                $('#modelLgHeading').html('@lang('app.edit') @lang('menu.page')');
                $.ajaxModal('#application-lg-modal', url);
            });

            $('body').on('click', '#create-page', function () {
                var url = '{{ route('admin.pages.create') }}';

                $('#modelLgHeading').html('@lang('app.createNew') @lang('menu.page')');
                $.ajaxModal('#application-lg-modal', url);
            });

            $('body').on('click', '.delete-row', function(){
                var id = $(this).data('row-id');
                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            var url = "{{ route('admin.pages.destroy',':id') }}";
                            url = url.replace(':id', id);

                            var token = "{{ csrf_token() }}";

                            $.easyAjax({
                                type: 'POST',
                                url: url,
                                data: {'_token': token, '_method': 'DELETE'},
                                success: function (response) {
                                    if (response.status == "success") {
                                        $.unblockUI();
                                        // swal("Deleted!", response.message, "success");
                                        table._fnDraw();
                                    }
                                }
                            });
                        }
                    });
            });

            // language table
            langTable = $('#langTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.language-settings.index') !!}',
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                order: [[1, 'ASC']],
                columns: [
                    { data: 'DT_RowIndex'},
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', width: '20%' }
                ]
            });
            new $.fn.dataTable.FixedHeader( langTable );

            $('body').on('click', '.edit-language', function () {
                var id = $(this).data('row-id');
                var url = '{{ route('admin.language-settings.edit', ':id')}}';
                url = url.replace(':id', id);

                $('#modelHeading').html('@lang('app.edit') @lang('menu.language')');
                $.ajaxModal('#application-modal', url);
            });

            $('body').on('click', '#create-language', function () {
                var url = '{{ route('admin.language-settings.create') }}';

                $('#modelHeading').html('@lang('app.createNew') @lang('menu.language')');
                $.ajaxModal('#application-modal', url);
            });

            $('body').on('click', '.delete-language-row', function(){
                var id = $(this).data('row-id');
                const lang = {!! $languages !!}.filter(language => language.id == id);

                swal({
                    icon: "warning",
                    buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                    dangerMode: true,
                    title: "@lang('errors.areYouSure')",
                    text: "@lang('errors.deleteWarning')",
                })
                .then((willDelete) => {
                    if (willDelete) {
                        var url = "{{ route('admin.language-settings.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.unblockUI();
                                    // swal("Deleted!", response.message, "success");
                                    langTable._fnDraw();

                                    if (lang[0].status == 'enabled') {
                                        location.reload();
                                    }
                                }
                            }
                        });
                    }
                });
            });

            $('body').on('change', '.lang_status', function () {
                const id = $(this).data('lang-id');

                let url = '{{ route('admin.language-settings.changeStatus', ':id') }}'
                url = url.replace(':id', id);

                let status = '';
                if ($(this).is(':checked')) {
                    status = 'enabled';
                }
                else {
                    status = 'disabled';
                }

                $.easyAjax({
                    url: url,
                    type: 'POST',
                    container: '#langTable',
                    data: {
                        id: id,
                        status: status,
                        _method: 'PUT',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            location.reload();
                        }
                    }
                });
            });

            $(".booking_time_type").click(function(){
                let duration_type = '';
                if($(this).val()=='sum'){
                    duration_type = "@lang('messages.sumOfServiceTime').";
                }
                else if($(this).val()=='avg'){
                    duration_type = "@lang('messages.AvgOfServiceTime').";
                }
                else if($(this).val()=='max'){
                    duration_type = "@lang('messages.MaxServiceTime').";
                }
                else if($(this).val()=='min'){
                    duration_type = "@lang('messages.MinServiceTime').";
                }
                $('#info-msg').html(duration_type+'..!');
            });

            // *** save loyalty program settings start ***

            $('body').on('submit', '#loyalty_settings_form', function (e) {
                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "{{route('admin.settings.loyalty-program-setting.update')}}",
                    data: $(this).serialize(),
                    success: function (result) {
                        console.log(result);

                        if(result.status == "error")
                        {
                            $.each(result.error, function (key, value) { 

                                $.showToastr(value, 'error');                 
                                                        
                            });
                        }
                        else if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            });

            // *** save loyalty program settings end ***

            // *** save loyalty point settings start ***

            $('body').on('submit', '#loyalty_point_settings_form', function (e) {
                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "{{route('admin.settings.loyalty-point-setting.update')}}",
                    data: $(this).serialize(),
                    success: function (result) {
                        console.log(result);

                        if(result.status == "error")
                        {
                            $.each(result.error, function (key, value) { 

                                $.showToastr(value, 'error');                 
                                                        
                            });
                        }
                        else if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            });

            // *** save loyalty point settings end ***

            // *** save notification settings start ***

            $('body').on('submit', '#notification_settings_form', function (e) {
                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "{{route('admin.settings.notification-setting.update')}}",
                    data: $(this).serialize(),
                    success: function (result) {
                        console.log(result);

                        if(result.status == "error")
                        {
                            $.each(result.error, function (key, value) { 

                                $.showToastr(value, 'error');                 
                                                        
                            });
                        }
                        else if(result.status == "success")
                        {
                            $.showToastr(result.message, 'success');
                        }
                        else
                        {
                            $.showToastr(result.message, 'error');
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            });

            // *** save notification settings end ***
        });

        function isNumberKey(evt)
        {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

        function disableSlotChanged()
        {
            if($('#disable_slot').is(":checked"))
            {
                $("#div_disable_slot").show(10);
                $('#multi_task_user').prop("checked", false);
            }
            else
            {
                $("#div_disable_slot").hide(10);
            }
        }

        function multiTaskingEmpChanged()
        {
            if($('#multi_task_user').is(":checked"))
            {
                $("#div_disable_slot").hide(10);
                $('#disable_slot').prop("checked", false);
            }
        }

    </script>
    @include('vendor.froiden-envato.update.update_script')

@endpush
