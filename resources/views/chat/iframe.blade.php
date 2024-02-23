<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
    <link rel="stylesheet" href="{{ asset('dist/css/fontawesome6.5.1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap5.3.2.min.css') }}">
    <script src="{{ asset('dist/js/jquery3.7.1.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap5.3.2.min.js') }}"></script>
    <script src="{{ asset('dist/js/fontawesome6.5.1.min.js') }}"></script>
    <script src="{{ asset('dist/js/image-compressor1.1.4.min.js') }}"></script>
    <script src="{{ asset('dist/js/markdown-it.min.js') }}"></script>
    <script src="{{ asset('dist/js/utils.js') }}"></script>
    <style>
        body {
            background: transparent;
        }

        * {
            margin: 0;
            padding: 0;
            font-family: "Nunito", sans-serif;
            font-size: 12px;
        }

        div.root {
            position: relative;
            width: 100vw;
            height: 100vh;
        }

        #popup-iframe-gozic {
            position: absolute;
            bottom: 0;
            right: 0;
            padding: inherit;
        }
        #popup-iframe-gozic.alert-new-message::before {
            content: "";
            width: 16px;
            height: 16px;
            background-color: red;
            position: absolute;
            top: 0;
            right: 3px;
            border-radius: 50%;
            animation: shake 0.5s infinite alternate;
        }
        #popup-iframe-gozic.alert-new-message::after {
            content: attr(data-unread-counter);
            width: 16px;
            height: 16px;
            background-color: red;
            color: white;
            font-weight: bold;
            position: absolute;
            top: 0;
            right: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        @keyframes shake {
            0% {
                scale: 1.2;
                /* transform: rotate(-8deg); */
            }
            50% {
                scale: 1.1;
                /* transform: rotate(0deg); */
            }
            100% {
                scale: 1.2;
                /* transform: rotate(8deg); */
            }
        }

        #popup-iframe-gozic img {
            width: 50px;
            height: 50px;
        }

        .chat-container {
            position: relative;
            /* width: 320px;
            height: 360px; */
            width: 100%;
            height: calc(100% - 78px);
            /* background-color: #e8e8e8; */
            background-color: white;
            display: flex;
            flex-direction: column;
            border: 1px solid lightgray;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0px 0px 4px lightgray;

            scale: 0;
            transform-origin: right bottom;
            animation-fill-mode: forwards;
        }

        #customer-info {
            position: absolute;
            top: 64px;
            left: 0px;
            width: 100%;
            height: calc(100% - 64px);
            background-color: white;
            z-index: 999;
        }

        #customer-info span#welcome-title {
            color: #f9bd43;
            height: 30px;
            white-space: nowrap;
            overflow: hidden;
            margin: 0 auto;
            animation: 
                typing 2s steps(40, end),
                blink-caret .25s step-end infinite;
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: orange; }
        }

        #customer-info > div.container-fluid {
            width: 100%;
            height: calc(100% - 48px);
            overflow-y: auto;
        }

        #customer-info > div.container-fluid > div:first-child {
            position: relative;
            width: 300px;
            /* background-color: aqua; */
            color: rgba(0, 0, 0, 1);
        }

        #customer-info .invalid {
            border: 1px solid red;
            color: red;
        }

        #customer-info .invalid::placeholder {
            color: red;
            opacity: 1;
        }

        #customer-info .invalid::-ms-input-placeholder {
            color: red;
        }

        #customer-info select {
            color: gray;
        }

        #customer-info select option {
            color: black;
        }

        #customer-info .overlay-block {
            height: 64px;
        }

        #customer-info .overlay-opacity {
            position: absolute;
            bottom: 0;
            width: 300px;
            height: 48px;
            box-shadow: 0px -12px 64px 16px white;
        }

        #customer-info button {
            position: absolute;
            bottom: 48px;
            background-color: #f9bd43;
        }

        #customer-info button:hover {
            background-color: #806524;
            color: white;
        }

        #chat-header {
            top: 0px;
            height: 64px;
            /* background-color: gray; */
            background-color: #f9bd43;
        }

        #chat-header .header-function button {
            color: #f7e3b4;
        }

        #chat-header .header-function button:hover {
            color: white;
        }

        #chat-history-container {
            position: relative;
            width: 100%;
            height: calc(100% - 64px - 48px);
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

        #chat-history .chat-user {
            flex-direction: row-reverse;
        }

        #chat-history .chat-system {
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
            justify-content: flex-end;
            margin-right: 5px;
        }
        .chat-system .chat-message-list div.reply-note {
            margin-left: 5px;
        }
        .chat-message-list div.reply-note > span {
            font-size: x-small;
            margin-bottom: -2px;
            width: fit-content;
        }
        .chat-message-list div.reply-note > span:last-child {
            background-color: #f7e3b4;
            opacity: 0.6;
            border-radius: 5px;
            padding: 1px 3px;
        }
        .chat-message-list div.reply-note img {
            height: 50px;
            width: fit-content;
            /* max-width: 50px; */
            border-radius: 3px;
            opacity: 0.6;
            object-fit: contain;
        }

        .chat-user .chat-message-list > div.w-100 {
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
            /* background-color: #f2f2f2; */
            background-color: #f7e3b4;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        /* #chat-history .chat-message {
            position: relative;
            display: flex;
            align-items: center;
        } */

        #chat-history .chat-message div {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message div > p:last-child {
            margin-bottom: 0px;
        }

        #chat-history .chat-message:has(> span:first-child) {
            padding: 4px 8px;
            margin: 0px 5px 2px 5px;
            /* background-color: #f2f2f2; */
            background-color: #f7e3b4;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        #chat-history .chat-message span {
            overflow-wrap: anywhere;
        }

        #chat-history .chat-message:has(> img:first-child) {
            margin: 0px 5px 2px 5px;
            /* background-color: #f2f2f2; */
            background-color: #f7e3b4;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }
        #chat-history .chat-message:has(> img:first-child) img {
            border-radius: 8px;
            cursor: pointer;
        }

        #chat-history .chat-message:has(> div.images-message:first-child) {
            margin: 0px 5px 2px 5px;
            min-height: 28px;
            max-width: 64%;
            justify-content: flex-end;
        }

        #chat-history .chat-message:has(> div.chat-markdown:first-child) {
            margin: 0px 5px 2px 5px;
            padding: 4px 8px;
            background-color: #f7e3b4;
            border-radius: 8px;
            min-height: 28px;
            max-width: 64%;
        }

        .user-message div.images-message {
            justify-content: flex-end;
        }

        #chat-history .chat-message div.images-message > div {
            border-radius: 8px;
            border: 1px solid #f7e3b4;
            cursor: pointer;
            background-color: white;
        }
        
        #chat-history .chat-message div.images-message > div:hover {
            background-color: #f6f6f6;
        }

        #chat-history .chat-message img {
            width: 100%;
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

        #chat-input form.bot-chatting::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
        }
        #chat-input form.bot-chatting textarea#message {
            background: transparent !important;
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
            color: #f9bd43;
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
            color: #806524;
            cursor: pointer;
        }

        @media screen and (max-width: 576px) {
            #chat-history .chat-message {
                max-width: 80% !important;
            }
        }

        .message-reply {
            display: none;
            color: lightgray;
            cursor: pointer;
            width: fit-content;
        }
        .chat-system .chat-message-list > div.w-100 .message-reply {
            right: -20px;
        }
        .chat-user .chat-message-list > div.w-100 .message-reply {
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
            background-color: #f7e3b4;
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
            border-left: 1px solid #f9bd43;
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
</head>
<body>
    <div class="container-fluid root p-1">
        <div id="popup-iframe-gozic" role="button">
            <img src="" alt="">
        </div>
        <div class="chat-container">
            <div id="customer-info">
                <div class="container-fluid m-0 p-0 w-100 d-flex justify-content-center">
                    <div>
                        <div class="d-flex flex-column form-info px-2">
                            <span class="h3 mt-4 text-center fw-bold" id="welcome-title"></span>
                            <span class="h6 mb-2 mt-2">Please fill your information</span>
                            <input type="text" name="name" id="name" class="form-control mb-3 p-3" placeholder="Enter your name" maxlength="50">
                            <input type="tel" name="phone" id="phone" class="form-control mb-3 p-3" placeholder="Enter your phone number" minlength="10" maxlength="10">
                            <div class="overlay-block"></div>
                        </div>
                    </div>
                    <div class="overlay-opacity"></div>
                    <button class="btn border border-0 rounded px-4 py-2">Chat</button>
                </div>
            </div>
            <div id="chat-header"
                class="container-fluid d-flex flex-row justify-content-between align-items-center px-4">
                <div class="header-title h3 fw-bold text-white">
                    Chat
                </div>
                <div class="header-function">
                    <!-- <button class="border border-0 bg-transparent me-3">
                        <i class="fas fa-minus-circle" style="font-size: xx-large;"></i>
                    </button> -->
                    <button class="border border-0 bg-transparent" id="close-chat-btn">
                        <i class="fas fa-chevron-circle-down" style="font-size: xx-large;"></i>
                    </button>
                </div>
            </div>
            <div id="chat-history-container" class="container-fluid p-0">
                <div id="chat-history">
                </div>
                <div id="chat-reply">
                    <div id="chat-reply-container">
                        <div id="reply-images" class="row images-message">
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
                            <i class="fa-solid fa-image"></i>
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
    </div>
</body>
<script>
    var avatar_list = {
        admin: 'https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg',
        system: 'https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg',
        user: 'https://appbanhang.gozic.vn/uploads/stores/97/2024/01/download-removebg-preview.png',
    };

    $("#popup-iframe-gozic img").prop("src", 'https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg');
    $("#welcome-title").text('Chào mừng tới Gozic');
</script>
<script type="module">
    // Import modules
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-analytics.js";
    import { getAuth, connectAuthEmulator } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
    // import { getDatabase, connectDatabaseEmulator, ref, child, push, get, set, update, serverTimestamp, onValue, off, query, orderByChild, equalTo, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";
    import { getFirestore, connectFirestoreEmulator, collection, orderBy, getDocs, doc, getDoc, addDoc, setDoc, updateDoc, serverTimestamp, onSnapshot, query, where } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

    // Initialize Firebase
    const firebaseConfig = {
        apiKey: "AIzaSyDw2S01aViwowyyJ-A0m7pVTX8OIZF2VJU",
        authDomain: "ziczacapp.firebaseapp.com",
        databaseURL: "https://ziczacapp-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "ziczacapp",
        storageBucket: "ziczacapp.appspot.com",
        messagingSenderId: "1054197522212",
        appId: "1:1054197522212:web:02a6765b198580e53f9db1",
        measurementId: "G-8XQG0CX88E"
    };
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
    // var db = getDatabase(app);
    var auth = getAuth(app);
    var fs = getFirestore(app);
    // connectFirestoreEmulator(fs, '127.0.0.1', 8082);
    var messaging = getMessaging(app);
    var swRegistration;
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            if (registrations.length == 0) {
                navigator.serviceWorker.register('/dist/js/firebase-messaging-sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered:', registration);
                    swRegistration = registration;
                })
                .catch(function(error) {
                    console.error('Service Worker registration failed:', error);
                });
            }
        });
    } else {
        console.log('Service Worker is not supported.');
    }
    
    // Server APIs config
    const BASE_URL = "https://appbanhang.gozic.vn";
    // const API_CREATE_CHAT = "/module/chat-firebase/create-chat";
    const API_PING = "/api/ping"; // Get server config
    const API_CHAT_BOT = "/module/chat-firebase/bot"; // Not in-use yet
    // const USER_AVATAR_URL = "https://appbanhang.gozic.vn/uploads/stores/97/2024/01/download-removebg-preview.png";

    // let welcome_message = "Chào mừng bạn đến với Gozic. Hãy để lại câu hỏi để chúng tôi có thể giải đáp giúp bạn!"
    function firstMessage(user_data) {
        return "Thông tin:\nTên: " + (user_data.name ?? "") + "\nSố điện thoại: " + (user_data.phone ?? "");
    }
    let user_data = {};
    let system_data = {
        id: "000097",
        name: "App Bán Hàng",
        phone: "0987654321",
        avatar_url: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
        vapid: "BOdrLVZfmC0wgJc8PNMXS3ZY66jqpaU1KVee4Y1YNg5h8aEEHjbwcY4LqOPzzUc2h387XUsjsViaZfQuGpWWz9I"
    };
    
    user_data = JSON.parse(localStorage.getItem("user_data"));
    // system_data = JSON.parse(localStorage.getItem("system_data"));
    if (user_data && Date.now() - new Date(user_data.created_at).getTime() > 2*365*24*3600*1000) {
        console.log("time out")
        user_data = null;
        // system_data = null;
    }

    /*
    *
    *   Collections/Refs && Utils
    *
    */
    // Cols
    const col_rooms = collection(fs, "chat_rooms_gozic");
    const col_messages = collection(fs, "messages");
    const col_users = collection(fs, "users_gozic");
    // console.log(col_rooms);

    var room_id = null;
    var thread_id = null;
    var col_room = null;
    var doc_room = null;
    var bot_ready = false;
    var showChat = false;
    var lastid = null;
    var list_message_ids = [];
    var unread_counter = -1
    const CHECK_TYPE = {
        CHAT_TEXT: 1,
        CHAT_IMAGES: 2,
        CHAT_PRODUCT: 3,
        REPLY_CHAT_TEXT: 4,
        REPLY_CHAT_IMAGES: 5,
        REPLY_CHAT_PRODUCT: 6,
        CHAT_BOT: 7
    }

    var reply = {
        id: "",
        check: CHECK_TYPE.CHAT_TEXT,
        reply: "",
        name: "",
        images: []
    }

    // Base query
    const docRoomByRoomId = (room_id) => {
        return doc(col_rooms, room_id);
    }
    const colMessageByRoomId = (room_id) => {
        let doc_room = docRoomByRoomId(room_id);
        return collection(doc_room, 'chat');
    }

    // Add new message
    function addMessage(message, message_type = "text") {
        // console.log(message, reply)
        const col_chat = collection(fs, "chat_rooms_gozic", room_id, "chat");
        const doc_chat_data = {
            "askimg": "",
            "chat": message_type == "text" ? message : "",
            "check": reply.check,
            "id": "",
            "images": message_type == "text" ? [] : message,
            "price": 0,
            "receiverId": system_data.id,
            "receiverName": system_data.name,
            "receiverToken": "",
            "reply": reply.reply,
            "replyimages": reply.images,
            "senderId": user_data.id,
            "senderName": user_data.name,
            "senderPhone": user_data.phone,
            "timestamp": new Date(),
            "title": ""
        }
        addDoc(col_chat, doc_chat_data).then((doc) => localStorage.setItem("last_message_id", doc.id));

        const update_room = {
            "lastchat": message_type == "text" ? message : "đã gửi ảnh",
            "lastid": user_data.id,
            "participants": [
                user_data.id,
                system_data.id
            ],
            "receiverAvatar": system_data.avatar_url,
            "receiverId": system_data.id,
            "receiverName": system_data.name,
            "receiverRoleId": 1,
            "senderAvatar": user_data.avatar_url,
            "senderId": user_data.id,
            "senderName": user_data.name,
            "senderRoleId": 10,
            "timestamp": new Date(),
            "unread": 1,
        };
        updateDoc(docRoomByRoomId(room_id), update_room);

        // Send Cloud Messaging Notification to mobile device
        let data = {
            "notification": {
                "title": system_data.name, 
                "body": message_type == "text" ? message : "đã gửi ảnh"
            },
            "priority": "high", 
            "data": {
                "click_action":"test",
                "id": "1",
                "status": "done",
                "message" : message_type == "text" ? message : "đã gửi ảnh"
            },
            "to": system_data.token ?? ""
        };
        $.post({
            url: "https://fcm.googleapis.com/fcm/send",
            contentType: 'application/json; charset=utf-8',
            headers: {
                Authorization: "key=AAAA9XMRnyQ:APA91bEoAtbVA7Uq47FtyQa71SpBMdVhfWT3sKeVODNH9izm4yI8pQJ3oTdDSf_FGC4rVcaXEJjZFzfTyC6izycPetPZlx0HSghNgJ5zbzN9XWYkLVe_zMEfj28l52eeWE4Z_LmFpIJ-",
            },
            dataType: 'json',
            data: JSON.stringify(data)
        })

        resetReply();

        if (thread_id && bot_ready) {
            bot_ready = false;
            $('#chat-input form').addClass('bot-chatting');
            $('textarea#message').prop("disabled", true).prop("placeholder", "Vui lòng chờ, trợ lý đang nhắn tin...").blur();

            let data = {
                message: message,
                thread_id: thread_id
            };
            const headers = {
                'Authorization': 'Bearer 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',
                'Content-Type': 'application/json; charset=utf-8',
                'OpenAI-Beta': 'assistants=v1',
            };
            
            // Add message to thread
            $.post({
                url: "https://api.openai.com/v1/threads/" + thread_id + "/messages",
                headers: headers,
                dataType: 'json',
                data: JSON.stringify({
                    "role": "user",
                    "content": message
                }),
                success: function (result) {
                    // Create run
                    $.post({
                        url: "https://api.openai.com/v1/threads/" + thread_id + "/runs",
                        headers: headers,
                        dataType: 'json',
                        data: JSON.stringify({
                            "assistant_id": "asst_XJdELsXpLgGLPRom0w5H2d4z"
                        }),
                        success: function(result) {
                            // Retrieve run
                            let retrieve_run = null;
                            let run_status = result.status;
                            let begin_time = Date.now();
                            let current_retrieve_time = 0;
                            let retrieve_times = 16;
                            retrieve_run = setInterval(function() {
                                $.get({
                                    url: "https://api.openai.com/v1/threads/" + thread_id + "/runs/" + result.id,
                                    headers: headers,
                                    dataType: 'json',
                                    data: {
                                        "assistant_id": "asst_XJdELsXpLgGLPRom0w5H2d4z"
                                    },
                                    success: function(result) {
                                        run_status = result.status;
                                    },
                                    error: function (error) {
                                        console.log("Bot failed to chat: 1+");
                                        return;
                                    }
                                })
                            
                                if (run_status == 'failed' || run_status == 'expired' || run_status == 'cancelled') {
                                    console.log("Bot failed to chat: 1++");
                                    return;
                                }

                                current_retrieve_time += 1;
                                if (current_retrieve_time >= retrieve_times || run_status == 'completed') {
                                    clearInterval(retrieve_run);
                                    // Get message list
                                    $.get({
                                        url: "https://api.openai.com/v1/threads/" + thread_id + "/messages?limit=5&order=desc",
                                        headers: headers,
                                        success: function(result) {
                                            // console.log(result);
                                            // console.log(result.data[0].content[0].text.value);
                                            const col_chat = collection(fs, "chat_rooms_gozic", room_id, "chat");
                                            const doc_chat_data = {
                                                "askimg": "",
                                                "chat": result.data[0].content[0].text.value ?? "",
                                                "check": CHECK_TYPE.CHAT_BOT,
                                                "id": "",
                                                "images": [],
                                                "price": 0,
                                                "receiverId": user_data.id,
                                                "receiverName": user_data.name,
                                                "receiverToken": "",
                                                "reply": "",
                                                "replyimages": [],
                                                "senderId": system_data.id,
                                                "senderName": system_data.name,
                                                "senderPhone": system_data.phone,
                                                "timestamp": new Date(),
                                                "title": ""
                                            }
                                            addDoc(col_chat, doc_chat_data).then((doc) => localStorage.setItem("last_message_id", doc.id));

                                            const update_room = {
                                                "lastchat": result.data[0].content[0].text.value ?? "",
                                                "lastid": system_data.id,
                                                "participants": [
                                                    user_data.id,
                                                    system_data.id
                                                ],
                                                "receiverAvatar": user_data.avatar_url,
                                                "receiverId": user_data.id,
                                                "receiverName": user_data.name,
                                                "receiverRoleId": 1,
                                                "senderAvatar": system_data.avatar_url,
                                                "senderId": system_data.id,
                                                "senderName": system_data.name,
                                                "senderRoleId": 10,
                                                "timestamp": new Date(),
                                                "unread": 1,
                                            };
                                            updateDoc(docRoomByRoomId(room_id), update_room);
                                            bot_ready = true;
                                            $('#chat-input form').removeClass('bot-chatting');
                                            $('textarea#message').prop("disabled", false).prop("placeholder", "Aa").focus();
                                        },
                                        error: function(error) {
                                            console.log("Bot failed to chat: 3");
                                            bot_ready = true;
                                            $('#chat-input form').removeClass('bot-chatting');
                                            $('textarea#message').prop("disabled", false).prop("placeholder", "Aa").focus();
                                        }
                                    })
                                }

                                if (current_retrieve_time >= retrieve_times && run_status != 'completed') {
                                    console.log("Bot failed to chat: 2");
                                    clearInterval(retrieve_run)
                                    bot_ready = true;
                                    $('#chat-input form').removeClass('bot-chatting');
                                    $('textarea#message').prop("disabled", false).prop("placeholder", "Aa").focus();
                                }
                            }, 5000);
                        },
                        error: function (error) {
                            console.log("Bot failed to chat: 1");
                            bot_ready = true;
                            $('#chat-input form').removeClass('bot-chatting');
                            $('textarea#message').prop("disabled", false).prop("placeholder", "Aa").focus();
                        }
                    })
                },
                error: function (error) {
                    console.log("Bot failed to chat: 0");
                    bot_ready = true;
                    $('#chat-input form').removeClass('bot-chatting');
                    $('textarea#message').prop("disabled", false).prop("placeholder", "Aa").focus();
                }
            });
        }

        // Sync message in real-time with database and make request with bot if no admin takes care
        if (!bot_ready && false) {
            console.log("Start tracking");
            // Request chat with bot
            setTimeout(function () {
                let data = {
                    message: message,
                    thread_id: thread_id
                };
                $.post({
                    url: BASE_URL + API_CHAT_BOT,
                    data: JSON.stringify(data),
                    dataType : "json",
                    contentType: "application/json; charset=utf-8",
                    success: function (result) {
                        console.log(result);
                        switch (result.status) {
                            case 0:
                                console.log("Admin has already taken care");
                                break;
                            case 1:
                                console.log("Chat with bot");
                                // renderMessage("BEGIN_CHATBOT", "text", "Bot entered", "system");
                                let data= result.data;
                                set(push(ref_messages), {
                                    "room_id": room_id ?? "",
                                    "type": "system",
                                    "user_id": user_data.id,
                                    "message_type": "markdown",
                                    "message": data.latest_message,
                                    "is_read": false,
                                    "created_at": new Date()
                                });
                                break;
                        }
                    },
                    error: function (error) {
                        console.log("Bot failed to enter");
                    }
                })

                onSnapshot(query(colMessageByRoomId(room_id), orderBy("timestamp")), (snapshot) => {
                    const documents = snapshot.docs;
                    console.log("Current documents in collection: ", documents);
                    chat_history.innerHTML = "";
                    last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
                    last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
                    renderOldMessages(documents);
                }, (error) => {
                    console.log(error);
                });
                bot_ready = true;
            }, 10000);
        }
    }

    // Check user is whether exist or new
    async function checkUser() {
        avatar_list['user'] = user_data.avatar_url;
        getDocs(query(col_rooms, where("participants", "array-contains", user_data.id))).then((rooms) => {
            rooms.forEach(room => {
                room_id = room.id;
                doc_room = doc(fs, 'chat_rooms_gozic', room_id);
            });
            getDoc(doc_room).then(doc_room_data => {
                if (doc_room_data.exists()) {    
                    system_data.id = doc_room_data.data().participants[1];
                    thread_id = doc_room_data.data().threadId;
                    if (thread_id && thread_id != "") bot_ready = true;
                    // console.log("Retrive thread: " + thread_id);

                    // Sync message in real-time with firestore
                    onSnapshot(query(colMessageByRoomId(room_id), orderBy("timestamp")), (snapshot) => {
                        // console.log("Retrieve: " + room_id);
                        const messages = snapshot.docs;
                        list_message_ids = messages.map(message => message.id);
                        // console.log(list_message_ids)
                        let last_message_id = localStorage.getItem("last_message_id");
                        if (last_message_id && last_message_id != "" && list_message_ids.indexOf(last_message_id) != -1) {
                            unread_counter = list_message_ids.length - list_message_ids.indexOf(last_message_id) - 1;
                        } else {
                            unread_counter = -1;
                        }
                        // console.log("Current messages in collection: ", messages)
                        chat_history.innerHTML = "";
                        last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
                        last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
                        renderOldMessages(messages);
                    }, (error) => {
                        console.log(error);
                    });

                    onSnapshot(query(docRoomByRoomId(room_id)), (room) => {
                        if (room.exists()) {
                            // console.log("Changed from system " + system_data.id + " to " + room.data().participants[1] ?? "null");
                            // system_data.id = room.data().participants[1] ?? null;
                            system_data.id = room.data().participants[1];
                            lastid = room.data().lastid;

                            let doc_admin = doc(fs, 'users_gozic', system_data.id);
                            getDoc(doc_admin).then((doc_admin_data) => {
                                if (doc_admin_data.exists()) {
                                    system_data.token = doc_admin_data.data().token;
                                    // console.log(system_data.token);
                                }
                            })

                            if ($("textarea#message").is(":focus") && lastid == system_data.id && room.data().unread) {
                                const update_room = {
                                    "unread": 0,
                                };
                                updateDoc(docRoomByRoomId(room_id), update_room);
                                localStorage.setItem("last_message_id", list_message_ids.length > 0 ? list_message_ids[list_message_ids.length - 1] : "");
                            }

                            // console.log("Changed from thread " + (thread_id ? thread_id : "null") + " to " + ((room.data().threadId && room.data().threadId != "") ? room.data().threadId : "null"));
                            
                            if (room.data().threadId && room.data().threadId != "") {
                                thread_id = room.data().threadId;
                                bot_ready = true;
                                $(".header-title").text("Chat (bạn đang chat với trợ lý ảo)");
                            } else {
                                thread_id = null;
                                $(".header-title").text("Chat");
                            }

                            if (room.data().lastid == system_data.id && room.data().unread) {
                                if (unread_counter <= 0 || unread_counter > 99) {
                                    // Code when retrieve only a part of all message
                                } else {
                                    window.parent.postMessage({
                                        topic: "UNREAD COUNTER",
                                        message: "",
                                        data: {
                                            unread_counter: unread_counter
                                        }
                                    }, '*');
                                    $("#popup-iframe-gozic").attr("data-unread-counter", unread_counter).addClass("alert-new-message");
                                }
                            } else {
                                window.parent.postMessage({
                                    topic: "UNREAD COUNTER",
                                    message: "",
                                    data: {
                                        unread_counter: 0
                                    }
                                }, '*');
                                $("#popup-iframe-gozic").removeClass("alert-new-message").removeAttr("data-unread-counter");
                            }
                        }
                    })

                    $("#customer-info").remove(); // Disable customer's info collection form if is an old customer
                }
            });
        })
    }
    if (user_data != null) checkUser();

    $(document).ready(function() {
        $("textarea#message").focus(function() {
            if (lastid == system_data.id) {
                const update_room = {
                    "unread": 0,
                };
                updateDoc(docRoomByRoomId(room_id), update_room);
                localStorage.setItem("last_message_id", list_message_ids.length > 0 ? list_message_ids[list_message_ids.length - 1] : "");
            }
        });
    })

    /* 
    *
    * Document elements & functions
    * 
    */
    var chat_history = document.querySelector("#chat-history");
    var last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
    var last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
    var message_input = document.querySelector("#chat-input textarea#message");
    var default_input_height = message_input.offsetHeight;
    
    var md = window.markdownit();
    var defaultRender = md.renderer.rules.link_open || function (tokens, idx, options, env, self) {
        return self.renderToken(tokens, idx, options);
    };
    md.renderer.rules.link_open = function (tokens, idx, options, env, self) {
        tokens[idx].attrSet('target', '_blank');
        return defaultRender(tokens, idx, options, env, self);
    };

    scrollToLastMessage();

    async function sendMessage(event) {
        event.preventDefault();
        if (message_input.value.trim() == '') {
            return;
        }
        addMessage(message_input.value.trim());
        message_input.value = "";
        message_input.style.height = default_input_height;
    }

    function renderMessage(message, is_reply=false, reply, avatar_url = avatar_list) {
        // console.log(is_reply, reply, message);
        let message_content = "";
        switch (message.type) {
            case "image":
                if (message.content.length == 1) {
                    message_content = "<img src='" + message.content[0] + "'>";
                } else {
                    message_content = "<div class='row w-100 m-0 images-message'>";
                    message.content.forEach((img_src, index) => {
                        message_content +=
                        "<div class='col-6 p-1 d-flex align-items-center justify-content-center'>" +
                            "<img src='" + img_src + "' class='img-fluid'>" +
                        "</div>";
                    });
                    message_content += "</div>";
                }
                break;
            case "markdown":
                message_content = "<div class='chat-markdown'>" + md.render(message.content) + "</div>";
                break;
            default:
                message_content = "<span>" + message.content.replace(/\n/g,"<br>") + "</span>";
                break;
        }

        let reply_content = "";
        if (is_reply) {
            if (reply.type == "text") reply_content = "<div class='reply-note'><span>" + shortenStringDisplay(reply.message, 30) + "</span></div>";
            else {
                reply_content = "<div class='reply-note row w-100 images-message'>";
                reply.images.forEach((img_src, index) => {
                    reply_content +=
                    "<div class='col-6 p-1 d-flex align-items-center justify-content-center'>" +
                        "<img src='" + img_src + "' class='img-fluid'>" +
                    "</div>";
                })
                reply_content += "</div>";
            }
        }
        // console.log(message_content);
        if (last_message_list && last_message_list.classList.contains("chat-" + message.role)) {
            last_message_list.querySelector(".chat-message-list").innerHTML +=
                "<div class='w-100'>" +
                    reply_content +
                    "<div class='chat-message " + message.role + "-message'>" +
                        message_content +
                        "<span class='position-absolute message-reply' id='" + message.id + "' data-message-type='" + message.type + "'>" +
                            "<i class='fas fa-fw fa-reply'></i>" +
                        "</span>" +
                    "</div>" +
                "</div>";
        } else {
            chat_history.innerHTML +=
                "<div class='chat-detail chat-" + message.role + "'>" +
                    "<div class='chat-avatar " + message.role + "-avatar'>" +
                        "<img src='" + avatar_url[message.role] + "'>" +
                    "</div>" +
                    "<div class='chat-message-list'>" +
                        "<div class='w-100'>" +
                            reply_content +
                            "<div class='chat-message " + message.role + "-message'>" +
                                message_content +
                                "<span class='position-absolute message-reply' id='" + message.id + "' data-message-type='" + message.type + "'>" +
                                    "<i class='fas fa-fw fa-reply'></i>" +
                                "</span>" +
                            "</div>" +
                        "</div>" +
                    "</div>";
                "</div>";
        }
        last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
        last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
    }

    // Re-render old messages
    function renderOldMessages(messages) {
        messages.forEach((message) => {
            let message_data = {
                id: message.id,
                type: "text",
                content: message.data().chat,
                role: ""
            };
            if (message.data().images.length > 0) {
                message_data.type = "image";
                message_data.content = message.data().images;
            }
            if (message.data().check == CHECK_TYPE.CHAT_BOT) {
                message_data.type = "markdown";
            }

            let is_reply = [CHECK_TYPE.REPLY_CHAT_TEXT, CHECK_TYPE.REPLY_CHAT_IMAGES, CHECK_TYPE.REPLY_CHAT_PRODUCT].includes(message.data().check) ?? false;
            let reply_data = {
                type: "text",
                message: message.data().reply,
                images: []
            }
            if ([CHECK_TYPE.REPLY_CHAT_IMAGES].includes(message.data().check)) {
                reply_data.type = "image";
                reply_data.message = "";
                reply_data.images = message.data().replyimages;
            }
            switch (message.data().senderId) {
                case 0:
                    message_data.role = "system";
                    renderMessage(message_data, is_reply, reply_data);
                    break;
                case system_data.id:
                    message_data.role = "system";
                    renderMessage(message_data, is_reply, reply_data);
                    break;
                case user_data.id:
                    message_data.role = "user";
                    renderMessage(message_data, is_reply, reply_data);
                    break;
            }
        });
        let loadedImages = 0;
        $(".chat-message-list img").on('load error', function () {
            loadedImages++;
            // Check if all images are loaded because of using link src
            if (loadedImages === $(".chat-message-list img").length) {
                scrollToLastMessage();
            }
        });
    }

    //Handle file input
    $(document).ready(function () {
        $('input#image').change(async function () {
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            var input = this;

            if (input.files && input.files[0]) {
                const options = {
                    strict: true,
                    maxWidth: 1920,
                    maxHeight: 1920,
                    quality: 0.8,
                    mimeType: 'image/jpeg',
                    success(result) {
                        // console.log(result);
                    },
                    error(e) {
                        console.error(e.message);
                    },
                };
                let promises = [];
                const compressor = new ImageCompressor();

                // console.log(input.files)

                Array.from(input.files).map((file, index) => {
                    // Check image file extension
                    if (allowedExtensions.test(file.name)) {
                        // console.log(file)
                        promises.push(compressor.compress(file, options));
                    } else {
                        alert('Chỉ chấp nhận file ảnh có đuôi .jpg, .jpeg hoặc .png');
                        $(this).val(null);
                        return;
                    }
                })

                // Compress images
                const compressedImages = await Promise.all(promises);
                console.log(compressedImages)

                // Upload compressed images
                let image_list = [];
                if (!([CHECK_TYPE.REPLY_CHAT_TEXT, CHECK_TYPE.REPLY_CHAT_IMAGES, CHECK_TYPE.REPLY_CHAT_PRODUCT].includes(reply.check))) {
                    reply.check = CHECK_TYPE.CHAT_IMAGES;
                }
                for (const result of compressedImages) {
                    try {
                        const url = await uploadFile(new File([result], result.name, { type: result.type })); // Wait for each request to complete
                        image_list.push(url); // Push the response image link to the result array
                    } catch (error) {
                        console.log("Upload images failed: " + error);
                    }
                }
                // console.log(image_list)
                addMessage(image_list, "image");
            }
            $(this).val(null);
            resetReply();
        });
    });

    // Pre-processing
    function scrollToLastMessage() {
        if (last_message) $("#chat-history .chat-detail:last-child .chat-message-list .w-100:last-child .chat-message:last-child")[0].scrollIntoView({ behavior: "auto" });
    }

    $(document).ready(function() {
        message_input.oninput = function () {
            message_input.style.height = default_input_height + "px";
            message_input.style.height = (message_input.scrollHeight) + "px";
        };

        message_input.onkeypress = function (event) {
            if (event.which === 13 && !event.shiftKey) {
                event.preventDefault();
                sendMessage(event);
            }
        };
    });

    // Handle reply message
    $(document).on('click', ".message-reply", function() {
        reply.id = $(this).prop("id");
        reply.name = $(this).parent().hasClass("user-message") ? user_data.name : system_data.name;
        $("#reply-detail #reply-name").text(shortenStringDisplay(reply.name, 18));
        switch ($(this).data("message-type")) {
            case "image":
                reply.check = CHECK_TYPE.REPLY_CHAT_IMAGES;
                reply.reply = "";
                reply.images = $(this).parent(".chat-message").find("img").map(function() {
                    return $(this).prop("src");
                }).get();
                $("#reply-detail #reply-content").text("[Hình ảnh]");
                let reply_images = "";
                reply.images.forEach((img_src, index) => {
                    reply_images += 
                    "<div class='col-6 p-1 d-flex align-items-center justify-content-center'>" +
                        "<img src='" + img_src + "' class='img-fluid'>" +
                    "</div>";
                })
                $("#reply-images").html(reply_images).addClass('d-show');
                break;
            case "text":
                reply.check = CHECK_TYPE.REPLY_CHAT_TEXT;
                reply.reply = $(this).parent(".chat-message").children("span").eq(0).text();
                reply.images = [];
                $("#reply-detail #reply-content").text(shortenStringDisplay(reply.reply, 30));
                $("#reply-images").removeClass('d-show');
                $("#chat-history").removeClass('with-reply');
                break;
        }
        $("#chat-history").addClass('with-reply');
        $("#chat-reply").addClass('d-flex');
    })
    function resetReply() {
        reply.id = "";
        reply.reply = "";
        reply.check = CHECK_TYPE.CHAT_TEXT;
        reply.images = [];
        reply.name = "";
        $("#reply-images").removeClass('d-show');
        $("#chat-history").removeClass('with-reply');
        $("#chat-reply").removeClass('d-flex');
    }
    $(document).on('click', "#reply-cancel", function() {
        resetReply();
    })

    // Handle chat toggle
    $(document).ready(function () {
        // Handle click icon popup
        $("#popup-iframe-gozic").click(function () {
            window.top.postMessage('showChat', '*');
            if (!showChat) {
                $(".chat-container").animate({
                    scale: 1
                }, "fast");
                $("textarea#message").focus();
                showChat = true;
                scrollToLastMessage();
            } else {
                $(".chat-container").animate({
                    scale: 0
                }, "fast", function() {
                    window.top.postMessage('hideChat', '*');
                });
                showChat = false;
            }
        })

        $("#close-chat-btn").click(function () {
            $(".chat-container").animate({
                scale: 0
            }, "fast", function() {
                window.top.postMessage('hideChat', '*');
            });
            showChat = false;
        })

        $("#chat-input").click(function() {
            $("textarea#message").focus();
        })

        // Handle user info form event
        $("#customer-info select").change(function() {
            $(this).css("color", "black");
        });

        $("#customer-info input[name='name']").on("focusout", function() {
            if ($(this).val() == "") {
                $(this).prop("placeholder", "Name is required!").addClass("invalid");
            }
        })
        $("#customer-info input[name='name']").on("focusin", function() {
            $(this).prop("placeholder", "Enter your name").removeClass("invalid");
        })

        $("#customer-info input[name='phone']").on("focusout", function() {
            let pattern = /^\d+$/;
            if ($(this).val() == "" || !pattern.test($(this).val())) {
                $(this).prop("placeholder", "Phone number is required!").addClass("invalid");
            }
        })
        $("#customer-info input[name='phone']").on("focusin", function() {
            $(this).prop("placeholder", "Enter your phone number").removeClass("invalid");
        })

        $("#customer-info button").click(function() {
            let is_valid = true;
            if ($("#customer-info input[name='name']").val() == "") {
                $("#customer-info input[name='name']").prop("placeholder", "Name is required!").addClass("invalid");
                is_valid = false;
                $("#customer-info input[name='name']").focus();
            }
            let pattern = /^\d+$/;
            if ($("#customer-info input[name='phone']").val() == "" || !pattern.test($("#customer-info input[name='phone']").val())) {
                $("#customer-info input[name='phone']").prop("placeholder", "Phone number is required!").addClass("invalid");
                is_valid = false;
                $("#customer-info input[name='phone']").focus();
            }

            if (is_valid) {
                $(this).prop("disabled", true);
                getToken(messaging, { serviceWorkerRegistration: swRegistration, vapidKey: system_data.vapid }).then((currentToken) => {
                    if (currentToken) {
                        user_data = {
                            id: Date.now() + makeid(8),
                            name: $("#customer-info input[name='name']").val(),
                            phone: $("#customer-info input[name='phone']").val(),
                            avatar_url: avatar_list.user,
                            created_at: new Date(),
                            token: currentToken
                        }
                        const doc_user = doc(fs, 'users_gozic', user_data.id.toString());
                        setDoc(doc_user, {
                            date_time: new Date(),
                            id: user_data.id,
                            name: user_data.name,
                            token: user_data.token
                        });

                        localStorage.setItem("user_data", JSON.stringify(user_data)); // Temporary save user data for getting old messages
                        localStorage.setItem("system_data", JSON.stringify(system_data)); // Temporary save system data for getting old messages
                        
                        room_id = system_data.id + "_" + user_data.id;
                        doc_room = doc(fs, 'chat_rooms_gozic', room_id);
                        setDoc(doc_room, {
                            "lastchat": firstMessage(user_data),
                            "lastid": system_data.id,
                            "participants": [
                                user_data.id,
                                system_data.id
                            ],
                            "receiverAvatar": user_data.avatar_url,
                            "receiverId": user_data.id,
                            "receiverName": user_data.name,
                            "receiverRoleId": 1,
                            "senderAvatar": system_data.avatar_url,
                            "senderId": system_data.id,
                            "senderName": system_data.name,
                            "senderRoleId": 10,
                            "timestamp": new Date(),
                            "unread": 0,
                            "threadId": ""
                        }).then(() => {
                            col_room = collection(doc_room, "chat")
                            addDoc(col_room, {
                                "askimg": "",
                                "chat": firstMessage(user_data),
                                "check": 1,
                                "id": "",
                                "images": [],
                                "price": 0,
                                "receiverId": user_data.id,
                                "receiverName": user_data.name,
                                "receiverToken": "",
                                "reply": "",
                                "replyimages": [],
                                "senderId": system_data.id,
                                "senderName": system_data.name,
                                "senderPhone": system_data.phone,
                                "timestamp": new Date(),
                                "title": ""
                            }).then((doc) => {
                                localStorage.setItem("last_message_id", doc.id);
                                checkUser();
                                $("#customer-info").fadeOut();
                                $("textarea#message").focus();
                            });
                        })
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                    $("#customer-info button").text("Chưa cấp quyền thông báo").css({color: "white", backgroundColor: "red"});
                });
            }
        });
    });

    // Handle click image
    $(document).on('click', '.images-message > div', function() {
        $("body").append(popupImage($(this).children("img").eq(0).prop("src")));
    })
    $(document).on('click', '.chat-message > img', function() {
        console.log($(this), $(this).prop("src"))
        $("body").append(popupImage($(this).prop("src")));
    })
    function popupImage(img_src) {
        return "<div id='popup-image' style='position: absolute; top: 0; left: 0; width: 100vw; height: 100vh; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(225, 225, 225, 0.6);'>" +
            "<div class='container-fluid h-100 p-5 d-flex align-items-center justify-content-center'>" +
                "<img src='" + img_src + "' style='max-width: 100%; max-height: 100%;'>" +
            "</div>" +
            "<div style='position: absolute; top: 2rem; right: 2rem'>" +
                "<div>" +
                    "<button id='close-popup-image' style='width: 36px; height: 36px; border: 1px solid lightgray; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center;'><i class='fas fa-times' style='font-size: x-large;'></i></button>" +
                "</div>" +
            "</div>" +
        "</div>";
    }
    $(document).on('click', 'button#close-popup-image', function() {
        $("div#popup-image").remove();
    })
</script>

</html>