@extends('layouts.app')
@section('title')
    اضافة فئة
@endsection
@section('header')
    اضافة فئة
@endsection
@section('header_title_link')
    قائمة الفئات
@endsection
@section('header_title')
    اضافة فئة
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tasks_category.create') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اسم الفئة</label>
                                            <input type="text" class="form-control" placeholder="اسم الفئة" name="name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <span style="font-size: 150px" class="fa fa-list-alt"></span>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-success">اضافة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

