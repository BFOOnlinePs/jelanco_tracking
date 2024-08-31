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
            @include('message_alert.success_message')
            @include('message_alert.fail_message')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('permissions.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $role->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">اسم الصلاحية</label>
                                    <input type="text" name="name" value="{{ $role->name }}" class="form-control" placeholder="اسم الصلاحية">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col">
                                    <label for="permissions">Permissions</label>
                                    <div class="form-check">
                                        @foreach($permissions as $permission)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $assignedPermissions)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
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

