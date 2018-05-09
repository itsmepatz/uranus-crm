@extends('layout.portal')

@section('title', $title)

@section('load_css')
@parent
        <link href="{{ base_url('plugins/bower_components/morrisjs/morris.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('load_js')
@parent
        <!--Morris JavaScript -->
        <script src="{{ base_url('plugins/bower_components/raphael/raphael-min.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/morrisjs/morris.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

        <script src="{{ base_url('js/module/portal/chart.js') }}" type="text/javascript"></script>
@endsection

@section('content')
                <?php
                $show_list_cs = FALSE;
                $size_side_left = 'col-md-0';
                $size_side_right = 'col-md-12';

                if(in_array($role_active->role_id, [1,2,6]))
                {
                    $show_list_cs = TRUE;
                    $size_side_left = 'col-md-4';
                    $size_side_right = 'col-md-8';
                }
                ?>

                <div class="row">
                    @if($show_list_cs)
                    <div class="{{ $size_side_left }} col-sm-12 col-xs-12">
                        <div class="white-box">
                            <h3>List CS</h3>
                        </div>
                        <hr>
                        <div class="white-box cs-list">
                        @foreach ($cs as $key => $value)
                            <h4 onclick="setCS_User_id_and_load({{ $value->user_id }}, '{{ $value->first_name.' '.$value->last_name }}')">{{ $value->first_name.' '.$value->last_name }}</h4>
                        @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="{{ $size_side_right }} col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-md-2 pull-right">
                                <button id="btnFilter" class="btn btn-rounded form-control" onclick="loadDataCS()">
                                    <i class="fa fa-search"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                            <div class="col-md-4 pull-right">
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control" name="start" value="{{ date('Y-m-01') }}">
                                    <span class="input-group-addon bg-info b-0 text-white">to</span>
                                    <input type="text" class="form-control" name="end" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <br>
                        <script type="text/javascript">
                            portal = {
                                cs_user_id: {{ $profile['user_id'] }}
                            };
                        </script>

                        <div class="white-box">
                            <h3>Statistik CS: <b id="fieldCsName">{{ $profile['first_name'].' '.$profile['last_name'] }}</b></h3>
                            <hr>
                            <ul class="list-inline text-right" id="statusSection">
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #00C3ED;"></i>
                                        <input type="checkbox" name="status" colors="#00C3ED" keys="total_fu" labels="Follow Up">
                                        Follow Up
                                    </h5>
                                </li>
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #004471;"></i>
                                        <input type="checkbox" name="status" colors="#004471" keys="total_pending" labels="Pending">
                                        Pending
                                    </h5>
                                </li>
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #E80094;"></i>
                                        <input type="checkbox" name="status" colors="#E80094" keys="total_cancel" labels="Cancel">
                                        Cancel
                                    </h5>
                                </li>
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #FF7A01;"></i>
                                        <input type="checkbox" name="status" colors="#FF7A01" keys="total_confirm" labels="Confirm Buy">
                                        Confirm Buy
                                    </h5>
                                </li>
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #FED700;"></i>
                                        <input type="checkbox" name="status" colors="#FED700" keys="total_verify" labels="Verify Pay">
                                        Verify Pay
                                    </h5>
                                </li>
                                <li>
                                    <h5>
                                        <i class="fa fa-circle m-r-5" style="color: #73B700;"></i>
                                        <input type="checkbox" name="status" colors="#73B700" keys="total_sale" labels="Sale" checked>
                                        Sale
                                    </h5>
                                </li>
                            </ul>
                            <div id="morris-area-chart"></div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
@foreach ($list_module as $key => $module)
                    <div class="col-lg-4">
                        <div class="well panel-primary" style="cursor: pointer;"
                            onclick="window.location = '{{ base_url($module['module_link']) }}'">
                            <h1>{{ $module['module_name'] }}</h1>
                        </div>
                    </div>
@endforeach
                </div>

@endsection
