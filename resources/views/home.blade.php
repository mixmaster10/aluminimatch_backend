@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <table>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <td colspan=6 style="text-align: center">LIST OF REPORTED POST</td>
                            </tr>
                            <tr>
                                <td>Reported By</td>
                                <td>Post Title</td>
                                <td>Post Description</td>
                                <td>Date Reported</td>
                                <td>Reason</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($reported_posts))
                                @foreach ($reported_posts as $post)
                                    <tr>
                                        <td>{{ $post['reported_by']['last_name'] }}, {{ $post['reported_by']['first_name'] }} {{ substr($post['reported_by']['last_name'],0,1) }}.</td>
                                        <td>{{ $post['reported_post']['title'] }}</td>
                                        <td>{{ $post['reported_post']['description'] }}</td>
                                        <td>{{ $post['created_at'] }}</td>
                                        <td>{{ $post['reason'] }}</td>
                                        <td>
                                            <a href="{{ route('post.remove',['postId'=>$post['id']]) }}" onclick="event.preventDefault(); document.getElementById('submit-form').submit();" class="btn btn-primary">
                                                <i class="fa fa-trash-o fa-2x text-muted mt-4 padding-left-icon"></i> Remove
                                            </a>
                                             <form id="submit-form" action="{{ route('post.remove',['postId'=>$post['id']]) }}" method="POST" class="hidden">
                                                @csrf
                                            
                                                @method('POST')
                                            </form>
                                        </td> 
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
