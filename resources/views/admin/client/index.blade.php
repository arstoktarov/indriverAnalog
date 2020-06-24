@extends('admin.layouts.app', ['title' => 'Список пользователей', 'active_clients' => 'active'])

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    <p>{{session()->get('message')}}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    <p>{{session()->get('error')}}</p>
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
                            <p>Поиск</p>
                            <form action="">
                                <input name="phone" type="text" placeholder="Введите номер телефона">
                                <button class="btn btn-secondary">Ок</button>
                            </form>
                            @if(isset($_GET['phone']))
                                <form action="">
                                    <input type="hidden" placeholder="Введите номер телефона">
                                    <button class="btn btn-warning">Очистить поиск</button>
                                </form>
                            @endif
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
                                    <?php
                                        if(isset($_GET['page'])) $i=$_GET['page']*10-9;
                                        else $i=1;
                                    ?>
                                    @foreach($users as $user)
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->phone}}</td>
                                            <td>{{$user->balance}}</td>
                                            <td>{{$user->city['title'] ?? 'Не указан'}}</td>
                                            <td>
                                                <a href="{{route('clients.show', $user->id)}}" class="waves-effect btn btn-primary"><i class="material-icons">visibility</i></a>
                                                <a href="{{route('clients.edit', $user->id)}}" class="waves-effect btn btn-success"><i class="material-icons">edit</i></a>
                                                <form action="{{route('clients.destroy', $user->id)}}" method="POST" style="display:inline-block">
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
                            {{$users->appends(request()->except('page'))->links()}}
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
                                <form action="{{route('clients.store')}}" method="post" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="first_name" name="first_name"/>
                                            <label class="form-label">Имя</label>
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
                                            <input onchange="phones()" required type="text" class="form-control" name="phone" id="element"/>
                                            <input required type="hidden" class="form-control" id="phone" name="phone"/>
                                            <label class="form-label">Телефон</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required type="number" class="form-control" id="balance" name="balance"/>
                                            <label class="form-label">Баланс</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <p class="form-label">Город</p>
                                        <div class="form-line">
                                            <select class="form-control" name="city_id" id="city_id">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->title}}</option>
                                                @endforeach
                                            </select>
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
    <script src="https://unpkg.com/imask"></script>
    <script>
        var element = document.getElementById('element');
        var phone = document.getElementById('phone');
        var maskOptions = {
            mask: '{8} (000) 000-00-00'
        };
        var mask = IMask(element, maskOptions);

        function phones() {
            var element = document.getElementById('element');
            var str = mask.value.split('-').join('').split(' ').join('').split('(').join('').split(')').join('');
            phone.value = str;
        }
    </script>

@endpush
