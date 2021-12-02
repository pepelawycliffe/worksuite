@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
@endpush

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('app.update') @lang('app.googleCalendar') @lang('app.setting')</div>

                <div class="vtabs customvtab m-t-10">

                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="form-body ">
                                    @if ($superadmin->google_calendar_status == 'active')
                                        @if(\Illuminate\Support\Facades\Session::has('message'))
                                            <p class="alert alert-success">{{ \Illuminate\Support\Facades\Session::get('message') }}</p>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class="">@lang('app.googleCalendar')</h4>
                                                <a href="{{ route('googleAuth') }}">
                                                    <button type="button" class="btn btn-success">
                                                        <i class="fa fa-play"></i>
                                                        @if ($company->googleAccount) @lang('app.change') @lang('app.googleCalendar') @lang('app.account')
                                                        @else @lang('app.connect') @lang('app.googleCalendar') @lang('app.account')@endif
                                                    </button>
                                                </a>

                                            </div>
                                            <div class="col-md-3">
                                                <h4>@lang('app.status')</h4>
                                                <div class="form-group">
                                                    <label class="label {{ $company->googleAccount ? 'badge-success' : 'badge-danger' }}">{{ $company->googleAccount ? __('app.connected') : __('app.notConnected') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="row">
                                            <div class="col-md-6">
                                                @if ($company->googleAccount)
                                                    <button type="button" id="googleCalendarDisconnect" class="btn btn-danger">
                                                        @lang('app.disconnect') @lang('app.googleCalendar') </button>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                            <label class="control-label text-danger">@lang("app.superAdminAllowCalendarMessage")</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        @if (company()->googleAccount)

        $('body').on('click', '#googleCalendarDisconnect', function(){
            var id = $(this).data('file-id');
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.disconnectAccount')",
                dangerMode: true,
                icon: 'warning',
                buttons: {
                    cancel: "@lang('messages.confirmNoArchive')",
                    confirm: {
                        text:  "@lang('messages.disconnectConfirmation')",
                        value: true,
                        visible: true,
                        className: "danger",
                    }
                }
            }).then(function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('googleAuth.destroy',company()->googleAccount->id) }}";
                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
        @endif
    </script>
@endpush

