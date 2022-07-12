@if($message = Session::get("success"))
<div class=" btn btn-success">
    {{ $message}}
</div>
@endif

@if($message = Session::get("error"))
<div class=" btn btn-danger">
    {{ $message}}
</div>
@endif


