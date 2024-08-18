@extends('layouts.app')
@section('title')
    فئات المهام
@endsection
@section('header')
    فئات المهام
@endsection
@section('header_title_link')
    الرئيسية
@endsection
@section('header_title')
    فئات المهام
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('message_alert.success_message')
            @include('message_alert.fail_message')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('tasks_category.add') }}" class="btn btn-dark">اضافة فئة</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>الفئة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->isEmpty())
                                    <tr>
                                        <td colspan="2" class="text-center">لا توجد بيانات</td>
                                    </tr>
                                @else
                                    @foreach($data as $key)
                                        <tr>
                                            <td>{{ $key->c_name }}</td>
                                            <td>
                                                <a href="{{ route('tasks_category.edit',['id'=>$key->c_id]) }}" class="btn btn-success btn-sm"><span class="fa fa-edit"></span></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

