<article class="message">
    <span class="subtitle is-6 is-pulled-right" style="padding: .4em">{{ $message->getTimestamp() }}</span>
    <div class="message-body">
        @if($message->hasName())
            <div class="title is-6">
                {{ $message->getName() }}
            </div>
        @endif

        {!! $message->getBody() !!}

        @if($message->hasAttachment())
            @foreach($message->getAttachments() as $attachment)
                {!! $attachment->render() !!}
            @endforeach
        @endif
    </div>
</article>
