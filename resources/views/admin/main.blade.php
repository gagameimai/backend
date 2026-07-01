@extends('admin.layout.master')

@section('unit.main', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="main" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak></div>
    </div>
@endsection
