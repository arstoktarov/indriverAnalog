@extends('admin.layouts.app', ['title' => 'Список заказов материал', 'active_clients' => 'active'])

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
                                Список заказов материал
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                {{--                                @include('admin.components.error')--}}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Клиент</th>
                                        <th>Водитель</th>
                                        <th>Цена</th>
                                        <th>Город</th>
                                        <th>Адрес</th>
                                        <th>Описание</th>
                                        <th>Значение материала</th>
                                        <th>Статус</th>
                                        <th>Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{$order->id}}</td>
                                            <td>
                                                @if($order->user_id && $order->user)
                                                    {{$order->user['name'] ?? 'Не выбран'}}<br>
                                                    {{$order->user['phone'] ?? 'Не выбран'}}
                                                @else
                                                    Не выбран
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->executor_id && $order->executor)
                                                    {{$order->executor['name'] ?? 'Не выбран'}}<br>
                                                    {{$order->executor['phone'] ?? 'Не выбран'}}
                                                @else
                                                    Не выбран
                                                @endif
                                            </td>
                                            <td>{{$order->price ?? 'Не указано'}}</td>
                                            <td>{{$order->city['title'] ?? 'Не указано'}}</td>
                                            <td>{{$order->address ?? 'Не указано'}}</td>
                                            <td style="max-width: 200px;">{{$order->description ?? 'Не указано'}}</td>
                                            <td>{{$order->material['charac_value']}}</td>
                                            <td>
                                                @switch($order->status)
                                                    @case(0)
                                                    Не зачат
                                                    @break
                                                    @case(1)
                                                    В процессе
                                                    @break
                                                    @case(3)
                                                    Закончен
                                                    @break
                                                    @case(4)
                                                    Отменен
                                                    @break
                                                    @default
                                                    Нет такого статуса
                                                @endswitch
                                            </td>
                                            <td>
                                                <form action="{{route('materialOrders.destroy', $order->id)}}" method="POST" style="display:inline-block">
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
                            {{$orders->links()}}
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
