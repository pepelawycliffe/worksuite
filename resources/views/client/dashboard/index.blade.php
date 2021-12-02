@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <div class="col-md-3 pull-right hidden-xs hidden-sm">

                <select class="selectpicker language-switcher  pull-right" data-width="fit">
                    @if($global->timezone == "Europe/London")
                   <option value="en" @if($global->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-gb"></span>'>En</option>
                   @else
                   <option value="en" @if($global->locale == "en") selected @endif data-content='<span class="flag-icon flag-icon-us"></span>'>En</option>
                   @endif
                    @foreach($languageSettings as $language)
                        <option value="{{ $language->language_code }}"
                                @if($user->locale == $language->language_code) selected
                                @endif  data-content='<span class="flag-icon @if($language->language_code == 'zh-CN') flag-icon-cn @elseif($language->language_code == 'zh-TW') flag-icon-tw @else flag-icon-{{ $language->language_code }} @endif"></span>'>{{ $language->language_code }}</option>
                    @endforeach
                </select>
                @if ($company_details->count() > 1)
                    <select class="selectpicker company-switcher" data-width="fit" name="companies" id="companies">
                        @foreach ($company_details as $company_detail)
                            <option {{ $company_detail->company->id === $global->id ? 'selected' : '' }} value="{{ $company_detail->company->id }}">{{ ucfirst($company_detail->company->company_name) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<style>
    .col-in {
        padding: 0 20px !important;

    }

    .fc-event{
        font-size: 10px !important;
    }
    .front-dashboard .white-box{
        margin-bottom: 8px;
    }

    @media (min-width: 769px) {
        .panel-wrapper{
            height: 530px;
            overflow-y: auto;
        }
    }

</style>
@endpush

@section('content')
<div class="white-box">

    <div class="row dashboard-stats front-dashboard">
        @if(in_array('projects',$modules))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3">
                        <div>
                            <span class="bg-info-gradient"><i class="icon-layers"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-9 text-right">
                        <span class="widget-title"> @lang('modules.dashboard.totalProjects')</span><br>
                        <span class="counter">{{ $counts->totalProjects }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('tickets',$modules))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3">
                        <div>
                            <span class="bg-warning-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-9 text-right">
                        <span class="widget-title"> @lang('modules.tickets.totalUnresolvedTickets')</span><br>
                        <span class="counter">{{ $counts->totalUnResolvedTickets }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('invoices',$modules))
        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3">
                        <div>
                            <span class="bg-success-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-9 text-right">
                        <span class="widget-title"> @lang('modules.dashboard.totalPaidAmount')</span><br>
                        <span class="counter">{{ floor($counts->totalPaidAmount) }}</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="white-box">
                <div class="row">
                    <div class="col-xs-3">
                        <div>
                            <span class="bg-danger-gradient"><i class="ti-ticket"></i></span>
                        </div>
                    </div>
                    <div class="col-xs-9 text-right">
                        <span class="widget-title"> @lang('modules.dashboard.totalOutstandingAmount')</span><br>
                        <span class="counter">{{ floor($counts->totalUnpaidAmount) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    <!-- .row -->

    <div class="row" >

        @if(in_array('projects',$modules))
        <div class="col-md-6" id="project-timeline">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang("modules.dashboard.projectActivityTimeline")</div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="steamline">
                            @foreach($projectActivities as $activity)
                                <div class="sl-item">
                                    <div class="sl-left"><i class="fa fa-circle text-info"></i>
                                    </div>
                                    <div class="sl-right">
                                        <div><h6><a href="{{ route('client.projects.show', $activity->project_id) }}" class="text-danger">{{ ucwords($activity->project_name) }}:</a> {{ $activity->activity }}</h6> <span class="sl-date">{{ $activity->created_at->timezone($global->timezone)->diffForHumans() }}</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
            @if(in_array('projects',$modules))
                <div class="col-md-6">
                <div class="panel panel-inverse">
                    <div class="panel-heading">@lang('modules.dashboard.upcomingPayments')</div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <ul class="list-task list-group" data-role="tasklist">
                                <li class="list-group-item row" data-role="task">
                                    <div class="col-xs-4"><strong>@lang('app.invoiceNo')</strong> </div>
                                    <div class="col-xs-5"><span ><strong>@lang('app.amount')</strong></span></div>
                                    <span class="pull-right"><strong>@lang('modules.dashboard.dueDate')</strong></span>
                                </li>
                                @forelse($upcomingInvoices as $key=>$invoice)
                                    <a href="{{ route('client.invoices.show', $invoice->id) }}" >
                                        <li class="list-group-item row" data-role="task">
                                            <div class="col-xs-4">
                                                    <a href="{{ route('client.invoices.show', $invoice->id) }}" class="font-12">{{ ucwords($invoice->invoice_number) }}</a>
                                            </div>
                                            <div class="col-xs-5">
                                                {{ number_format((float)$invoice->amountDue(), 2, '.', '') }}
                                            </div>
                                            <label class="label label-danger pull-right col-xs-3">{{ $invoice->due_date->format($global->date_format) }}</label>
                                        </li>
                                    </a>
                                @empty
                                    <li class="list-group-item" data-role="task">
                                        @lang("messages.noUpcomingPayments")
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

    </div>
</div>
@endsection
