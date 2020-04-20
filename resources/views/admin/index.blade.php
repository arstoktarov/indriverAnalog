@extends('admin.layouts.app', ['title' => 'Главная страница', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Главная страница</h2>
            </div>

{{--            <div class="row clearfix">--}}
{{--                @include('admin.components.error')--}}
{{--            </div>--}}
        </div>
    </section>
@endsection

@push('css')
@endpush

@push('js')
@endpush
