@extends('layouts.app')
@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="{{ route('admin.currency.create') }}"
               class="btn btn-outline btn-success btn-sm">@lang('modules.currencySettings.addNewCurrency') <i
                        class="fa fa-plus" aria-hidden="true"></i></a>
            <a href="javascript:;" id="update-exchange-rates"
               class="btn btn-outline btn-info btn-sm">@lang('app.update') @lang('modules.currencySettings.exchangeRate')
                <i class="fa fa-refresh" aria-hidden="true"></i></a>

            <ol class="breadcrumb">
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>

        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">

                <div class=" sttabs tabs-style-line">
                    @include('admin.currencies.tabs')

                    @include('sections.admin_setting_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="white-box">
                                    {!! Form::open(['id'=>'updateCurrencyFormat','class'=>'ajax-form','method'=>'POST']) !!}
                                        <div class="form-group">
                                            <input type ="hidden" value = "{{$currencyFormatSetting->id ?? ''}}" id ="currency_id" name = "id">
                                            <label for="company_email">@lang('modules.currencySettings.currencyPosition')</label>
                                            <select class="select2 form-control" data-placeholder="@lang('modules.currencySettings.thousandSeparator')"  id="currency_position" name="currency_position">
                                                <option @if ($currencyFormatSetting->currency_position == 'left') selected @endif value="left">@lang('modules.currencySettings.left')</option>
                                                    <option  @if ($currencyFormatSetting->currency_position == 'right') selected @endif @if ($currencyFormatSetting->currency_position == 'right') selected @endif  value="right">@lang('modules.currencySettings.right')</option>
                                                    <option @if ($currencyFormatSetting->currency_position == 'left_with_space') selected @endif   value="left_with_space">@lang('modules.currencySettings.leftWithSpace')</option>
                                                    <option @if ($currencyFormatSetting->currency_position == 'right_with_space') selected @endif value="right_with_space">@lang('modules.currencySettings.rightWithSpace')</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="thousand_separator">@lang('modules.currencySettings.thousandSeparator')</label>
                                            <input type="text" class="form-control" value = "{{$currencyFormatSetting->thousand_separator ?? ''}}" id="thousand_separator" name="thousand_separator"
                                             placeholder="e.g. ,">
                                        </div>
                                        <div class="form-group">
                                            <label for="decimal_separator">@lang('modules.currencySettings.decimalSeparator')</label>
                                            <input type="text" class="form-control" value = "{{$currencyFormatSetting->decimal_separator ?? ''}}" id="decimal_separator" name="decimal_separator"
                                             placeholder="e.g. .">
                                        </div>
                                        <div class="form-group">
                                            <label for="no_of_decimal">@lang('modules.currencySettings.numberOfDecimal')</label>
                                            <input type="text" class="form-control" value = "{{$currencyFormatSetting->no_of_decimal ?? ''}}"  id="no_of_decimal" name="no_of_decimal"
                                            placeholder="no_of_decimal">
                                            
                                        </div>
                                        <div class="form-group">
                                            <p>@lang('modules.currencySettings.sample') - <span id="formatted_currency">{{ $defaultFormattedCurrency ?? '' }}</span> </p>
                                            <input type ="hidden" name="sample_data" id="sample_data" value="{{ $defaultFormattedCurrency ??'' }}"/>
                                        </div>
                                        
                                        <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                            @lang('app.save')
                                        </button>

                                        {!! Form::close() !!}
                                        
                                    </div>
                                </div>
                            </div>    <!-- .row -->

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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
           $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        $('ul.showClientTabs .currencyFormatSetting').addClass('tab-current');

       $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.currency.update-currency-format')}}',
            container: '#updateCurrencyFormat',
            type: "POST",
            data: $('#updateCurrencyFormat').serialize()
        })
    });
    $("body").on("change keyup", "#currency_position, #thousand_separator, #decimal_separator, #no_of_decimal", function() {
        let number              = 1234567.89;
        let no_of_decimal       = $('#no_of_decimal').val();
        let thousand_separator  = $('#thousand_separator').val();
        let currency_position   = $('#currency_position').val();
        let decimal_separator   = $('#decimal_separator').val();
        let formatted_currency  =  number_format(number, no_of_decimal, decimal_separator, thousand_separator, currency_position);
        $('#formatted_currency').html(formatted_currency);
        $('#sample_data').val(formatted_currency);
    });

    function number_format(number, decimals, dec_point, thousands_sep, currency_position)
    {
    // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var currency_symbol = '{{company()->currency->currency_symbol}}';
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        // number = dec_point == '' ? s[0] : s.join(dec);
        number = s.join(dec);
        switch (currency_position) {
            case 'left':
                    number = number+currency_symbol;
                break;
            case 'right':
                    number = currency_symbol+number;
                break;
            case 'left_with_space':
                    number = number+' '+currency_symbol;
                break;
            case 'right_with_space':
                    number = currency_symbol+' '+number;
                break;
            default:
                number = currency_symbol+number;
                break;
        }
        return number;
    }
    $('#update-exchange-rates').click(function () {
            var url = '{{route('admin.currency.update-exchange-rates')}}';
            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                        window.location.reload();
                    }
                }
            })
        });
    </script>
@endpush
