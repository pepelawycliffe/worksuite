@extends('layouts.super-admin')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">{{ __($pageTitle) }}</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.super_admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-xs-12">

{{--                                    <h3 class="box-title m-b-0">@lang("app.menu.googleCalendarSetting")</h3>--}}

{{--                                    <p class="text-muted m-b-10 font-13">--}}
{{--                                        @lang("app.menu.googleCalendarSetting")--}}
{{--                                    </p>--}}


                                    {!! Form::open(['id'=>'editSlackSettings','class'=>'ajax-form','method'=>'PUT']) !!}

                                    <div class="form-body">
                                        <div class="form-group">
                                            <label for="google_client_id" class="control-label">@lang('app.clientId')</label>
                                            <input type="text" class="form-control  form-control-lg"
                                                   id="google_client_id" name="google_client_id"
                                                   value="{{ $calendarSetting->google_client_id }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="google_client_secret" class="control-label">@lang('app.clientSecret')</label>
                                            <input type="password" class="form-control  form-control-lg"
                                                   id="google_client_secret" name="google_client_secret"
                                                   value="{{ $calendarSetting->google_client_secret }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="company_name">@lang('app.status')</label>
                                            <select name="google_calendar_status" class="form-control" id="">
                                                <option
                                                        @if($calendarSetting->google_calendar_status == 'inactive') selected @endif
                                                value="inactive">@lang('app.inactive')</option>
                                                <option
                                                        @if($calendarSetting->google_calendar_status == 'active') selected @endif
                                                value="active">@lang('app.active')</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-actions m-t-20">
                                        <button type="submit" id="save-form"
                                                class="btn btn-success waves-effect waves-light m-r-10">
                                            @lang('app.update')
                                        </button>
                                    </div>

                                    {!! Form::close() !!}

                                </div>
                                <!-- .row -->

                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <!-- .row -->


        @endsection

        @push('footer-script')
        <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
        <script>
            $('#save-form').click(function () {
                $.easyAjax({
                    url: '{{route('super-admin.google-calendar-settings.update', ['1'])}}',
                    container: '#editSlackSettings',
                    type: "POST",
                    data: $('#editSlackSettings').serialize(),
                })
            });
        </script>
    @endpush
