@extends('admin.layouts.app', ['title' => 'Просмотр пользователя', 'active_clients' => 'active'])

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    <p>{{session()->get('message')}}</p>
                </div>
            @endif
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Просмотр пользователя № {{$user->id}}
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td>Имя пользователя - </td>
                                        <td>{{$user->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Номер пользователя</td>
                                        <td>{{$user->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td>Баланс пользователя</td>
                                        <td>{{$user->balance}}</td>
                                    </tr>
                                    <tr>
                                        <td>Город пользователя</td>
                                        <td>{{$user->city['title']}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <p><strong>Транзакции пользователя</strong></p>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Пользователь</th>
                                        <th>Сумма</th>
                                        <th>Описание</th>
                                        <th>Телефон</th>
                                        <th>Email</th>
                                        <th>Статус</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{$transaction->id}}</td>
                                            <td>{{$transaction->user['name'] ?? 'Не указано'}}</td>
                                            <td>{{$transaction->amount}}</td>
                                            <td>{{$transaction->description ?? 'Не указано'}}</td>
                                            <td>{{$transaction->pg_user_phone ?? 'Не указано'}}</td>
                                            <td>{{$transaction->pg_user_contact_email ?? 'Не указано'}}</td>
                                            <td>{{$transaction->status === 'success' ? 'Успешно' : 'Не успешно'}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$transactions->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>
@endsection

@push('css')
    <!-- Bootstrap Select Css -->
    <link href="{{asset('admin-vendor/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />
@endpush

@push('js')
    <!-- Select Plugin Js -->
    <script src="{{asset('admin-vendor/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>

@endpush
