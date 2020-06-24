@extends('admin.layouts.app', ['title' => 'Список транзакции', 'active_clients' => 'active'])

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
                                Список транзакции
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                {{--                                @include('admin.components.error')--}}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Пользователь</th>
                                        <th>Сумма</th>
                                        <th>Телефон</th>
                                        <th>Email</th>
                                        <th>Статус</th>
                                        <th>Время</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{$transaction->id}}</td>
                                            <td>
                                                {{$transaction->user['name'] ?? 'Не указано'}}<br>
                                                {{$transaction->user['phone'] ?? 'Не указано'}}
                                            </td>
                                            <td>{{$transaction->amount}}</td>
                                            <td>{{$transaction->pg_user_phone ?? 'Не указано'}}</td>
                                            <td>{{$transaction->pg_user_contact_email ?? 'Не указано'}}</td>
                                            <td>{{$transaction->status === 'success' ? 'Успешно' : 'Не успешно'}}</td>
                                            <td>{{$transaction->created_at}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$transactions->links()}}
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
