@extends('layouts.app')

@section('content')
<section class="">
    <!--quit-->
    <div class="container ">
        <!--quit-->
        <div class="row ">
            <div class="card " style="border-style: solid;">
                <div class="card-header mx-auto ">
                    EMAIL TRANSFER MAILCHIMP
                </div>
                <div class="card-body">
                    <div class="col-md-12 mx-auto text-center ">
                        <form action="{!! route('mailchimp.transfer') !!}" method="post" enctype="multipart/form-data" class="row g-3">
                            @csrf

                            <div class="col-4">
                                <label for="inputAddress" class="form-label ">Mailchimp account: origin </label>
                                <select class="form-select" name="origin" aria-label="Default select example">
                                    <option value="" selected>Select one account</option>
                                    <option value="adhel1997@gmail.com">adhel1997@gmail.com</option>
                                </select>
                            </div>
                            @error('origin')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="col-4">
                                <label for="inputAddress" class="form-label ">Mailchimp account: receives</label>
                                <select class="form-select" name="receives" aria-label="Default select example">
                                    <option value="" selected>Select one account</option>
                                    <option value="adelaida.molinar1997@gmail.com">adelaida.molinar1997@gmail.com</option>
                                </select>
                            </div>
                            @error('receives')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

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