@extends('admin.layouts.app', ['title' => 'Список техник', 'active_clients' => 'active'])

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
                                Список техник
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
                                        <th>Модель</th>
                                        <th>Спецификация</th>
                                        <th>Картинка</th>
                                        <th>Категория</th>
                                        <th>Характеристика</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($technics as $technic)
                                        <tr>
                                            <td>{{$technic->id}}</td>
                                            <td>{{$technic->model}}</td>
                                            <td>{{$technic->specification}}</td>
                                            <td><img style="max-height: 200px; max-width: 200px" src="{{asset($technic->image)}}" alt="Картинка статьи"/></td>
                                            <td>{{$technic->category['title']}}</td>
                                            <td>
                                                <form action="{{route('technicCharacteristics.index', $technic->id)}}" style="display:inline-block">
                                                    @csrf
                                                    <input type="hidden" value="{{$technic->id}}" name="id">
                                                    <button type="submit" class="waves-effect btn btn-danger"><i class="material-icons">visibility</i></button>
                                                </form>
                                            </td>
                                            <td>
                                                <a href="{{route('technics.edit', $technic->id)}}" class="waves-effect btn btn-primary"><i class="material-icons">edit</i></a>
                                                <form action="{{route('technics.destroy', $technic->id)}}" method="POST" style="display:inline-block">
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
                            {{$technics->links()}}
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <h5 class="modal-header">
                            Добавить город
                        </h5>
                        <div class="modal-body">
                            <form action="{{route('technics.store')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="form-group form-float p-t-20">
                                    <label class="form-label">Категория</label>
                                    <div class="form-line">
                                        <select class="form-control" name="category_id" id="category_id">
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float p-t-20">
                                    <label class="form-label">Картинка</label>
                                    <div ng-class="form-control" class="form-line">
                                        <input  type="file" class="form-control" id="image" name="image" accept="image/*" />
                                    </div>
                                </div>
                                <div class="form-group form-float p-t-20">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" id="model" name="model"/>
                                        <label class="form-label">Модель</label>
                                    </div>
                                </div>
                                <div class="form-group form-float p-t-20">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" id="specification" name="specification"/>
                                        <label class="form-label">Спецификация</label>
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
