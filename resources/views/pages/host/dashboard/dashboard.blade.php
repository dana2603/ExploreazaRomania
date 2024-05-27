{{-- extindem vederea userIndex. Practic ne lipim la ea --}}
@extends('userIndex')
{{-- in sectiunea  @yield('userContent') atasam html din sectiunea @section('userContent') --}}
@section('userContent')
    {{-- in fisierele JS tourist.js si host.js daca cauti #data-row-container o sa observi ca toate datele dinamice ce le primim prin axios le atasam in acest container cu id-ul id="data-row-container" --}}
    <div class="row d-flex justify-content-center statistics" id="data-row-container"></div>
@endsection
<style>
    .statistics{
        margin-top: -50px;
        padding-bottom: 100px;
    }
</style>