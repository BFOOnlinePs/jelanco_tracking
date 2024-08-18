@extends('layouts.app')
@section('title')
    قائمة المستخدمين
@endsection
@section('header')
    قائمة المستخدمين
@endsection
@section('header_title_link')
    الرئيسية
@endsection
@section('header_title')
    قائمة المستخدمين
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
                    <a href="{{ route('users.add') }}" class="btn btn-dark">اضافة مستخدم</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="list_users_table"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            list_users_ajax();
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
                    data:{

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

