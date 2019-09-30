<a href="{{  $attachment->getUrl() }}">{{ $attachment->getTitle() }}</a>
@if ($attachment->hasDescription())
    {{ $attachment->getDescription() }}
@endif