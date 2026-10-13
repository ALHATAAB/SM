@if ($message=Session::get('success'))
<div class="alert alert-success" role="alert">
    {{ $message }}
</div>
@endif
@if ($message=Session::get('danger'))
<div class="alert alert-danger" role="alert">
    {{ $message }}
</div>
@endif
@if ($message=Session::get('warning'))
<div class="alert alert-warning" role="alert">
    {{ $message }}
</div>
@endif
@if ($message=Session::get('info'))
<div class="alert alert-info" role="alert">
    {{ $message }}
</div>
@endif
