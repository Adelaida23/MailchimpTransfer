@extends('layouts.app')

@section('content')
<section class="">
    <!--quit-->
    <div class="container ">
        <!--quit-->
        <div class="row ">
            <div class="card " style="border-style: solid;">
                <div class="card-header mx-auto ">
                    EMAIL SUBSCRIBE
                </div>
                <div class="card-body">
                    <div class="col-md-12 mx-auto text-center ">
                        <form action="{!! route('mailchimp.subscribe') !!}" method="post" enctype="multipart/form-data" class="row g-3">
                            @csrf

                            <div class="col-4">
                                <label for="inputAddress" class="form-label ">Mailchimp account: origin </label>
                                <select class="form-select" name="origin" aria-label="Default select example">
                                    <option value="" selected>Select one account</option>
                                    <option value="adelaida.molinar1997@gmail.com">Account origin</option>
                                </select>
                            </div>
                            @error('origin')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="col-4"></div>
                            <div class="col-4">
                                <a href="{{route('transfer')}}" class="btn btn-primary">Go to Transfer</a>
                            </div>

                            <div class="">
                                <label for="inputAddress" class="form-label ">Email's to subscribe </label>
                                <textarea name="emails" class="form-control border border-primary" cols="15" rows="5" value="{!! old('correos')??'' !!}" placeholder="Type emails">{!! old('correos')??"" !!}</textarea>
                            </div>
                            @error('emails')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary">Subscribe</button>

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