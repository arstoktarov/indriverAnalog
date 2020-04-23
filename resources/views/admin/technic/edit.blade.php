@extends('admin.layouts.app', ['title' => 'Изменить технику', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Изменить технику</h2>
            </div>

            <div class="row clearfix">
                {{--                @include('admin.components.error')--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Изменить технику
                            </h2>
                        </div>
                        <div class="body">
                            <form action="{{route('technics.update', $technic)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @method('PATCH')
                                <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                <p>Картинка</p>
                                <img width="200" src="{{asset($technic->image)}}" alt="">
                                <div class="table-responsive">
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <p>Изменить картинку</p>
                                            <input type="file" class="form-control" id="image" name="image"/>
                                        </div>
                                    </div>
                                    <div class="form-group form-float p-t-20">
                                        <label class="form-label">Категория</label>
                                        <div class="form-line">
                                            <select class="form-control" name="category_id" id="category_id">
                                                @foreach($categories as $category)
                                                    <option {{$category->id === $technic->category_id}} value="{{$category->id}}">{{$category->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <input value="{{$technic->model}}" required type="text" class="form-control" id="model" name="model"/>
                                            <label class="form-label">Модель</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <input value="{{$technic->specification}}" required type="text" class="form-control" id="specification" name="specification"/>
                                            <label class="form-label">Спецификация</label>
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
