@extends('layouts.app')
@section('title')
    اضافة مستخدم
@endsection
@section('header')
    اضافة مستخدم
@endsection
@section('header_title_link')
    قائمة المستخدمين
@endsection
@section('header_title')
    اضافة مستخدم
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.create') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اسم المستخدم</label>
                                            <input required type="text" name="name" class="form-control"
                                                placeholder="اسم المستخدم">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">الايميل</label>
                                            <input required type="email" name="email" class="form-control"
                                                placeholder="الايميل">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">رقم الهاتف</label>
                                            <input required type="number" name="phone_number" class="form-control"
                                                placeholder="رقم الهاتف">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">كلمة المرور</label>
                                            <input required type="password" name="password" class="form-control"
                                                placeholder="كلمة المرور">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">اضافة دور</label>
                                            <select class="form-control" required name="role" id="">
                                                <option value="">اختر دور</option>
                                                @foreach ($roles as $key)
                                                    <option value="{{ $key->id }}">{{ $key->name }}</option>
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
                                <button class="btn btn-success">اضافة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

        });

        function list_users_ajax() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('users.list_users_ajax') }}',
                type: 'post',
                dataType: 'json',
                data: {

                },
                success: function(response) {
                    $('#list_users_table').html(response.view);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>
@endsection
