@extends('layouts.app')
@section('title')
    اضافة صلاحيات
@endsection
@section('header')
    اضافة صلاحيات
@endsection
@section('header_title_link')
    قائمة الصلاحيات
@endsection
@section('header_title')
    اضافة صلاحيات
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
                <form action="{{ route('permissions.create') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">اسم الصلاحية</label>
                                    <input required type="text" name="name" class="form-control" placeholder="اسم الصلاحية">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="permissions">الصلاحيات</label>

                                <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="form-check">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-success">حفظ</button>
                            </div>
                        </div>
                    </div>
                </form>
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

