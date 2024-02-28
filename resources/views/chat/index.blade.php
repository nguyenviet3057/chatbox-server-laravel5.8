@extends('adminlte::page')

@section('title', 'Chat')

@section('css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
    <style>
        .content-header {
            display: none;
        }
        /*
        *   Bootstrap 3.4.1 -> 4.6.1
        */
        .p-0 {
            padding: 0;
        }
        .p-1 {
            padding: 0.25rem !important;
        }
        .p-2 {
            padding: 0.5rem !important;
        }
        .m-0 {
            margin: 0;
        }
        .m-1 {
            margin: 0.25rem !important;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .w-100 {
            width: 100%;
        }
        .h-100 {
            height: 100%;
        }
        .d-none {
            display: none;
        }
        .d-flex {
            display: flex;
        }
        .flex-column {
            flex-direction: column;
        }
        .flex-row {
            flex-direction: row;
        }
        .row {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .row > * {
            flex-shrink: 0;
            max-width: 100%;
        }
        .col-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        .justify-content-between {
            justify-content: space-between;
        }
        .position-absolute {
            position: absolute;
        }
        .position-relative {
            position: relative;
        }
        .fw-bold {
            font-weight: bold;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        /*
        *   Chat CSS
        */
        .content-container *:not(i, a, span.message-reply) {
            font-family: "Nunito", sans-serif;
            font-size: 12px;
            color: black;
        }
        .content-container {
            width: 100% !important;
            height: calc(100vh - 84px);
            overflow-y: auto;
            /* min-height: 600px !important; */
            background-color: black;
            border: 1px solid rgb(65, 31, 31);
        }

        /*
        *
        *   Chat rooms
        *
        */

        #message-nav {
            height: 100%;
            border-right: 1px solid lightgray;
            background-color: white;
        }

        #message-nav > span {
            width: 100%;
            height: 24px;
            vertical-align: middle;
            background-color: #357ca5;
            color: white;
            display: flex;
            align-items: center;
            text-indent: 10px;
        }

        #message-nav .thumbnail-user img {
            width: 36px;
            height: 36px;
            /* border-radius: 50%; */
            max-width: 100%;
            object-fit: contain;
        }

        #message-nav > div {
            max-height: 50%;
            overflow-y: auto;
        }

        #message-nav > div > div.show-all-rooms, #message-nav > div > div.show-less-rooms {
            height: 25px;
        }

        .chat-list {
            height: 50px;
            border-bottom: 1px solid lightgray;
            cursor: pointer;
        }

        .chat-list.active, .chat-list.active .message-timestamp {
            background-color: #f2f2f2 !important;
        }
        .chat-list .message-info .user-name {
            width: 100%;
            position: relative;
            text-overflow: ellipsis;
            overflow-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .chat-list .message-info .message-timestamp {
            position: relative;
            padding-left: 10px;
            background-color: white;
            white-space: nowrap;
        }
        .chat-list .message-content {
            max-width: 100%;
        }
        .chat-list .message-content span {
            width: 100%;
            text-overflow: ellipsis;
            overflow-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /*
        *
        *   Chat messages
        *
        */
        #message-detail .chat-overlay {
            top: 0px;
            left: 0px;
            background-color: #f2f2f2;
        }

        .chat-overlay > p {
            font-size: large;
        }

        .chat-container {
            /* width: 320px; */
            /* height: 360px; */
            background-color: white;
            display: flex;
            flex-direction: column;
            /* border: 1px solid lightgray; */
        }

        #chat-header {
            top: 0px;
            height: 48px;
            background-color: #3c8dbc;
            display: flex;
            align-items: center;
            padding: 0px 10px;
            justify-content: space-between;
        }
        #chat-header .header-title > span {
            color: white !important;
            font-weight: bold;
        }

        #chat-history-container {
            position: relative;
            width: 100%;
            height: calc(100% - 50px - 48px);
            border-top: 1px solid lightgray;
            border-bottom: 1px solid lightgray;
        }

        #chat-history {
            position: absolute;
            bottom: 0px;
            left: 0px;
            width: 100%;
            max-height: 100%;
            padding: 0px 10px;
            height: fit-content;
            overflow-y: auto;
        }

        #chat-history.with-reply {
            max-height: calc(100% - 56px);
            bottom: 56px;
        }

        #chat-history .chat-detail {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        #chat-history .chat-system {
            flex-direction: row-reverse;
        }

        #chat-history .chat-user {
            flex-direction: row;
        }

        #chat-history .chat-avatar {
            top: 0px !important;
        }

        #chat-history .chat-avatar img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        #chat-history .chat-message-list {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .chat-message-list div.reply-note {
            display: flex;
            /* flex-direction: column; */
            margin-top: 3px;
        }
        .chat-message-list div.reply-note.images-message {
            max-width: 100px; /* .chat-message-list div.reply-note img -> max-width = 50px; */
        }
        .chat-user .chat-message-list div.reply-note {
            margin-left: 5px;
        }
        .chat-system .chat-message-list div.reply-note {
            justify-content: flex-end;
            margin-right: 5px;
        }
        .chat-message-list div.reply-note > span {
            font-size: x-small;
            margin-bottom: -2px;
            width: fit-content;
        }
        .chat-message-list div.reply-note > span:last-child {
            /* background-color: #f7e3b4; */
            background-color: #f2f2f2;
            opacity: 0.6;
            border-radius: 5px;
            padding: 1px 3px;
        }
        .chat-system .chat-message-list div.reply-note {
            align-items: flex-end;
        }
        .chat-message-list div.reply-note img {
            height: 50px;
            width: fit-content;
            /* max-width: 50px; */
            border-radius: 3px;
            opacity: 0.6;
            object-fit: contain;
        }

        .chat-system .chat-message-list > div.w-100 {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        #chat-history .chat-message {
            position: relative;
            width: fit-content;
            display: flex;
            align-items: center;
        }

        #chat-history .chat-message:has(> div:first-child:not(:has(> img))) {
            padding: 4px 8px;
            margin: 0px 5px 2px 5px;
            background-color: #f2f2f2;
            /* background-color: #f7e3b4; */
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        #chat-history .chat-message div {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message div > p:last-child {
            margin-bottom: 0px;
        }

        #chat-history .chat-message:has(> span:first-child) {
            padding: 4px 8px;
            margin: 0px 5px 2px 5px;
            background-color: #f2f2f2;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        #chat-history .chat-message span {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message:has(> img:first-child) {
            margin: 0px 5px 2px 5px;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }
        #chat-history .chat-message:has(> img:first-child) img {
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #357ca5;
            border-radius: 8px;
        }
        
        #chat-history .chat-message:has(> div.images-message:first-child) {
            margin: 0px 5px 2px 5px;
            min-height: 28px;
            max-width: 64%;
            justify-content: flex-end;
        }

        #chat-history .chat-message:has(> div.chat-product:first-child) {
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            margin: 0px 5px 2px 5px;
            padding: 4px 8px;
            background-color: #f2f2f2;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }
        .chat-message:has(> div.chat-product:first-child) > div.chat-product > div {
            margin-left: 8px;
        }
        .chat-message:has(> div.chat-product:first-child) .product-title {
            font-weight: bold;
            width: fit-content;
        }
        .chat-message:has(> div.chat-product:first-child) .product-price span {
            width: fit-content;
        }
        .chat-message:has(> div.chat-product:first-child) img {
            max-height: 100px;
            width: auto;
            object-fit: contain;
        }
        .chat-message:has(> div.chat-product:first-child) > span {
            margin-top: 4px;
        }

        #chat-history .chat-message:has(> div.chat-markdown:first-child) {
            margin: 0px 5px 2px 5px;
            padding: 4px 8px;
            background-color: #f2f2f2;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        .system-message div.images-message {
            justify-content: flex-end;
        }

        #chat-history .chat-message div.images-message > div {
            border-radius: 8px;
            border: 1px solid #357ca5;
            cursor: pointer;
            background-color: white;
        }

        #chat-history .chat-message div.images-message > div:hover {
            background-color: #f6f6f6;
        }

        #chat-history .chat-message img {
            width: 100%;
            /* border-radius: 8px; */
            max-height: 160px;
            min-height: 28px;
            object-fit: contain;
        }

        #chat-input form {
            position: relative;
            min-height: 50px;
            background-color: white;
            display: flex;
            align-items: center;
            padding: 2px 0px;
            margin: 0px;
        }

        #chat-input textarea {
            width: 100%;
            min-height: fit-content;
            max-height: 48px;
            padding: 0px 42px 0px 16px;
            outline: none !important;
            border: none !important;
            resize: none;
            overflow-y: auto;
        }

        #chat-input .chat-widget-container {
            display: flex;
            flex-direction: row;
            margin: 0px 10px;
        }

        .chat-widget-container button {
            position: relative;
            border: none;
            background-color: transparent;
            margin: 0px 5px;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-widget-container button input {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
        }

        input[type=file],
        input[type=file]::-webkit-file-upload-button {
            cursor: pointer; 
        }

        .chat-widget-container button:hover {
            color: #3c8dbc;
            cursor: pointer;
        }

        @media screen and (max-width: 576px) {
            #chat-history .chat-message {
                max-width: 90% !important;
            }
        }
        
        /*
        *   Reply section CSS
        */
        .message-reply {
            display: none;
            color: lightgray;
            cursor: pointer;
            width: fit-content;
        }
        .chat-message-list > div.w-100 .message-reply {
            right: -20px;
        }
        .chat-system .chat-message-list > div.w-100 .message-reply {
            left: -20px;
        }
        .chat-message-list > div.w-100:hover .message-reply {
            display: block;
        }

        #chat-reply {
            position: absolute;
            bottom: 0;
            height: 56px;
            width: 100%;
            background-color: rgba(60, 141, 188, 0.25);
            padding: 10px 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            display: none;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        #chat-reply.d-flex {
            display: flex;
        }
        #chat-reply #chat-reply-container {
            position: relative;
            height: 36px;
            display: flex;
            flex-direction: row;
            align-items: center;
            border-left: 1px solid #3c8dbc;
            padding: 0px 10px;
        }
        #chat-reply-container #reply-images {
            height: 100%;
            width: auto;
            max-width: 80px;
            margin-right: 10px;
            display: none;
            overflow: hidden;
            border-radius: 3px;
            border: 1px solid #f7e3b4;
            cursor: pointer;
            background-color: white;
        }
        #chat-reply-container #reply-images.d-show {
            display: flex;
        }
        #chat-reply-container #reply-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        #chat-reply #reply-cancel {
            border: none;
            background: transparent;
            height: fit-content;
            margin-right: 12px;
        }

        /* Switch toggle button for Chat bot */
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #01ff70;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #01ff70;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(16px);
            -ms-transform: translateX(16px);
            transform: translateX(16px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .chat-bot-toggle > span {
            color: white !important;
            margin-right: 0.5rem;
        }
        .chat-bot-toggle > label {
            margin: 0;
        }
    </style>
@stop

@section('content')
    <div class="content-container row w-100 m-0 p-0">
        <div class="col-xs-4 m-0 p-0 d-flex flex-column" id="message-nav">
            {{-- <span id="current-rooms-span">Current</span> --}}
            <div id="current-container">
                <ul class="w-100 list-unstyled mb-0" id="current-rooms">
                </ul>
                {{-- <div class="w-100 d-none justify-content-center align-items-center show-all-rooms" id="show-all-current" role="button">
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="w-100 d-none justify-content-center align-items-center show-less-rooms" id="show-less-current" role="button">
                    <i class="fas fa-chevron-up"></i>
                </div> --}}
            </div>
            {{-- <span id="waiting-rooms-span">Waiting</span>
            <div id="waiting-container">
                <ul class="w-100 list-unstyled mb-0" id="waiting-rooms">
                </ul>
                <div class="w-100 d-none justify-content-center align-items-center show-all-rooms" id="show-all-waiting" role="button">
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="w-100 d-none justify-content-center align-items-center show-less-rooms" id="show-less-waiting" role="button">
                    <i class="fas fa-chevron-up"></i>
                </div>
            </div> --}}
        </div>
        <div class="col col-xs-8 m-0 p-0 h-100" id="message-detail">
            <div class="chat-container w-100 h-100">
                <div id="chat-header">
                    <div class="header-title">
                        <span></span>
                    </div>
                    <div class="header-function">
                        <div class="chat-bot-toggle d-flex align-items-center">
                            <span>Chat Bot</span>
                            <label class="switch">
                                <input id="toggle-bot" type="checkbox">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="chat-history-container">
                    <div id="chat-history"></div>
                    <div id="chat-reply">
                        <div id="chat-reply-container">
                            <div id="reply-images" class="row images-message m-0">
                            </div>
                            <div id="reply-detail">
                                <span id="reply-name" class="fw-bold"></span>
                                <span id="reply-content"></span>
                            </div>
                        </div>
                        <button id="reply-cancel">
                            <i class="fas fa-times m-0"></i>
                        </button>
                    </div>
                </div>
                <div id="chat-input">
                    <form>
                        <textarea id="message" placeholder="Aa" rows="1"></textarea>
                        <div class="chat-widget-container">
                            <button type="button">
                                <i class="fas fa-image"></i>
                                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" multiple>
                            </button>
                            <!-- <button type="button">
                                <i class="fa-solid fa-paperclip"></i>
                                <input type="file" name="file" id="file">
                            </button> -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="chat-overlay position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                <p>Please select a chat room</p>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var system_data = {
            id: "{{ $user_data->id }}",
            name: "{{ $user_data->name }}",
            email: "{{ $user_data->email }}",
            phone: "{{ $user_data->phone }}",
            avatar_url: "{{ $user_data->avatar_url }}",
        }
    </script>
    <script>
        var origin_title = document.title;
        var topic;
        var message;
        var data
        window.addEventListener('message', function(event) {
            var receivedData = event.data;
            if (typeof receivedData === 'object' && receivedData !== null) {
                topic = receivedData.topic;
                message = receivedData.message;
                data = receivedData.data;
    
                if (topic == "UNREAD COUNTER" && data.unread_counter != null) {
                    if (data.unread_counter > 0) document.title = `(${data.unread_counter}) ${origin_title}`;
                    else document.title = origin_title;
                }
            }
        });
    </script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/js/image-compressor1.1.4.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/markdown-it.min.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.8/push.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.8/serviceWorker.min.js"></script> --}}
    <script>
        if ('Notification' in window) {
            if (Notification.permission === 'default') {
                console.log('default')
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        console.log('granted');
                        const iframe = document.getElementById('gozic-iframe');
                        const data = {
                            message: 'Notification permission is granted',
                            is_granted: true
                        };

                        iframe.contentWindow.postMessage(data, '*');
                    }
                });
            }
        }
    </script>
@stop