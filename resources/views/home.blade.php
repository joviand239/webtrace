@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Version 1.1</div>

                <div class="panel-body text-center">
                    <table id="mms-table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Time</th>
                            <th>Unit</th>
                            <th>Geofence</th>
                            <th>Status</th>
                            <th>Muat/Bongkar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(@$units as $index => $item)
                            <tr>
                                <td>{!! @$index+1 !!}.</td>
                                <td>{!! @$item->datetime !!}</td>
                                <td>{!! @$item->plate !!}</td>
                                <td>{!! @$item->geofence !!}</td>
                                <td>{!! @$item->status !!}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jsCustom')
    <script>
        $(document).ready(function () {
            $('#mms-table').DataTable({
                "iDisplayLength": 25,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        title: 'MMS Data Export'
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'MMS Data Export'
                    },
                ]
            });
        })
    </script>
@endsection
