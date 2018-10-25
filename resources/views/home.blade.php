@extends('layouts.app')

@section('content')

    <div class="layout-wrapper">
        <div class="control-wrapper">
            <div id="controls"></div>
        </div>
        <div class="map-wrapper">
            <div id="gmap-menu"></div>
        </div>
    </div>
@endsection

@section('jsCustom')
    <script>

        var units = {!! json_encode(@$units) !!};

        $(document).ready(function () {

            createMapMarker(units);

        });

        function createMapMarker(units) {

            console.log(units);

            var locations = [];

            units.forEach(function (unit) {

                var tempData = {
                    lat: unit.latitude,
                    lon: unit.longitude,
                    title: unit.name,
                    html: [
                        '<h5>'+unit.name+'</h5>',
                        '<p>'+unit.address+'</p>'
                    ].join(''),
                    icon: 'http://maps.google.com/mapfiles/marker.png',
                    zoom: 14,
                    animation: google.maps.Animation.DROP
                }


                locations.push(tempData);
            })

            new Maplace({
                locations: locations,
                map_div: '#gmap-menu',
                controls_type: 'list',
                controls_on_map: false
            }).Load();

        }




    </script>
@endsection