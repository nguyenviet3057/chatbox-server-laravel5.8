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

        .chat-list.active {
            background-color: #f2f2f2;
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
            background-color: #e8e8e8;
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
            padding-left: 1rem;
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

        #chat-history .chat-message:has(> div:first-child:not(:has(> img))) {
            position: relative;
            padding: 4px 8px;
            margin: 0px 5px 2px 5px;
            background-color: #f2f2f2;
            /* background-color: #f7e3b4; */
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
            width: fit-content;
            display: flex;
            align-items: center;
        }

        #chat-history .chat-message div {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message div > p:last-child {
            margin-bottom: 0px;
        }

        #chat-history .chat-message:has(> span:first-child) {
            position: relative;
            padding: 4px 8px;
            margin: 0px 5px 2px 5px;
            background-color: #f2f2f2;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
            width: fit-content;
            display: flex;
            align-items: center;
        }

        #chat-history .chat-message span {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message:has(> img:first-child) {
            position: relative;
            margin: 0px 5px 2px 5px;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
            width: fit-content;
            display: flex;
            align-items: center;
        }
        #chat-history .chat-message:has(> img:first-child) img {
            border-radius: 8px;
            cursor: pointer;
        }
        
        #chat-history .chat-message:has(> div.images-message:first-child) {
            position: relative;
            margin: 0px 5px 2px 5px;
            min-height: 28px;
            max-width: 64%;
            width: fit-content;
            display: flex;
            align-items: center;
            justify-content: flex-end;
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
    </style>
@stop

@section('content')
    <div class="content-container row w-100 m-0 p-0">
        <div class="col-xs-4 m-0 p-0 d-flex flex-column" id="message-nav">
            <span id="current-rooms-span">Current</span>
            <div id="current-container">
                <ul class="w-100 list-unstyled mb-0" id="current-rooms">
                </ul>
                <div class="w-100 d-none justify-content-center align-items-center show-all-rooms" id="show-all-current" role="button">
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="w-100 d-none justify-content-center align-items-center show-less-rooms" id="show-less-current" role="button">
                    <i class="fas fa-chevron-up"></i>
                </div>
            </div>
            <span id="waiting-rooms-span">Waiting</span>
            <div id="waiting-container">
                <ul class="w-100 list-unstyled mb-0" id="waiting-rooms">
                </ul>
                <div class="w-100 d-none justify-content-center align-items-center show-all-rooms" id="show-all-waiting" role="button">
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="w-100 d-none justify-content-center align-items-center show-less-rooms" id="show-less-waiting" role="button">
                    <i class="fas fa-chevron-up"></i>
                </div>
            </div>
        </div>
        <div class="col col-xs-8 m-0 p-0 h-100" id="message-detail">
            <div class="chat-container w-100 h-100">
                <div id="chat-header">
                    <div class="header-title">
                        <span></span>
                    </div>
                    <div class="header-function"></div>
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
            gender: {{ $user_data->gender }},
            avatar_url: "{{ $user_data->avatar_url }}",
        }
    </script>
    <script src="{{ asset('assets/js/utils.js') }}"></script>
    <script src="{{ asset('assets/js/image-compressor1.1.4.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/markdown-it.min.js') }}"></script>
@stop