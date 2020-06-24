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
{{--                                        <tr>--}}
{{--                                            <td>Техника пользователя</td>--}}
{{--                                            <td>--}}
{{--                                                @foreach($user->technics as $technic)--}}
{{--                                                    {{$technic['charac_value']}}--}}
{{--                                                @endforeach--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td>Материалы пользователя</td>--}}
{{--                                            <td>--}}
{{--                                                @foreach($user->userTechnic as $technic)--}}
{{--                                                    <div class="row">--}}
{{--                                                        {{$technic}}--}}
{{--                                                    </div>--}}
{{--                                                @endforeach--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
                                    </tbody>
                                </table>
                                <p><strong>Техники пользователя</strong></p>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Картинки</th>
                                        <th>Название техники</th>
                                        <th>Описание</th>
                                        <th>Модель</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user->userTechnic as $technic)
                                        <tr>
                                            <td>{{$technic->id}}</td>
                                            <td><img width="200" src="{{asset($technic->image)}}" alt=""></td>
                                            <td>{{$technic->technic['type']['title']}}</td>
                                            <td>{{$technic->description ?? 'Не указан'}}</td>
                                            <td>{{$technic->model ?? 'Не указан'}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <p><strong>Материалы пользователя</strong></p>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Картинки</th>
                                        <th>Название материала</th>
                                        <th>Описание</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user->userMaterials as $material)
                                        <tr>
                                            <td>{{$material->id}}</td>
                                            <td><img width="200" src="{{asset($material->image)}}" alt=""></td>
                                            <td>{{$material->material['type']['title']}}</td>
                                            <td>{{$material->description ?? 'Не указан'}}</td>
                                        </tr>
                                    @endforeach
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
{{--                                <form action="{{route('users.update', $user)}}" method="post" enctype="multipart/form-data">--}}
{{--                                    {{csrf_field()}}--}}
{{--                                    @method('PATCH')--}}
{{--                                    <input type="hidden" name="redirects_to" value="{{URL::previous()}}">--}}
{{--                                    <div class="table-responsive">--}}
{{--                                        <div class="form-group form-float p-t-20">--}}
{{--                                            <div class="form-line">--}}
{{--                                                <p>Изменить баланс (не обязательно)</p>--}}
{{--                                                <input required value="{{$user->balance}}" type="text" class="form-control" id="balance" name="balance"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <button type="submit" class="btn btn-link waves-effect">Изменить</button>--}}
{{--                                        <button type="button" onclick="location.href='{{URL::previous()}}'" class="btn btn-link waves-effect">Отмена</button>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
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
