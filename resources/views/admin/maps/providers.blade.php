@extends('admin.layouts.app')
@section('page.title') Localización de las modelos @endsection
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
      integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
      crossorigin=""/>

<style>
    #providersMap{
        height: 500px;
    }
</style>
@endpush
@section('content')
<section class="content"> 

    <div id="providersMap"></div>
</section>
@endsection
@push('scripts')
<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
        integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
crossorigin=""></script>
<script>
    var positions = {!! json_encode($positions) !!}
    ;
    var map = L.map('providersMap').setView([19.000, -70.667], 9);
    L.tileLayer('{!! config("custom.maps_tiles") !!}', {
        maxZoom: 18,
    }).addTo(map);

    var markers = {};

    function addMarker(position, user) {
        var marker = null;
        if (markers[user.id] === undefined) {
            var divIcon = L.divIcon({html: '<div class="map-marker"><img src="' + user.xsmall_avatar_url +'" /></div>', iconSize: [30, 36], iconAnchor: [15, 35]});
            marker = L.marker([position.latitude, position.longitude], {icon: divIcon}).bindPopup(user.name);
            marker.addTo(map);
            markers[user.id] = marker;
        }
        marker = markers[user.id];
        marker.setLatLng(new L.LatLng(position.latitude, position.longitude));
    }

    $(document).ready(function () {
        EchoClient.private('admin.positions')
                .listen('.positionChanged', function (position) {
                    addMarker(position.position, position.user);
                }).listenForWhisper('.positionChanged', function (position) {
            addMarker(position.position, position.user);
        });

        Object.keys(positions).map(function (i) {
            var p = positions[i];
            addMarker(p.position, p.user);
        });

    })


</script>
@endpush