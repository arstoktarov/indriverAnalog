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
                                    <tr>
                                        <td>Техника пользователя</td>
                                        <td>
                                            @foreach($user->technics as $technic)
                                                {{$technic['charac_value']}}
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Материалы пользователя</td>
                                        <td>
                                            @foreach($user->materials as $material)
                                                <div class="row">
                                                    <div>{{$material['charac_title']}} - {{$material['charac_value']}} {{$material['charac_unit']}}</div>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <form action="{{route('clients.update', $user)}}" method="post" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    @method('PATCH')
                                    <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                    <div class="table-responsive">
                                        <div class="form-group form-float p-t-20">
                                            <div class="form-line">
                                                <p>Изменить баланс (не обязательно)</p>
                                                <input required value="{{$user->balance}}" type="text" class="form-control" id="balance" name="balance"/>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-link waves-effect">Изменить</button>
                                        <button type="button" onclick="location.href='{{URL::previous()}}'" class="btn btn-link waves-effect">Отмена</button>
                                    </div>
                                </form>
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
