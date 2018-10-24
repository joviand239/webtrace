@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Version 1.1</div>

                <div class="panel-body text-center">

                    <div class="row">
                        <div class="col-md-4">
                            <div id="controls"></div>
                        </div>
                        <div class="col-md-8">
                            <div id="gmap-menu"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jsCustom')
    <script>

        $(document).ready(function () {

            var LocsA = [
                {
                    lat: 45.9,
                    lon: 10.9,
                    title: 'B 9706 UXR - Bagong',
                    html: [
                        '<h5>B 9706 UXR - Bagong</h5>',
                        '<p>Jl. Parang Tritis Raya Blok EA-EB No. 1 Ancol Pademangan Jakarta Utara DKI Jakarta, RT.4/RW.2, Kota Tua, Ancol, Pademangan, Kota Jkt Utara, Daerah Khusus Ibukota Jakarta 14430, Indonesia</p>'
                    ].join(''),
                    icon: 'http://maps.google.com/mapfiles/marker.png',
                    zoom: 8,
                    animation: google.maps.Animation.DROP
                },
                {
                    lat: -6.131,
                    lon: 106.821,
                    title: 'B 9408 UYX - Ali ',
                    html: [
                        '<h5>B 9408 UYX - Ali</h5>',
                        '<p>Jl. Parang Tritis Raya Blok EA-EB No. 1 Ancol Pademangan Jakarta Utara DKI Jakarta, RT.4/RW.2, Kota Tua, Ancol, Pademangan, Kota Jkt Utara, Daerah Khusus Ibukota Jakarta 14430, Indonesia</p>'
                    ].join(''),
                    icon: 'http://maps.google.com/mapfiles/marker.png',
                    zoom: 14,
                    animation: google.maps.Animation.DROP
                },
                {
                    lat: 51.5,
                    lon: -1.1,
                    title: 'B 9707 UXR - BUDI',
                    html: [
                        '<h5>B 9707 UXR - BUDI</h5>',
                        '<p>Jl. Parang Tritis Raya Blok EA-EB No. 1 Ancol Pademangan Jakarta Utara DKI Jakarta, RT.4/RW.2, Kota Tua, Ancol, Pademangan, Kota Jkt Utara, Daerah Khusus Ibukota Jakarta 14430, Indonesia</p>'
                    ].join(''),
                    zoom: 8,
                    icon: 'http://maps.google.com/mapfiles/marker.png',
                    animation: google.maps.Animation.DROP
                }
            ];

            new Maplace({
                locations: LocsA,
                map_div: '#gmap-menu',
                controls_type: 'list',
                controls_on_map: false
            }).Load();

        })
    </script>
@endsection