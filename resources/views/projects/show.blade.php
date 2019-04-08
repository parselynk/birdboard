@extends('layouts.app')
@section('content')
	<header class="flex items-center mb-3 py-4">
		<div class="flex justify-between items-end w-full">
			<p class="mb-3 text-grey no-underline text-sm font-normal">
				<a href="/projects" class="text-grey no-underline text-sm">My Projects</a> / {{ $project->title }}
			</p>
			<a href="/projects/create" class="button">New Project</a>
		</div>
	</header>
	<main>
		<div class="lg:flex -mx-3">
			<div class="lg:w-3/4 px-3 mb-6">
				<div class="mb-8">
					<h2 class="mb-3 text-lg text-grey no-underline font-normal mb-3">Tasks</h2>
					{{--tasks--}}
						@foreach ($project->tasks as $task)
							<div class="card mb-3">
								<form action="{{ $task->path() }}" method="POST">
									@csrf
									@method('PATCH')
									<div class="flex">
										<input type="text" name="body" value="{{$task->body}}" class="w-full {{ $task->completed ? 'text-grey' : '' }}">
										<input type="checkbox" name="completed"  onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
									</div>
								</form>
							</div>
						@endforeach
						<div class="card mb-3">
							<form action="{{ $project->path() .'/tasks' }}" method="POST">
								@csrf
								<input type="text" name="body" placeholder="Add a new task ..." class="w-full">
							</form>
						</div>
				</div>
				<div>
					<h2 class="mb-3 text-lg text-grey no-underline font-normal mb-3">General notes</h2>
					{{--general notes--}}
					<div class="card">
						<form action="{{ $project->path() }}" method="POST">
						@csrf
						@method('PATCH')
						 <textarea
                            name="notes"
                            class="card w-full mb-4"
                            style="min-height: 200px"
                            placeholder="Anything special that you want to make a note of?"
                        > {{ $project->notes }} </textarea>
                    </form>
					</div>
				</div>
			</div>
			<div class="lg:w-1/4 px-3">
				@include('projects.card')
			</div>
		</div>
	</main>
@endsection