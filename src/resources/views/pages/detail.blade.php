@extends('template.master')

@section('title', 'Detail - '.ucwords($object_type))

@section('head')
<script>
    var detailData = {!! json_encode($data) !!};
    var rekomendasi = {!! json_encode($rekomendasi) !!};
</script>
@endsection

@section('banner')
    <div class="banner-full"
        style="background: linear-gradient(to bottom, hsla(0, 0%, 30.2%, 0.7), hsla(0, 0%, 30.2%, 0.7)), url({{ asset('img/banner.png') }}) no-repeat center center / cover;">
        <div>
            <p class="banner-full__header">{{ $data->nama }}</p>
            <p>{{ $data->alamat }}</p>
            <p> {{ $data->no_telp }}</p>
            <div class="recommend__rating" style="display: flex; gap: 8px; justify-items: center;">
                <div class="recommend__star">★</div>
                <div style="color: white; font-weight: 700;">({{ floatval($data->rating) }} / 5.0)</div>
            </div>
        </div>

    </div>
    <div style="position: absolute; padding: 30px 80px; top: 0px; width: 100%" class="notHome">
        @include('template.navbar')
    </div>

@endsection

@section('content')
    <div class="map__detail" id="map"></div>
    <p class="recommend__text">
        @if ($object_type == 'sarana olahraga')
            Rekomendasi Tempat Olahraga Lainnya
        @else
            Rekomendasi Tempat Wisata Lainnya
        @endif
    </p>
    <div class="recommend detail">
        @foreach ($rekomendasi as $item)
        <div class="d-flex mb-5 p-1 card__recommend "onmousemove="popMarker({{ $item->latitude }},{{ $item->longitude }},'{{ $item->asset_name }}')"
            onmouseout="setTimeout( clearMarker(),3000)">
            <div class="recommend__img"
                style="background: url({{ $item->asset_link }}) no-repeat center center / cover;">
            </div>

            <div class="recommend__info">
                <p class="recommend__header">{{ $item->nama }}</p>
                <p id="jarak{{$item->id}}"></p>
                <div class="recommend__rating">
                    {{ $item->rating }}

                    @for ($i = 1; $i <= $item->rating; $i++)
                        @if ($i <= $item->rating)
                            <div class="recommend__star">★</div>
                        @else
                            <div class="recommend__star nofill">☆</div>
                        @endif
                    @endfor
                </div>

                @if ($object_type == 'sarana olahraga')
                    <a class="recommend__link" href="{{ route('olahraga.show', $item->id) }}">Lihat
                        Selengkapnya</a>
                @else
                    <a class="recommend__link" href="{{ route('wisata.show', $item->id) }}">Lihat
                        Selengkapnya</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/map.js') }}"></script>
    <script>
        L.Routing.control({
            waypoints: [
                L.latLng(-5.361098,105.291406),
                L.latLng(detailData.latitude, detailData.longitude)
            ]
        }).addTo(map);
        circle.remove();
        function cekJarak(lat,lng,x){
            let destination = L.latLng(lat,lng)
            let wp2 = new L.Routing.Waypoint(destination);
            let routeUS = new L.Routing.osrmv1();
            routeUS.route([wp1,wp2],(err,obj)=>{
                if(!err){
                    var jarak = obj[0].summary.totalDistance ;
                    document.getElementById('jarak'+x).innerHTML = "Jarak " + (jarak/1000).toFixed(1) + " km";
                }
            })
        }
        rekomendasi.forEach(item => {
            cekJarak(item.latitude,item.longitude,item.id)
        });
    </script>
@endsection