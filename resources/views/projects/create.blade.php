@extends('layouts.app')

@section('content')
	<h1 class="text-muted">Create Project</h1>
	<form style="margin-top: 30px" action="/projects" method="POST" >
		@csrf
	<div class="form-group">
		<label for="title" class="col-sm-2 form-control-label">Title</label>
		<div class="col-sm-12">
			<input name="title" type="text" class="form-control" id="title" >
		</div>
	</div>
	<div class="form-group">
		<label for="title" class="col-sm-2 form-control-label">Description</label>
		<div class="col-sm-12">
			<textarea name="description" class="form-control" id="description"></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>
</form>
@endsection
