@extends('app')
{{--  in app.blade.php avem: @yield('content') --}}
{{-- @section('content') va fi introdus automat de catre Laravel in sectiunea  @yield('content') --}}
@section('content')
{{-- pentru gazda, verificam daca data de expirare a planului actual al gazdei mai mica decat data actual. Practic verificam daca planul a expirat, si aratam un mesaj in partea de sus a pagini cand gazda este autentificat --}}
@if(Auth::guard('host')->check() && ($user->planEndDate < date('Y-m-d')))
<div class="trial-period-expired">
    <p>Valabilitatea planului tău a expirat. Activează-ți unul dintre planurile noastre la acest <a href="/host/account" class="href-account">link</a> pentru ca anunțurile tale să fie vizibile din nou pe site</p>
</div>
@endif
<div class="row main-row all-container">
    <div class="col-2 user-menu-container">
        {{-- verificatm ce tip de user este autentificat. Daca este turist includem meniul lateral pentru turist. Daca este gazda atunci cel ai gazdei --}}
        @if (Auth::guard('tourist')->check())
            @include('partials.sideMenuTourist')
        @elseif(Auth::guard('host')->check())
            @include('partials.sideMenuHost')
        @endif
    </div>
    <div class="col-10 user-content-container">
        {{-- aicea va fi introdus html de pe pagina a userilor autentificati --}}
        @yield('userContent')
    </div>
</div>
@endsection
<style>
    .side-menu-nav{
        margin: 50px 0px 0px 30px;
        border: 1px solid #2a5b3e;
        max-width: 250px;
        box-shadow: -4px 0px 19px 11px rgb(42 91 62 / 27%)
    }
    .side-menu-icon{
        margin: 15px 10px 2px 2px;
    }

    .side-menu-nav-link {
        border-bottom: 1px solid #dfdfdf;
    }
    .nav-item-link{
        margin: 2px 15px 2px 2px;
    }
    .nav-item a{
        color: #2a5b3e;
        cursor: pointer;
    }
    .nav-item a{
        padding: 12px 0px 15px 15px;
    }
    .nav-item a:hover,  .nav-item a.active{
        color: white;
        background-color: #2a5b3e;
    }
    #data-row-container{
        display: none;
    }
    .href-account{
        color: #999999;
    }
    .href-account:hover{
        color: #999999;
    }
</style>