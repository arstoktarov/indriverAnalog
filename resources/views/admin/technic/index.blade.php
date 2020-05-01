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
                                        <th>Тип</th>
                                        <th>Значение</th>
{{--                                        <th>Характеристика</th>--}}
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($technics as $technic)
                                        <tr>
                                            <td>{{$technic->id}}</td>
                                            <td>{{$technic->type['title']}}</td>
                                            <td>{{$technic->charac_value}}</td>
{{--                                            <td>--}}
{{--                                                <form action="{{route('technicCharacteristics.index')}}" style="display:inline-block">--}}
{{--                                                    @csrf--}}
{{--                                                    <input type="hidden" value="{{$technic->id}}" name="technic_id">--}}
{{--                                                    <button type="submit" class="waves-effect btn btn-success">Перейти</button>--}}
{{--                                                </form>--}}
{{--                                            </td>--}}
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
                                    <div class="form-line">
                                        <input step="0.1" required type="number" class="form-control" id="charac_value" name="charac_value"/>
                                        <input type="hidden" value="{{$id}}" name="type_id">
                                        <label class="form-label">Значение</label>
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
