<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th>اسم المستخدم</th>
            <th>كلمة الايميل</th>
            @can('تعديل مستخدم')
                <th>العمليات</th>
            @endcan
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
                    <td>{{ $key->email }}</td>
                    @can('تعديل مستخدم')
                        <td>
                            <a href="{{ route('users.edit',['id'=>$key->id]) }}" class="btn btn-sm btn-success"><span class="fa fa-edit"></span></a>
                        </td>
                    @endcan
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
