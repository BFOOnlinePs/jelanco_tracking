@if(\Illuminate\Support\Facades\Session::has('fail'))
    <p class="alert alert-danger">{{ \Illuminate\Support\Facades\Session::get('fail') }}</p>
@endif
