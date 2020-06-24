@extends('admin.layouts.app', ['title' => 'Изменить пользователя', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Изменить пользователя</h2>
            </div>

            <div class="row clearfix">
                {{--                @include('admin.components.error')--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Изменить пользователя
                            </h2>
                        </div>
                        <div class="body">
                            <form></form>
                            <form action="{{route('users.update', $user)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @method('PATCH')
                                <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                <div class="table-responsive">
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <input required value="{{$user->name}}" type="text" class="form-control" id="name" name="name"/>
                                            <label class="form-label">Имя пользователя</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$user->phone}}" type="text" class="form-control" id="phone" name="phone"/>
                                            <label class="form-label">Номер пользователя</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <p>Город пользователя</p>
                                        <div class="form-line">
                                            <select class="form-control" name="city_id" id="city_id">
                                                @foreach($cities as $city)
                                                    <option {{$city->id === $user->city_id ? 'selected' : ''}} value="{{$city->id}}">{{$city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$user->balance}}" type="text" class="form-control" id="balance" name="balance"/>
                                            <label class="form-label">Баланс пользователя</label>
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
