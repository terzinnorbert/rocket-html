@extends('base')

@section('content')
    <div class="column is-5-tablet is-4-desktop is-3-widescreen">
        <form action="/download" method="post" class="box">
            <div class="field">
                <label for="" class="label">Rocket url</label>
                <div class="control has-icons-left">
                    <input type="text" placeholder="https://rocket..." class="input" name="url" required>
                    <span class="icon is-small is-left">
                      <i class="fa fa-globe"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <label for="" class="label">Username</label>
                <div class="control has-icons-left">
                    <input type="text" placeholder="username" class="input" name="username" required>
                    <span class="icon is-small is-left">
                      <i class="fa fa-user"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <label for="" class="label">Password</label>
                <div class="control has-icons-left">
                    <input type="password" placeholder="*******" class="input" name="password" required>
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock"></i>
                    </span>
                </div>
            </div>
            <div class="field has-text-centered	">
                <button type="submit" class="button is-success">
                    Export my rooms
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    $('form').submit(function () {
    var $button = $(this).find(':input[type=submit]');
    $button.prop('disabled', true);
    $button.text('Download in progress');
    });
@endsection