@extends('layouts.app')
@section('title')
    تعديل فئة
@endsection
@section('header')
    تعديل فئة
@endsection
@section('header_title_link')
    قائمة الفئات
@endsection
@section('header_title')
    تعديل فئة
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tasks_category.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->c_id }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اسم الفئة</label>
                                            <input type="text" value="{{ $data->c_name }}" class="form-control" placeholder="اسم الفئة" name="name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <span style="font-size: 150px" class="fa fa-list-alt"></span>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-success">تعديل</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

