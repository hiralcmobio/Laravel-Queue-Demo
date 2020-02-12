@extends('layouts.app')

@section('content')
    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('message') }}</p>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Add Blog</div>

                    <form action="postBlog" method="post">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right" for="btitle">Title</label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="title">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right" for="bdescription">Description</label>
                            <div class="col-md-6">
                                <textarea cols="30" class="form-control" rows="4" name="description"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <input type="submit" name="submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

