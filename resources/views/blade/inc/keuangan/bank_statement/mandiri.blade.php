@extends('layout.default')

@section('title', $title)

@section('load_css')
@parent
        <link href="{{ base_url('plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ base_url('plugins/bower_components/datatables-bootstrap/Buttons-1.5.1/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ base_url('plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ base_url('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('load_js')
@parent
        <script src="{{ base_url('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/datatables-bootstrap/Buttons-1.5.1/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/datatables-bootstrap/Buttons-1.5.1/js/buttons.flash.min.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/blockUI/jquery.blockUI.js') }}"></script>
        <!-- Sweet-Alert  -->
        <script src="{{ base_url('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
        <script src="{{ base_url('plugins/bower_components/sweetalert/jquery.sweet-alert.custom.js')}}"></script>
        <script src="{{ base_url('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ base_url('plugins/table-dragger/dist/table-dragger.min.js') }}"></script>

        <script src="{{ base_url('js/validator.js') }}"></script>
        <script src="{{ base_url('js/module/keuangan/bank_statement.js') }}" type="text/javascript"></script>
@endsection

@section('header')
@include('main-inc.default.top_navigation')
@include('main-inc.default.keuangan_sidebar')
@endsection

@section('content')

            <div class="row white-box">
                <div class="col-sm-4">
                    <img src="{{ base_url('images/bank/icon-mandiri.png') }}" width="200">
                </div>
                <div class="col-sm-8">
                    <h1>Mandiri Bank Statement</h1>
                </div>
            </div>

            <script type="text/javascript">
                document.app.uri_bank = 'mandiri';
            </script>

            <div class="row white-box" id="filterSection">
                <div class="col-md-1">
                    <b>Trx Date</b>
                </div>
                <div class="col-md-3">
                    <div class="input-daterange input-group" id="date-range">
                        <input type="text" class="form-control" name="start" value="{{ date('Y-m-01') }}">
                        <span class="input-group-addon bg-info b-0 text-white">to</span>
                        <input type="text" class="form-control" name="end" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-rounded form-control" onclick="loadBankStatement()">
                        <i class="fa fa-search"></i>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <div class="row white-box">
                <div class="col-md-2 pull-right">
                    <button class="btn form-control btn-danger" onclick="fixBalance(4)">
                        <i class="fa fa-sort-amount-asc"></i>
                        <span>Fix Balance</span>
                    </button>
                </div>

                <div class="col-md-2 pull-right">
                    <button class="btn form-control btn-warning" onclick="fixSequence(4)">
                        <i class="fa fa-sort-amount-asc"></i>
                        <span>Fix Sequence</span>
                    </button>
                </div>
            </div>

            <!-- .row -->
            <div class="row">
                <div class="table-responsive manage-table">
                    <table class="table" id="sortableStatement">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Trx Date</th>
                                <th>Invoice</th>
                                <th>Note</th>
                                <th>Debit (-)</th>
                                <th>Credit (+)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
@endsection
