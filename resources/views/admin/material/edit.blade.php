@extends('admin.layouts.app', ['title' => 'Изменить материал', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Изменить материал</h2>
            </div>

            <div class="row clearfix">
                {{--                @include('admin.components.error')--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Изменить тип материала
                            </h2>
                        </div>
                        <div class="body">
                            <form></form>
                            <form action="{{route('materials.update', $material)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @method('PATCH')
                                <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                <p>Картинка</p>
                                <img width="200" src="{{asset($material->avatar)}}" alt="">
                                <div class="table-responsive">
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <p>Изменить картинку</p>
                                            <input type="file" class="form-control" id="image" name="image"/>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$material->title}}" type="text" class="form-control" id="title" name="title"/>
                                            <label class="form-label">Название</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$material->description}}" type="text" class="form-control" id="description" name="description"/>
                                            <label class="form-label">Описание</label>
                                        </div>
                                    </div>
                                    <label class="form-label">Тип материала</label>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="type_id" id="type_id">
                                                @foreach($types as $type)
                                                    <option {{$type->id === $material->type_id ? 'selected' : ''}} value="{{$type->id}}">{{$type->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$material->brand}}" type="text" class="form-control" id="brand" name="brand"/>
                                            <label class="form-label">Бренд</label>
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
