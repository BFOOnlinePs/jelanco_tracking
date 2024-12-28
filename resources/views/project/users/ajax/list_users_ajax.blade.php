<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th>اسم المستخدم</th>
            <th>الايميل</th>
            <th>رقم الهاتف</th>
            @can('تعديل مستخدم')
                <th>العمليات</th>
            @endcan
        </tr>
    </thead>
    <tbody>
        @if ($data->isEmpty())
            <tr>
                <td colspan="3" class="text-center">لا توجد بيانات</td>
            </tr>
        @else
            @foreach ($data as $key)
                <tr>
                    <td>{{ $key->name }}</td>
                    <td>{{ $key->email }}</td>
                    <td>{{ $key->phone_number }}</td>
                    @can('تعديل مستخدم')
                        <td>
                            <a href="{{ route('users.edit', ['id' => $key->id]) }}" class="btn btn-sm btn-success"><span
                                    class="fa fa-edit"></span></a>
                            @if ($key->user_status == 'active')
                                <a href="{{ route('users.update_user_status',['id'=>$key->id]) }}" class="btn btn-danger btn-sm"><span class="fa fa-lock"></span></a>
                            @elseif ($key->user_status == 'not_active')
                                <a href="{{ route('users.update_user_status',['id'=>$key->id]) }}" class="btn btn-success btn-sm"><span class="fa fa-unlock"></span></a>
                            @endif
                        </td>
                    @endcan
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
