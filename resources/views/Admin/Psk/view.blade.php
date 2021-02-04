@extends('layouts.template')

@section('title',"View Users")

@section('head')
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{asset('dist/app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('dist/app-assets/vendors/css/extensions/sweetalert.css')}}">

@stop

<!-- content-body -->
@section('content-body')

    <!-- Main -->
    <section id="main">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">PSK</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body card-dashboard">
                            {!! Form::open(['action'=>'PskController@store','method'=>'post', 'id'=>'pskform', 'class'=>'form form-horizontal']) !!}
                            <fieldset class="form-group floating-label-form-group">
                                <label for="title">PSK Network Name</label>
                                {!! Form::text('psk_network_name',Cache::get('psk_network_name'),['id'=>'psk_network_name', 'class'=>'form-control', 'required'=>'true', 'placeholder'=>'Enter PSK Network Name']) !!}
                            </fieldset>
                            <fieldset class="form-group floating-label-form-group">
                                <label for="title">PSK Password</label>
                                {!! Form::text('psk_password',Cache::get('psk_password'),['id'=>'psk_password', 'class'=>'form-control', 'required'=>'true', 'placeholder'=>'Enter PSK Password']) !!}
                            </fieldset>
                            <input type="submit" class="btn btn-outline-primary btn-lg" value="Save">
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Javascript sourced data -->
@stop

@section('js_script')

    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('dist/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/ui/breadcrumbs-with-stats.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/modal/components-modal.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/js/scripts/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('dist/app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

@stop