@extends('app')
{{-- extindem principala fundatie app.blade.php --}}
{{--  in app.blade.php avem: @yield('content') --}}
{{-- @section('content') va fi introdus automat de catre Laravel in sectiunea  @yield('content') --}}
@section('content')
    <div class="all-container">
        <div class="search-area-container">
            <div class="empty-row"></div>
            {{-- include un alt fisier blade: practic includem sectiunea de cautare pe pagina --}}
            @include('partials.search')
            <div class="results-container" style="margin: 30px 0px 0px 0px;">
                <div class="row d-flex justify-content-center" id="results" style="display:none; margin:0px;"></div>
            </div>
        </div>
    </div>
@endsection
<style>
     .all-container{
        background-image: url(assets/images/idylic-background.jpg);
        background-size: cover;
     }
</style>