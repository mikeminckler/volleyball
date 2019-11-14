@extends ('scoreboard')
@section ('content')
<div class="score-keeper-controls">
    <court :id="{{ $court->id }}"></court>
</div>
@endsection
