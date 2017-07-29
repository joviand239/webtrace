@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Beta version</div>

                <div class="panel-body text-center">
                    <div id="home">
                        <div class="unauthorized">
                            <h4>Please authorized your wialon account!</h4>
                            <button id="login" class="btn btn-default btn-primary" onclick="getToken()">Click Here</button>
                            <p class="mt-30">Your token:</p>
                            <p id="token"></p>
                        </div>

                        <div class="authorized">
                            <div class="row mt-30">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <button id="create-list" type="button" class="btn btn-default btn-success" onclick="getList()" disabled><i class="fa fa-table"></i>  Create Table</button>
                                    </div>
                                    <div class="col-md-3">
                                        <a id="download" href="#" type="button" class="btn btn-default btn-info" disabled><i class="fa fa-download"></i>  Download</a>
                                    </div>
                                    <div class="col-md-3">
                                       {{-- <button id="send" type="button" class="btn btn-default btn-info" disabled> <i class="fa fa-envelope"></i>  Send Email</button>--}}
                                    </div>
                                    <div class="col-md-3">
                                        <button id="refresh" type="button" class="btn btn-default btn-warning" onclick="refreshPage()"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i>  Refresh Page</button>
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-30">
                                <div class="col-md-12">
                                    <table id="data-table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Time</th>
                                                <th>Unit</th>
                                                <th>Driver</th>
                                                <th>Geofence</th>
                                                <th>Position</th>
                                                <th>Status</th>
                                                <th class="hidden">Muat/Bongkar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unit-list">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-50 hidden">
                            <div class="col-md-12">
                                <div id="location">

                                </div>
                            </div>
                        </div>


                        <div class="row mt-50 hidden">
                            <div class="col-md-12">
                                <div id="log"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js_custom')


@endsection
