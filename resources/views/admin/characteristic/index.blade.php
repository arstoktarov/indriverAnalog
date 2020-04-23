@extends('admin.layouts.app', ['title' => 'Список характеристик', 'active_clients' => 'active'])

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
                                Список характеристик
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
                                        <th>Название</th>
                                        <th>Тип</th>
                                        <th>Значение</th>
                                        <th>Единица измерения</th>
                                        <th>Дейсвия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($characteristics as $characteristic)
                                        <tr>
                                            <td>{{$characteristic->id}}</td>
                                            <td>{{$characteristic->title}}</td>
                                            <td>{{$characteristic->type['title']}}</td>
                                            <td>{{$characteristic->value}}</td>
                                            <td>{{$characteristic->unit}}</td>
                                            <td>
                                                <a href="{{route('technicCharacteristics.edit', $characteristic->id)}}" class="waves-effect btn btn-primary"><i class="material-icons">edit</i></a>
                                                <form action="{{route('technicCharacteristics.destroy', $characteristic->id)}}" method="POST" style="display:inline-block">
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
                            {{$characteristics->links()}}
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
                            <form action="{{route('technicCharacteristics.store')}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input type="hidden" value="{{$id}}" name="technic_id">
                                <div class="form-group form-float p-t-20">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" id="title" name="title"/>
                                        <label class="form-label">Название</label>
                                    </div>
                                </div>
                                <label class="form-label">Тип</label>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="type_id" id="type_id">
                                            @foreach($types as $type)
                                                <option value="{{$type->id}}">{{$type->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input required type="number" class="form-control" id="value" name="value"/>
                                        <label class="form-label">Значение</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" id="unit" name="unit"/>
                                        <label class="form-label">Единица измерения</label>
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
