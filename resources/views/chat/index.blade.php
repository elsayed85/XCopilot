<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0"/>
    <meta name="description" content="A conversational AI system that listens, learns, and challenges"/>
    <meta property="og:title" content="ChatGPT"/>
    <meta property="og:image" content="https://openai.com/content/images/2022/11/ChatGPT.jpg"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta
        property="og:description"
        content="A conversational AI system that listens, learns, and challenges"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="{{ asset('img/apple-touch-icon.png') }}"/>
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('img/favicon-32x32.png') }}"/>
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="{{ asset('img/favicon-16x16.png') }}"/>
    <link rel="manifest" href="{{ asset('img/site.webmanifest') }}"/>
    <link
        rel="stylesheet"
        href="{{ asset('css/dracula.min.css') }}"/>
    <title>Chat</title>
</head>

<body data-urlprefix="{{ url('/')  }}">
<div class="main-container">
    <div class="box sidebar">
        <div class="top">
            <button class="button" onclick="new_conversation()">
                <i class="fa-regular fa-plus"></i>
                <span>{{_('New Conversation')}}</span>
            </button>
            <div class="spinner"></div>
        </div>
        <div class="sidebar-footer">
            <button class="button" onclick="window.location='{{ route('filament.user.pages.dashboard') }}'">
                <i class="fa-regular fa-home"></i>
                <span>
                    {{_('Dashboard')}}
                </span>
            </button>

            <br>

            <button class="button" onclick="delete_conversations()">
                <i class="fa-regular fa-trash"></i>
                <span>{{_('Clear Conversations')}}</span>
            </button>

            <div class="settings-container">
                <div class="checkbox field">
                    <span>{{_('Dark Mode')}}</span>
                    <input type="checkbox" id="theme-toggler"/>
                    <label for="theme-toggler"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="conversation">
        <div class="stop-generating stop-generating-hidden">
            <button class="button" id="cancelButton">
                <span>{{_('Stop Generating')}}</span>
            </button>
        </div>
        <div class="box" id="messages"></div>
        <div class="user-input">
            <div class="box input-box">
						<textarea
                            id="message-input"
                            placeholder="{{_('Ask a question')}}"
                            cols="30"
                            rows="10"
                            style="white-space: pre-wrap"></textarea>
                <div id="send-button">
                    <i class="fa-regular fa-paper-plane-top"></i>
                </div>
            </div>
        </div>
        <div>
            <div class="options-container">
                <div class="buttons">
                    <div class="field">
                        <select class="dropdown" name="github_account" id="github_account_id">
                            <option value="">{{_('Select Github Account')}}</option>
                            @foreach($github_accounts as $github_account)
                                <option value="{{ $github_account->id }}" @if($loop->first) selected @endif data-shared="{{ $github_account['shared'] ? "1" : "0" }}">
                                    {{ $github_account->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field checkbox">
                    <input type="checkbox" id="switch"/>
                    <label for="switch"></label>
                    <span>{{_('Web Access')}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="menu-button">
    <i class="fa-solid fa-bars"></i>
</div>

<!-- scripts -->
<script>
    window.conversation_id = "{{ $conversation }}";
</script>
<script src="{{ asset('js/icons.js') }}"></script>
<script src="{{ asset('js/chat.js') }}" defer></script>
<script src="{{ asset('js/markdown-it.min.js') }}"></script>
<script src="{{ asset('js/highlight.min.js') }}"></script>
<script src="{{ asset('js/highlightjs-copy.min.js') }}"></script>
<script src="{{ asset('js/theme-toggler.js') }}"></script>
<script src="{{ asset('js/sidebar-toggler.js') }}"></script>
</body>
</html>
