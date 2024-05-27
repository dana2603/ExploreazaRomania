@extends('userIndex')
{{-- in fisierele JS host.js si tourist.js daca cauti #data-row-container o sa observi ca toate datele dinamice ce le primim prin axios le atasam in acest container cu id-ul id="data-row-container" --}}
@section('userContent')
    <div class="row d-flex justify-content-center" id="data-row-container"></div>
@endsection