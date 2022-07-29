@extends('layouts.app')

@section('content')

<section class="">
    <!--quit-->
    <div class="container ">
        <!--quit-->
        <div class="row ">
            <div class="card " style="border-style: solid;">
                <div class="card-header mx-auto ">
                    EMAIL TRANSFER MAILCHIMP TO ACTIVE TRAIL
                </div>
                <div class="card-body">
                    <div class="col-md-12 mx-auto text-center ">
                        <form action="{!!route('transfer.mailchimpToactivetrail')!!}" method="post" enctype="multipart/form-data" class="row g-3">
                            @csrf
                            <div class="col-3">
                                <label for="inputAddress" class="form-label ">Account: origin </label>
                                <select class="form-select" name="origin" id="origin_id" onclick="origin_destinate()" aria-label="Default select example">
                                    <option value="" selected>Select one account</option>
                                    <option value="mailchimp">Mailchimp</option>
                                    <option value="active_trail">Active Trail</option>
                                    <option value="keap">Keap</option>
                                </select>
                            </div>
                            <div class=" col-1"> To</div>


                            <div class="col-3">
                                <label for="inputAddress" class="form-label ">Account: Destinate</label>
                                <select class="form-select" name="receives" id="destinate_id" aria-label="Default select example" >
                                    <option value="" selected>Select one account</option>
                                    <option value="mailchimp">Mailchimp</option>
                                    <option value="active_trail">Active Trail</option>
                                    <option value="keap">Keap</option>
                                </select>
                            </div>
                            @error('origin')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <!--
                            @error('receives')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            -->


                            <div class="">
                                <label for="inputAddress" class="form-label ">Email's to transfer </label>
                                <textarea name="emails" class="form-control border border-primary" cols="30" rows="10" value="{!! old('correos')??'' !!}" placeholder="Type emails">{!! old('correos')??"" !!}</textarea>
                            </div>
                            @error('emails')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary">Transfer</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
</section>

@endSection

@section('javascript')
<script>
    /*
    function origin_destinate() {
        var origin = $("#origin_id").val();
        if (origin == "mailchimp") {
            $("#destinate_id").val("active_trail");
        }
        if (origin == "active_trail") {
            $("#destinate_id").val("mailchimp");
        }
    }
    */
</script>


@endSection