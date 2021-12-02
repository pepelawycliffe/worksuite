@extends('layouts.member-app')


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
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.products.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update') @lang('app.menu.products')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.css') }}">

@endpush

@section('content')

<div class="row">
    <div class="col-xs-12">

        <section>
            <div class="sttabs tabs-style-line">
                
                <div class="content-wrap">
                    <section id="section-line-3" class="show">
                        <div class="row">
                            <div class="col-xs-12" id="files-list-panel">
                                <div class="white-box">
                                    <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>

                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-6 b-r"> <strong>@lang('app.name')</strong> <br>
                                                <p class="text-muted">{{ $product->name }}</p>
                                            </div>
                                            <div class="col-xs-6"> <strong>@lang('app.price')</strong> <br>
                                                <p class="text-muted">{{ $product->price }}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-xs-6 b-r"> <strong>@lang('modules.productCategory.productCategory')</strong> <br>
                                                <p class="text-muted"> @if(isset($product->category)) {{ ucwords($product->category->category_name) }} @else {{ '--'}} @endif</p>
                                            </div>
                                            <div class="col-xs-6 b-r"> <strong>@lang('modules.productCategory.productSubCategory')</strong> <br>
                                                <p class="text-muted">@if(isset($product->category)) {{ ucwords($product->subcategory->category_name) }}@else {{'--'}} @endif</p>
                                            </div>

                                        </div>
                                        <hr>
                                        @if(@isset($product->tax))
                                            
                                        <div class="row">
                                            <div class="col-xs-6 b-r"> <strong>@lang('modules.invoices.tax')</strong> <br>
                                                <p class="text-muted">{{ $product->tax->name }}: {{ $product->tax->rate_percent }}%</p>
                                            </div>
                                        </div>
                                        <hr>
                                        @endisset
                                        <div class="row">
                                            <div class="col-xs-12"> <strong>@lang('app.description')</strong> <br>
                                                <p class="text-muted">{{ $product->description }} </p>
                                            </div>
                                        </div>

                   

                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                </div><!-- /content -->
            </div><!-- /tabs -->
        </section>
    </div>


</div>

    

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/html5-editor/wysihtml5-0.3.0.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/html5-editor/bootstrap-wysihtml5.js') }}"></script>
   
@endpush

