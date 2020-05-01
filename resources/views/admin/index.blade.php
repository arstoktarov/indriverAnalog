@extends('admin.layouts.app', ['title' => 'Главная страница', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Главная страница</h2>
            </div>
            <div class="row clearfix">
                <div class="card">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <td>Количество городов - </td>
                                    <td>{{$data['city']}}</td>
                                </tr>
                                <tr>
                                    <td>Количество материалов - </td>
                                    <td>{{$data['mType']}}</td>
                                </tr>
                                <tr>
                                    <td>Количество пользователей - </td>
                                    <td>{{$data['users']}}</td>
                                </tr>
                                <tr>
                                    <td>Количество техник - </td>
                                    <td>{{$data['tType']}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css')
@endpush

@push('js')
@endpush
