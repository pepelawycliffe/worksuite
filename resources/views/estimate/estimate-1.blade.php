<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@lang('app.estimate') #{{ (is_null($estimate->estimate_number)) ? $estimate->id : $estimate->estimate_number }}</title>
    <style>
        /* Please don't remove this code it is useful in case of add new language in dompdf */

        /* @font-face {
            font-family: Hind;
            font-style: normal;
            font-weight: normal;
            src: url({{ asset('fonts/hind-regular.ttf') }}) format('truetype');
        } */

        /* For hindi language  */

        /* * {
           font-family: Hind, DejaVu Sans, sans-serif;
        } */

        /* For japanese language */

        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/TH_Sarabun.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunBoldItalic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunItalic.ttf') }}") format('truetype');
        }


        @if($invoiceSetting->is_chinese_lang)
            @font-face {
            font-family: SimHei;
            /*font-style: normal;*/
            font-weight: bold;
            src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
        }
        @endif

        @php
            $font = '';
            if($invoiceSetting->locale == 'ja') {
                $font = 'ipag';
            } else if($invoiceSetting->locale == 'hi') {
                $font = 'hindi';
            } else if($invoiceSetting->locale == 'th') {
                $font = 'THSarabun';
            }else if($invoiceSetting->is_chinese_lang) {
                $font = 'SimHei';
            } else {
                $font = 'noto-sans';
            }
        @endphp

        * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 100%;
            height: auto;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: Verdana, Arial, Helvetica, sans-serif;
        }

        h2 {
            font-weight:normal;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 11px;
        }

        #logo img {
            height: 55px;
            margin-bottom: 15px;
        }

        #company {

        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name, div.name {
            font-size: 1.2em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {

        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 5px 10px 7px 10px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td.desc h3, table td.qty h3 {
            color: #57B223;
            font-size: 1em;
            font-weight: normal;
            margin: 0 0 0 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
            width: 5%;
        }



        table .unit {
            background: #DDDDDD;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total
        {
            font-size: 1em;
            text-align: center;
        }
        table td.text-center
        {
            text-align: center;
        }


        table td.unit{
            width: 40%;
        }
        @if($invoiceSetting->hsn_sac_code_show)
            table td.desc{
            width: 10%;
        }
        table .desc {
            text-align: left;
            width: 35%;
        }
        @else
            table td.desc{
            width: 20%;
        }
        table .desc {
            text-align: left;
            width: 65%;
        }
        @endif

        table td.qty{
            width: 8%;
        }

        .status {
            margin-top: 15px;
            padding: 1px 8px 5px;
            font-size: 1.3em;
            width: 80px;
            color: #fff;
            float: right;
            text-align: center;
            display: inline-block;
        }

        .status.unpaid {
            background-color: #E7505A;
        }
        .status.paid {
            background-color: #26C281;
        }
        .status.cancelled {
            background-color: #95A5A6;
        }
        .status.error {
            background-color: #F4D03F;
        }

        table tr.tax .desc {
            text-align: right;
            color: #1BA39C;
        }
        table tr.discount .desc {
            text-align: right;
            color: #E43A45;
        }
        table tr.subtotal .desc {
            text-align: right;
            color: #1d0707;
        }
        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 10px 20px 10px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-bottom: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        table.billing td {
            background-color: #fff;
        }

        table td div#invoiced_to {
            text-align: left;
        }

        #notes{
            color: #767676;
            font-size: 11px;
        }

        .item-summary{
            font-size: 12px
        }

        .mb-3{
            margin-bottom: 1rem;
        }
        .logo {
            text-align: right;
        }
        .logo img {
            max-height: 70px;
        }
        .page_break { page-break-before: always; }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }
        @if($invoiceSetting->is_chinese_lang)
            body
        {
            font-weight: normal !important;
        }

        @endif
         .item-summary {
            font-size: 11px;
            text-align: left ;

        }
        .descItem {
            border-bottom: none !important;
        }
    </style>
</head>
<body>

<header class="clearfix">

    <table cellpadding="0" cellspacing="0" class="billing">
        <tr>
            <td>
                <div id="invoiced_to">
                    @if($estimate->client && !is_null($estimate->client->client))
                        <small>@lang("app.client"):</small>
                        <h2 class="name">{{ $estimate->client->client[0]->company_name }}</h2>
                        <div>{!! nl2br($estimate->client->client[0]->address) !!}</div>
                    @endif
                </div>
            </td>
            <td>
                <div id="company">
                    <div class="logo">
                        <img src="{{ invoice_setting()->logo_url }}" alt="home" class="dark-logo" />
                    </div>
                    <small>@lang("modules.invoices.generatedBy"):</small>
                    <h2 class="name">{{ ucwords($global->company_name) }}</h2>
                    @if(!is_null($settings))
                        <div>{!! nl2br($global->address) !!}</div>
                        <div>P: {{ $global->company_phone }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</header>
<main>
    <div id="details" class="clearfix">

        <div id="invoice">
            <h1>{{ (is_null($estimate->estimate_number)) ? '#'.$estimate->id : $estimate->estimate_number }}</h1>
            <div class="date">@lang("modules.estimates.validTill"): {{ $estimate->valid_till->format($global->date_format) }}</div>
            <div class="">@lang('app.status'): {{ __('app.'.$estimate->status) }}</div>
        </div>

    </div>
    @php
        $colspan = ($invoiceSetting->hsn_sac_code_show) ? 5 : 4;
    @endphp
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="desc">@lang("modules.invoices.item")</th>
            @if($invoiceSetting->hsn_sac_code_show)
                <th class="desc"> @lang('modules.invoices.hsnSacCode')</th>
            @endif
            <th class="qty">@lang("modules.invoices.qty")</th>
            <th class="qty">@lang("modules.invoices.unitPrice") ({!! htmlentities($estimate->currency->currency_code)  !!})</th>
            <th class="unit">@lang("modules.invoices.price") ({!! htmlentities($estimate->currency->currency_code)  !!})</th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 0; ?>
        @foreach($estimate->items as $item)

            @if($item->type == 'item')
                <tr style="page-break-inside: avoid;">
                    <td class="no @if(!is_null($item->item_summary)) descItem @endif">{{ ++$count }}</td>
                    <td class="desc @if(!is_null($item->item_summary)) descItem @endif"><h3>{{ ucfirst($item->item_name) }}</h3>
                    </td>
                    @if($invoiceSetting->hsn_sac_code_show)
                        <td class="qty @if(!is_null($item->item_summary)) descItem @endif">{{ $item->hsn_sac_code ?? '--' }}</td>
                    @endif
                    
                    <td class="qty @if(!is_null($item->item_summary)) descItem @endif"><h3>{{ $item->quantity }}</h3></td>
                    <td class="qty @if(!is_null($item->item_summary)) descItem @endif"><h3>{{  currency_formatter($item->unit_price,'') }}</h3></td>
                    <td class="unit @if(!is_null($item->item_summary)) descItem @endif">{{currency_formatter($item->unit_price,'')}}</td>
                </tr>
                @if(!is_null($item->item_summary))
                    <tr>
                        <td class="no"></td>
                        <td class="item-summary" @if($invoiceSetting->hsn_sac_code_show) colspan="4" @else colspan="3" @endif>
                            {{ $item->item_summary }}
                        </td>
                        <td></td>
                    </tr>
                @endif
            @endif
        @endforeach
        <tr style="page-break-inside: avoid;" class="subtotal">
            <td class="no"> </td>
            @if($invoiceSetting->hsn_sac_code_show)
                <td class="qty"> </td>
            @endif
            <td class="qty"> </td>
            <td class="qty"> </td>
            <td class="desc">@lang("modules.invoices.subTotal")</td>
            <td class="unit">{{currency_formatter($estimate->sub_total,'')}}</td>
        </tr>
        @if($discount != 0 && $discount != '')
            <tr style="page-break-inside: avoid;" class="discount">
                <td class="no"> </td>
                @if($invoiceSetting->hsn_sac_code_show)
                    <td class="qty"> </td>
                @endif
                <td class="qty"> </td>
                <td class="qty"> </td>
                <td class="desc">@lang("modules.invoices.discount")</td>
                <td class="unit">-{{ currency_formatter($discount,'') }}</td>
            </tr>
        @endif
        @foreach($taxes as $key=>$tax)
            <tr style="page-break-inside: avoid;" class="tax">
                <td class="no"> </td>
                @if($invoiceSetting->hsn_sac_code_show)
                    <td class="qty"> </td>
                @endif
                <td class="qty"> </td>
                <td class="qty"> </td>
                <td class="desc">{{ strtoupper($key) }}</td>
                <td class="unit">{{ currency_formatter($tax, '') }}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        <tr dontbreak="true">
            <td colspan="{{ $colspan }}">@lang("modules.invoices.total")</td>
            <td style="text-align: center">{{  currency_formatter($estimate->total,'')}}</td>
        </tr>

        </tfoot>
    </table>
        <p> </p>
        <hr>
        <p id="notes">
            @if(!is_null($estimate->note))
                {!! nl2br($estimate->note) !!}<br>
            @endif
            @if(!is_null($invoiceSetting->estimate_terms))
               {!! nl2br($invoiceSetting->estimate_terms) !!}
            @endif
        </p>

        @if($estimate->sign)
            <div style="text-align: right;">
                <h2 class="name" style="margin-bottom: 20px;">@lang('modules.estimates.signature') (@lang('app.customers'))</h2>
                    <img src="{{ $estimate->sign->signature }}" style="width: 250px;">
            </div>
        @endif

    {{--Custom fields data--}}
    @if(isset($fields) && count($fields) > 0)
        <div class="page_break"></div>
        <h3 class="box-title m-t-20 text-center h3-border"> @lang('modules.projects.otherInfo')</h3>
        <table  style="background: none" border="0" cellspacing="0" cellpadding="0" width="100%">
            @foreach($fields as $field)
                <tr>
                    <td style="text-align: left;background: none;" >
                        <div class="desc">{{ ucfirst($field->label) }}</div>
                        <p id="notes">
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
    @endif
</main>
</body>
</html> 