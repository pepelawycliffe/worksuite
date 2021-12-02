@extends('layouts.super-admin')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
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
    <link rel="stylesheet"
        href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

    <style>
        .m-b-10 {
            margin-bottom: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .hide-box {
            display: none;
        }

        .register {
            margin-top: 20px;
        }

    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-inverse">
                <div class="vtabs customvtab m-t-10">
                    @if ($global->front_design == 1)
                        @include('sections.front_setting_new_theme_menu')
                    @else
                        @include('sections.front_setting_menu')
                    @endif
                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <hr>
                                    <div class="form-body">
                                        <h4>@lang('app.menu.registrationPage')</h4>
                                        <div class="row register">
                                            <div class="col-md-4">
                                                <div class="switchery-demo">
                                                    <input type="checkbox" name="registration_open" id="registration_open"
                                                        class="js-switch packeges" data-size="medium" data-color="#00c292"
                                                        data-secondary-color="#f96262" value="true"
                                                        @if ($registrationStatus->registration_open == 1) checked @endif />
                                                </div>
                                                <div class="col-md-12" style="margin-top: 15px;"></div>

                                                <span>*</span><span id="registation-text"
                                                style='color:rgb(0,128,0);'>@lang('messages.registrationOpen')</span>
                                            </div>
                                            <div class ="col-md-4">
                                                <div class="switchery-demo">
                                                    <input type="checkbox" name="enable_register" id="enable_register"
                                                        class="js-switch packeges" data-size="medium" data-color="#00c292"
                                                        data-secondary-color="#f96262" value="true"
                                                        @if ($registrationStatus->enable_register == 1) checked @endif />
                                                </div>
                                                  <div class="col-md-12" style="margin-top: 15px;"></div>

                                                <span>@lang('modules.accountSettings.enableRegister')</span>
                                            </div>

                                        </div>
                                       

                                        <div class="col-md-12" style="margin-top: 20px;"></div>
                                        <div class="row disable-message hide-box">
                                            <div class="tab-content">
                                                <div id="vhome3" class="tab-pane active">
                                                    <div class="row">
                                                        <input type="hidden" name="setting_id" id="setting_id"
                                                            value={{ $registrationStatus->id }}>
                                                        <div class="col-sm-12">


                                                            <div class="white-box">
                                                                <label>@lang('messages.registerMessage')</label>
                                                                {{-- <h3 class="box-title m-b-0">@lang('app.menu.menu') @lang('app.menu.settings')</h3> --}}
                                                                <hr>
                                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                    <li class="nav-item active">
                                                                        <a class="nav-link active" id="en-tab"
                                                                            data-toggle="tab" data-language-id="0"
                                                                            href="#en" role="tab" aria-controls="en"
                                                                            aria-selected="true">
                                                                            <span class="flag-icon flag-icon-us"></span>
                                                                            English
                                                                        </a>
                                                                    </li>
                                                                    @forelse ($activeLanguages as $language)
                                                                        <li class="nav-item">
                                                                            <a class="nav-link"
                                                                                id="{{ $language->language_code }}-tab"
                                                                                data-toggle="tab"
                                                                                data-language-id="{{ $language->id }}"
                                                                                href="#{{ $language->language_code }}"
                                                                                role="tab"
                                                                                aria-controls="{{ $language->language_code }}"
                                                                                aria-selected="true">
                                                                                <span
                                                                                    class="flag-icon flag-icon-{{ $language->language_code }}"></span>
                                                                                {{ ucfirst($language->language_name) }}
                                                                            </a>
                                                                        </li>
                                                                    @empty
                                                                    @endforelse
                                                                </ul>
                                                                <div class="tab-content" id="edit-form">
                                                                    @include('super-admin.sign-up-setting.edit-form')
                                                                </div>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    </div> <!-- .row -->

                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/switchery/dist/switchery.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

    <script>
        function changeForm(target) {
            $.easyAjax({
                url: "{{ route('super-admin.sign-up-setting.changeForm') }}",
                container: '#editSettings',
                data: {
                    language_settings_id: $(target).data('language-id')
                },
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#edit-form').html(response.view);
                    }
                }
            })
        }

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            changeForm(e.target);
        })

        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ["view", ["fullscreen", "codeview"]]
            ]
        });

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function(elem) {
            new Switchery($(this)[0], $(this).data());

        });

        if (!$('#registration_open').is(':checked')) {
            $('.disable-message').show();
            $('#registation-text').text("@lang('messages.registrationClosed')").css("color", "#ff0000");
        }
        $('#registration_open').change(function() {
            var status = $(this).is(':checked') ? 1 : 0;
           
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: '{{ route('super-admin.sign-up-setting.update', $registrationStatus->id) }}',
                type: "PUT",
                data: {
                    'status': status,
                    '_token': token
                },
                success: function(response) {
                    if (status == 1) {
                        $('.disable-message').hide();
                        $('#registation-text').text("@lang('messages.registrationOpen')").css("color",
                            "#008000");
                    } else {
                        $('.disable-message').show();
                        $('#registation-text').text("@lang('messages.registrationClosed')").css("color",
                            "#ff0000");
                    }
                }
            })


        })

        $('#enable_register').change(function() {
            var enable_register = $(this).is(':checked') ? 1 : 0;
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                url: '{{ route('super-admin.sign-up-setting.update', $registrationStatus->id) }}',
                type: "PUT",
                data: {
                    'enable_register': enable_register,
                    '_token': token
                },
                success: function(response) {
                    
                }
            })


        })

        $('body').on('click', '#save-form', function() {
            var token = "{{ csrf_token() }}";
            var enable_register = $('#enable_register').is(':checked') ? 1 : 0;
            var setting_id = $('#setting_id').val();
            $.easyAjax({
                url: "{{ route('super-admin.sign-up-setting.store') }}",
                container: '#editSettings',
                type: "POST",
                file: true,
                data: {
                    language_settings_id: $('#editSettings').data('language-id'),
                    '_token': token,
                    'enable_register': enable_register,
                    'id': setting_id,
                }
            })
        });
    </script>
@endpush
