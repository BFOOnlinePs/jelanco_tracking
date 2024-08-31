@extends('layouts.app')
@section('title')
    قائمة المهام
@endsection
@section('header')
    قائمة المهام
@endsection
@section('header_title_link')
    الرئيسية
@endsection
@section('header_title')
    قائمة المهام
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
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">الوصف</label>
                                        <textarea name="ts_content" id="" class="form-control" cols="30" rows="2" placeholder="الوصف"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">الوصف</label>
                                        <textarea name="ts_content" id="" class="form-control" cols="30" rows="2" placeholder="الوصف"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center justify-content-center align-content-center">
                            <span class="fa fa-tasks" style="font-size: 150px"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

