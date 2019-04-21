@if ($errors->{$bag ?? 'default'}->any())
	<div class="field mt-6">
		@foreach ($errors->{$bag ?? 'default'}->all() as $error)
			<li class="text-sm text-red">{{ $error }}</li>
		@endforeach
	</div>
@endif