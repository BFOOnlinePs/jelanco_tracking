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
            <div class="card">
                <div class="card-body">
                        <a href="{{ route('permissions.add') }}" class="btn btn-dark">اضافة صلاحية</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم الدور</th>
                                            <th>الصلاحيات</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($data->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">لا توجد بيانات</td>
                                            </tr>
                                        @else
                                            @foreach($data as $key)
                                                <tr>
                                                    <td>{{ $key->name }}</td>
                                                    <td>
                                                        @foreach($key->permissions as $permission)
                                                            <span class="badge badge-warning">{{ $permission->name }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('permissions.edit',['id'=>$key->id]) }}" class="btn btn-sm btn-success"><span class="fa fa-edit"></span></a>
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
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {

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

