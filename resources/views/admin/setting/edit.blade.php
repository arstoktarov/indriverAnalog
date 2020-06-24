@extends('admin.layouts.app', ['title' => 'Изменить настройку', 'active_index' => 'active'])
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Изменить настройку</h2>
            </div>

            <div class="row clearfix">
                {{--                @include('admin.components.error')--}}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Изменить настройку
                            </h2>
                        </div>
                        <div class="body">
                            <form></form>
                            <form action="{{route('settings.update', $setting)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @method('PATCH')
                                <input type="hidden" name="redirects_to" value="{{URL::previous()}}">
                                <div class="table-responsive">
                                    <div class="form-group form-float p-t-20">
                                        <div class="form-line">
                                            <input required value="{{$setting->keyword_title}}" type="text" class="form-control" id="title" name="title"/>
                                            <label class="form-label">Название</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input required value="{{$setting->value}}" type="text" class="form-control" id="value" name="value"/>
                                            <label class="form-label">Значение</label>
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
