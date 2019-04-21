<div class="card flex flex-col mt-3">
	<h3 class="font-normal text-xl -ml-5 mb-3 py-4 border-l-4 border-blue-light pl-4"><a href="{{ $project->path() }}" class="text-black no-underline">{{ $project->title }}</a></h3>
    <form method="POST" action="{{ $project->path() }} /invitations">
        @csrf
		<div class="mb-3">
		    <input type="email" name="email" class="border border-grey-light rounded w-full py-2 px-3" placeholder="Email address">
		 </div>					            
		 <button type="submit" class="button">Invite</button>
		 @include('errors', ['bag' => 'invitations'])
    </form>
</div>
