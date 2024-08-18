@extends('layouts.app')
@section('title')
    تعديل مستخدم
@endsection
@section('header')
    تعديل مستخدم
@endsection
@section('header_title_link')
    قائمة المستخدمين
@endsection
@section('header_title')
    تعديل مستخدم
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">اسم المستخدم</label>
                                            <input required type="text" value="{{ $data->name }}" name="name" class="form-control" placeholder="اسم المستخدم">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">الايميل</label>
                                            <input required type="email" value="{{ $data->email }}" name="email" class="form-control" placeholder="الايميل">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">كلمة المرور</label>
                                            <input required type="password" name="password" class="form-control" placeholder="كلمة المرور">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اضافة دور</label>
                                            <select class="form-control" required name="role" id="">
                                                <option value="">اختر دور</option>
                                                @foreach($roles as $key)
                                                    <option value="{{ $key->id }}" {{ in_array($key->name, $userRoles) ? 'selected' : '' }}>
                                                        {{ $key->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <span style="font-size: 150px" class="fa fa-user-plus"></span>
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

