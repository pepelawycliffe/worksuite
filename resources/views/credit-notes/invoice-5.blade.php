<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Template CSS -->
    <!-- <link type="text/css" rel="stylesheet" media="all" href="css/main.css"> -->

    <title>@lang('app.invoice') - {{ $creditNote->cn_number }}</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <style>

        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/TH_Sarabun.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunBoldItalic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunItalic.ttf') }}") format('truetype');
        }

        @if($company->locale == 'zh-hk' || $company->locale == 'zh-cn' || $company->locale == 'zh-sg' ||
        $company->locale == 'zh-tw' || $company->locale == 'cn')
        @font-face {
            font-family: SimHei;
            /*font-style: normal;*/
            font-weight: bold;
            src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
        }
        @endif

        @php
            $font = '';
            if($company->locale == 'ja') {
                $font = 'ipag';
            } else if($company->locale == 'hi') {
                $font = 'hindi';
            } else if($company->locale == 'th') {
                $font = 'THSarabun';
            } else if($company->locale == 'zh-hk' || $company->locale == 'zh-cn' || $company->locale == 'zh-sg' ||
            $company->locale == 'zh-tw' || $company->locale == 'cn') {
                $font = 'SimHei';
            }else {
                $font = 'noto-sans';
            }
        @endphp
        @if($company->locale == 'zh-hk' || $company->locale == 'zh-cn' || $company->locale == 'zh-sg' ||
            $company->locale == 'zh-tw' || $company->locale == 'cn')
            body
        {
            font-weight: normal !important;
        }
        @endif
        * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }
        body {
            margin: 0;
        }

        .bg-grey {
            background-color: #F2F4F7;
        }

        .bg-white {
            background-color: #fff;
        }

        .border-radius-25 {
            border-radius: 0.25rem;
        }

        .p-25 {
            padding: 1.25rem;
        }

        .f-13 {
            font-size: 13px;
        }

        .f-14 {
            font-size: 14px;
        }

        .f-15 {
            font-size: 15px;
        }

        .f-21 {
            font-size: 21px;
        }

        .text-black {
            color: #28313c;
        }

        .text-grey {
            color: #616e80;
        }

        .font-weight-700 {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .line-height {
            line-height: 24px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .b-collapse {
            border-collapse: collapse;
        }

        .heading-table-left {
            padding: 6px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            border-right: 0;
        }

        .heading-table-right {
            padding: 6px;
            border: 1px solid #DBDBDB;
            border-left: 0;
        }

        .unpaid {
            color: #000000;
            border: 1px solid #000000;
            position: relative;
            padding: 11px 22px;
            font-size: 15px;
            border-radius: 0.25rem;
            width: 100px;
            text-align: center;
        }

        .main-table-heading {
            border: 1px solid #DBDBDB;
            background-color: #f1f1f3;
            font-weight: 700;
        }

        .main-table-heading td {
            padding: 11px 10px;
            border: 1px solid #DBDBDB;
        }

        .main-table-items td {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
        }

        .total-box {
            border: 1px solid #e7e9eb;
            padding: 0px;
            border-bottom: 0px;
        }

        .subtotal {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
        }

        .subtotal-amt {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-right: 0;
        }

        .total {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
            border-left: 0;
        }

        .total-amt {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-right: 0;
            font-weight: 700;
        }

        .balance {
            font-size: 16px;
            font-weight: bold;
            background-color: #f1f1f3;
        }

        .balance-left {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
        }

        .balance-right {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-right: 0;
        }

        .centered {
            margin: 0 auto;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .leftaligned {
            margin-left: 0;
            margin-right: auto;
        }

        .page_break {
            page-break-before: always;
        }
        #logo {
            height: 55px;
        }

    </style>

</head>

<body class="content-wrapper">
@php
    $colspan = ($invoiceSetting->hsn_sac_code_show) ? 3 : 2;
@endphp
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
            <!-- Table Row Start -->
            <tr>
                <td><img src="{{ invoice_setting()->logo_url }}" alt="{{ ucwords($global->company_name) }}" id="logo" /></td>
                <td align="right" class="f-21 text-black font-weight-700 text-uppercase">@lang('app.credit-note')</td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td>
                    <p class="line-height mt-1 mb-0 f-14 text-black">
                        {{ ucwords($global->company_name) }}<br>
                        @if (!is_null($settings))
                            {!! nl2br($global->address) !!}<br>
                            {{ $global->company_phone }}
                        @endif
                        @if ($invoiceSetting->show_gst == 'yes' && !is_null($invoiceSetting->gst_number))
                            <br>@lang('app.gstIn'): {{ $invoiceSetting->gst_number }}
                        @endif
                    </p>
                </td>
                <td>
                    <table class="text-black mt-1 f-13 b-collapse rightaligned">
                        @if ($creditNote)
                            <tr>
                                <td class="heading-table-left">@lang('app.credit-note')</td>
                                <td class="heading-table-right">{{ $creditNote->cn_number }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="heading-table-left">@lang('app.menu.issues') Date:</td>
                            <td class="heading-table-right">{{ $creditNote->issue_date->format($global->date_format) }}
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td height="10"></td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td colspan="2">
                    @if (!is_null($creditNote->project) && !is_null($creditNote->project->client))
                        @php
                            $client = $creditNote->project;
                        @endphp
                    @elseif(!is_null($creditNote->client_id) && !is_null($creditNote->clientdetails))
                        @php
                            $client = $creditNote->client;
                        @endphp
                    @endif

                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="f-14 text-black">
                                <p class="line-height mb-0">
                                    <span
                                        class="text-grey text-capitalize">@lang("modules.invoices.billedTo")</span><br>
                                        @if(isset($client))
                                                 {{ ucwords($client->client_details->company_name) }}<br>
                                              {!! nl2br($client->client_details->address) !!}
                                              @else
                                              --
                                        @endif
                                    
                                </p>

                                @if ($invoiceSetting->show_gst == 'yes' && !is_null($client->client_details->gst_number))
                                    <br>@lang('app.gstIn'):
                                    {{ $client->client_details->gst_number }}
                                @endif
                            </td>
                            <td class="f-14 text-black">
                                @if ($creditNote->show_shipping_address === 'yes')
                                    <p class="line-height"><span
                                            class="text-grey text-capitalize">@lang('app.shippingAddress')</span><br>
                                        {!! nl2br($client->client_details->address) !!}</p>
                                @endif
                            </td>
                            <td align="right">
                                <div class="text-uppercase bg-white unpaid rightaligned">
                                    {{ ucwords($creditNote->status) }}</div>
                            </td>
                        </tr>
                    </table>
                </td>


            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td height="30" colspan="2"></td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td colspan="2">
                    <table width="100%" class="f-14 b-collapse">
                        <!-- Table Row Start -->
                        <tr class="main-table-heading text-grey">
                            <td>@lang('app.description')</td>
                            @if($invoiceSetting->hsn_sac_code_show)
                                <th> @lang('modules.invoices.hsnSacCode')</th>
                            @endif
                            <td align="right">@lang("modules.invoices.qty")</td>
                            <td align="right">@lang("modules.invoices.unitPrice") ({{ $creditNote->currency->currency_code }})</td>
                            <td align="right">@lang("modules.invoices.amount") ({{ $creditNote->currency->currency_code }})</td>
                        </tr>
                        <!-- Table Row End -->
                        @foreach ($creditNote->items as $item)
                            @if ($item->type == 'item')
                                <!-- Table Row Start -->
                                <tr class="main-table-items text-black">
                                    <td>{{ ucfirst($item->item_name) }}</td>
                                    @if($invoiceSetting->hsn_sac_code_show)
                                        <td>{{ $item->hsn_sac_code ?? '--' }}</td>
                                    @endif
                                    <td align="right">{{ $item->quantity }}</td>
                                    <td align="right">{{ currency_formatter( $item->unit_price,'') }}</td>
                                    <td align="right">{{ $creditNote->currency->currency_symbol }}
                                        {{ currency_formatter( $item->amount,'') }}</td>
                                </tr>
                                <!-- Table Row End -->
                                @if (!is_null($item->item_summary))
                                    <!-- Table Row Start -->
                                    <tr class="main-table-items text-black f-13">
                                        <td colspan="4">{{ $item->item_summary }}</td>
                                    </tr>
                                    <!-- Table Row End -->
                                @endif
                            @endif
                        @endforeach
                        <!-- Table Row Start -->
                        <tr>
                            <td colspan="{{ $colspan }}"></td>
                            <td colspan="2" class="total-box">
                                <table width="100%" class="b-collapse">
                                    <!-- Table Row Start -->
                                    <tr align="right" class="text-grey">
                                        <td width="50%" class="subtotal">@lang("modules.credit-notes.total")</td>
                                        <td class="subtotal-amt">
                                            {{ currency_formatter($creditNote->total,'') }}</td>
                                    </tr>
                                    <!-- Table Row End -->
                                    @if ($discount != 0 && $discount != '')
                                        <!-- Table Row Start -->
                                        <tr align="right" class="text-grey">
                                            <td width="50%" class="subtotal">@lang("modules.credit-notes.creditAmountUsed")</td>
                                            <td class="subtotal-amt">{{ currency_formatter( $discount,'') }}</td>
                                        </tr>
                                        <!-- Table Row End -->
                                    @endif
                                    @foreach ($taxes as $key => $tax)
                                        <!-- Table Row Start -->
                                        <tr align="right" class="text-grey">
                                            <td width="50%" class="subtotal">{{ strtoupper($key) }}</td>
                                            <td class="subtotal-amt">{{ currency_formatter( $tax,'') }}</td>
                                        </tr>
                                        <!-- Table Row End -->
                                    @endforeach
                                    <!-- Table Row Start -->
                                    <tr align="right" class="text-grey">
                                        <td width="50%" class="total">@lang("modules.credit-notes.creditAmountUsed")</td>
                                        <td class="total-amt f-15">{{ currency_formatter($creditNote->creditAmountUsed(),'') }}</td>
                                    </tr>
                                    <!-- Table Row End -->
                                    <!-- Table Row Start -->
                                    <tr align="right" class="balance text-black">
                                        <td>@lang('modules.credit-notes.creditAmountRemaining')</td>
                                         <td style="text-align: center">{{ currency_formatter($creditNote->creditAmountRemaining(),'') }}</td>
                                    </tr>
                                    <!-- Table Row End -->
                                </table>
                            </td>
                        </tr>
                        <!-- Table Row End -->
                    </table>
                </td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td height="30" colspan="2"></td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr>
                <td width="50%" class="f-14">@lang('app.note')</td>
                <td width="50%" class="f-14">@lang('modules.invoiceSettings.invoiceTerms')</td>
            </tr>
            <!-- Table Row End -->
            <!-- Table Row Start -->
            <tr class="text-grey">
                <td width="50%" class="f-14 line-height">{!! nl2br($creditNote->note) !!}</td>
                <td width="50%" class="f-14 line-height">@if($creditNote->status == 'open')
                        {!! nl2br($creditNoteSetting->credit_note_terms) !!}
                        @endif
                </td>
            </tr>
            <!-- Table Row End -->
        </tbody>
    </table>

{{--Custom fields data--}}
@if(isset($fields) && count($fields) > 0)
    <div class="page_break"></div>
        <h3 class="box-title m-t-20 text-center h3-border"> @lang('modules.projects.otherInfo')</h3>
        <table  style="background: none" border="0" cellspacing="0" cellpadding="0" width="100%">
                @foreach($fields as $field)
                    <tr>
                        <td style="text-align: left;background: none;" >
                            <div class="f-14">{{ ucfirst($field->label) }}</div>
                            <p  class="f-14 line-height text-grey">
                                @if( $field->type == 'text')
                                    {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}
                                @elseif($field->type == 'password')
                                    {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}
                                @elseif($field->type == 'number')
                                    {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}

                                @elseif($field->type == 'textarea')
                                    {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}

                                @elseif($field->type == 'radio')
                                    {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? $invoice->custom_fields_data['field_'.$field->id] : '-' }}
                                @elseif($field->type == 'select')
                                    {{ (!is_null($invoice->custom_fields_data['field_'.$field->id]) && $invoice->custom_fields_data['field_'.$field->id] != '') ? $field->values[$invoice->custom_fields_data['field_'.$field->id]] : '-' }}
                                @elseif($field->type == 'checkbox')
                                    {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? $field->values[$invoice->custom_fields_data['field_'.$field->id]] : '-' }}
                                @elseif($field->type == 'date')
                                    {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? \Carbon\Carbon::parse($invoice->custom_fields_data['field_'.$field->id])->format($global->date_format) : '--'}}
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforeach
            </table>
    </div>

@endif
  
        </body>

        </html>
