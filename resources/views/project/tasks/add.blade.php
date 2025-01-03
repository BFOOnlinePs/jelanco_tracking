@extends('layouts.app')
@section('title')
    اضافة تكليف
@endsection
@section('header')
    اضافة تكليف
@endsection
@section('header_title_link')
    الرئيسية
@endsection
@section('header_title')
    اضافة تكليف
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
                    <form action="{{ route('tasks.create') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">الموظفين المكلفين</label>
                                            <select required class="form-control select2bs4" name="clients[]" multiple="multiple">
                                                @foreach($clients as $key)
                                                    <option value="{{ $key->id }}">{{ $key->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">محتوى التكليف</label>
                                            <textarea name="t_content" id="" cols="30" placeholder="ادخل محتوى التكليف" class="form-control" rows="3"></textarea>
                                            {{-- <input required type="text" name="t_content" class="form-control" placeholder="ادخل محتوى التكليف"> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">موعد البدء</label>
                                            <input required type="datetime-local" name="t_planed_start_time" class="form-control" placeholder="بداية المهمة">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">موعد الانتهاء</label>
                                            <input required type="datetime-local" name="t_planed_end_time" class="form-control" placeholder="نهاية المهمة">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">التصنيف</label>
                                            <select class="form-control" required name="t_category_id" id="">
                                                <option value="">اختر التصنيف ...</option>
                                                @foreach($task_category as $key)
                                                    <option value="{{ $key->c_id }}">{{ $key->c_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button class="btn btn-success">اضافة تكليف</button>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4 text-center justify-content-center align-content-center">
                                <span style="font-size: 150px" class="fa fa-tasks"></span>
                            </div> --}}
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
