@if(\Illuminate\Support\Facades\Session::has('success'))
    <p class="alert alert-success">{{ \Illuminate\Support\Facades\Session::get('success') }}</p>
@endif
