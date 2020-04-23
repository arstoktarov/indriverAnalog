@extends('admin.layouts.app', ['title' => 'Изменить тип материала', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Изменить тип материала</h2>
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
                            <form action="{{route('materialTypes.update', $type)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @method('PATCH')
                                <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                <p>Картинка</p>
                                <img width="200" src="{{asset($type->avatar)}}" alt="">
                                <div class="table-responsive">
                                    <div class="form-group form-float p-t-20">
                                        <p>Изменить картинку</p>
                                        <div class="form-line">
                                            <input type="file" class="form-control" id="image" name="image"/>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$type->title}}" type="text" class="form-control" id="title" name="title"/>
                                            <label class="form-label">Название</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea required type="text" class="form-control" id="description" name="description">{{$type->description}}</textarea>
                                            <label class="form-label">Описание</label>
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
