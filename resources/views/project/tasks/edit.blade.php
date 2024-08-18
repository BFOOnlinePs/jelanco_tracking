@extends('layouts.app')
@section('title')
    تعديل مهمة
@endsection
@section('header')
    تعديل مهمة
@endsection
@section('header_title_link')
    قائمة المهمات
@endsection
@section('header_title')
    تعديل مهمة
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tasks.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->t_id }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اسم المهمة</label>
                                            <input required type="text" value="{{ $data->t_content }}" name="t_content" class="form-control" placeholder="اسم المهمة">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">بداية المهمة</label>
                                            <input required type="datetime-local" value="{{ $data->t_planed_start_time }}" name="t_planed_start_time" class="form-control" placeholder="بداية المهمة">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">نهاية المهمة</label>
                                            <input required type="datetime-local" value="{{ $data->t_planed_end_time }}" name="t_planed_end_time" class="form-control" placeholder="نهاية المهمة">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">الفئة</label>
                                            <select class="form-control" required name="t_category_id" id="">
                                                <option value="">اختر الفئة ...</option>
                                                @foreach($task_category as $key)
                                                    <option @if(($key->c_id) == $data->t_category_id) selected @endif value="{{ $key->c_id }}">{{ $key->c_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">حالة المهمة</label>
                                            <select class="form-control" required name="t_status" id="">
                                                <option value="">اختر الحالة ...</option>
                                                <option @if($data->t_status == 'active') selected @endif value="active">مفعل</option>
                                                <option @if($data->t_status == 'notActive') selected @endif value="notActive">غير مفعل</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">الموظفين</label>
                                            <select required class="form-control select2bs4" name="clients[]" multiple="multiple">
                                                @foreach($clients as $key)
                                                    <option @if(in_array($key->id,json_decode($data->t_assigned_to))) selected @endif value="{{ $key->id }}">{{ $key->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button class="btn btn-success">تعديل</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center justify-content-center align-content-center">
                                <span style="font-size: 150px" class="fa fa-tasks"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        })
    </script>
@endsection
