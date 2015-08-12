@extends('_layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center page-header">
                <h1>Resultat de la recherche</h1>
            </div>
            <div class="col-md-8 col-md-offset-2">
                @if ($searchType == 'shop_item')
                    @include('admin.search.shop')
                @endif
            </div>
        </div>
    </div>
@stop