@extends('admin.layouts.app', ['title' => 'Список пользователей', 'active_clients' => 'active'])

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
                                Список пользователей
                            </h2>
                            <button title="Добавить статью" type="button" data-toggle="modal" data-target="#defaultModal" class="btn btn-danger btn-circle waves-effect waves-circle waves-float waves-effect m-t--30 pull-right">
                                <i class="material-icons m-t-5">add</i>
                            </button>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                {{--                                @include('admin.components.error')--}}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Имя</th>
                                        <th>Телефон</th>
                                        <th>Баланс</th>
                                        <th>Город</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->phone}}</td>
                                            <td>{{$user->balance}}</td>
                                            <td>{{$user->city['title']}}</td>
                                            <td>
                                                <a href="{{route('users.edit', $user->id)}}" class="waves-effect btn btn-primary"><i class="material-icons">edit</i></a>
                                                <form action="{{route('users.destroy', $user->id)}}" method="POST" style="display:inline-block">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="submit" class="waves-effect btn btn-danger"><i class="material-icons">delete</i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$users->links()}}
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <h5 class="modal-header">
                            Добавить статью или новость
                        </h5>
                        <div class="modal-body">
                            <form action="{{route('users.store')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="p-b-20">
                                    <label class="form-label">Пол</label>
                                    <select class="form-control show-tick" id="gender" name="gender">
                                        <option value="1">Мужской</option>
                                        <option value="0">Женский</option>
                                    </select>
                                </div>
                                {{--                                    <div id="authors" class="p-t-20">--}}
                                {{--                                        <select class="form-control show-tick" id="author_id" name="author_id">--}}
                                {{--                                            @foreach ($authors as $author)--}}
                                {{--                                                <option value="{{$author->id}}">{{$author->name}} {{$author->last_name}}</option>--}}
                                {{--                                            @endforeach--}}
                                {{--                                        </select>--}}
                                {{--                                    </div>--}}
                                <div class="form-group form-float p-t-20">
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="first_name" name="first_name"/>
                                        <label class="form-label">Имя</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="last_name" name="last_name"/>
                                        <label class="form-label">Фамилия</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="surname" name="surname"/>
                                        <label class="form-label">Отчество</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="email" class="form-control" id="email" name="email"/>
                                        <label class="form-label">E-mail</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="password" class="form-control" id="password" name="password"/>
                                        <label class="form-label">Пароль</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input required type="number" class="form-control" id="phone" name="phone"/>
                                        <label class="form-label">Телефон</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label>День рождения</label>
                                        <input required type="date" class="form-control" id="birthday" name="birthday"/>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-link waves-effect">Добавить</button>
                                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Отмена</button>
                                </div>
                            </form>
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
