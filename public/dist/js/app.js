// Import modules
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-analytics.js";
import { getAuth, connectAuthEmulator } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
import { getDatabase, connectDatabaseEmulator, ref, child, push, get, set, update, serverTimestamp, onValue, off, query, orderByChild, equalTo, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";
import { getFirestore, connectFirestoreEmulator, collection, addDoc, serverTimestamp, onSnapshot, query, where } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

// Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyB7OEUgaGaipy8nWbAYXacrLNNNVEYZm_4",
    authDomain: "chat-bot-ec322.firebaseapp.com",
    databaseURL: "https://chat-bot-ec322-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "chat-bot-ec322",
    storageBucket: "chat-bot-ec322.appspot.com",
    messagingSenderId: "765269828752",
    appId: "1:765269828752:web:c173315fcc8095e82b6415",
    measurementId: "G-52EMCT879B"
};
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
var db = getDatabase(app);
var auth = getAuth(app);
var fs = getFirestore(app);
// Use emulators
connectDatabaseEmulator(db, 'localhost', 9000);
connectAuthEmulator(auth, "http://127.0.0.1:9099");
connectFirestoreEmulator(fs, "127.0.0.1:8082")
const user_data = {
    uid: "S10001",
    name: "Nguyen Admin",
    email: "admin@gmail.com",
    gender: 1,
    avatarUrl: "assets/image/system.jpg",
}
const avatar_list = {
    "admin": "assets/image/system.jpg",
    "system": "assets/image/system.jpg",
    "user": "assets/image/user.jpg",
}

/* 
*
* Document elements & functions
* 
*/
// Elements for manage rooms
var message_nav_container_current = document.querySelector("#message-nav ul#current-rooms");
var message_nav_container_waiting = document.querySelector("#message-nav ul#waiting-rooms");

// Elements for manage chat
var chat_history = document.querySelector("#chat-history");
var last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
var last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
var message_input = document.querySelector("#chat-input textarea#message");
var default_input_height = message_input.scrollHeight;

// Declair common variables
var current_room_list = [];
var waiting_room_list = [];
var room_id = null; // current selected room

scrollToLastMessage();

function sendMessage(event) {
    event.preventDefault();
    if (message_input.value.trim() == '') {
        return;
    }
    addMessage(message_input.value.trim());
    message_input.value = "";
    message_input.style.height = default_input_height;
}

// document.querySelector("#submit-btn").addEventListener("click", (event) => sendMessage(event, this));

function renderMessage(message_id, message_type="text", message, type="user", avatarUrl=avatar_list) {
    // console.log(message_type);
    let message_content = (message_type == "image") ? ("<img id='" + message_id + "' src='" + message + "'>") : ("<span id='" + message_id + "'>" + message + "</span>");
    // console.log(message_content);
    if (last_message_list && last_message_list.classList.contains("chat-" + type)) {
        last_message_list.querySelector(".chat-message-list").innerHTML +=
            "<div class='chat-message " + type + "-message'>" +
                message_content +
            "</div>";
    } else {
        chat_history.innerHTML +=
            "<div class='chat-detail chat-" + type + "'>" +
                "<div class='chat-avatar " + type + "-avatar'>" +
                    "<img src='" + avatarUrl[type] + "'>" +
                "</div>" +
                "<div class='chat-message-list'>" +
                    "<div class='chat-message " + type + "-message'>" +
                        message_content +
                    "</div>" +
                "</div>" +
            "</div>";
    }
    last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
    last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
}

// Re-render old messages
function renderOldMessage(messages) {
    if (messages.exists()) {
        messages.forEach((message) => {
            switch (message.val().type) {
                case "admin":
                    renderMessage(message.key, message.val().message_type, message.val().message, "system");
                    break;
                case "system":
                    renderMessage(message.key, message.val().message_type, message.val().message, "system");
                    break;
                case "user":
                    renderMessage(message.key, message.val().message_type, message.val().message, "user");
                    break;
            }
        });
        scrollToLastMessage();
    } else {
        console.log("No message");
    }
}

// Re-render old rooms
function renderOldRooms(room_list=[], message_nav_container) {
    if (room_list.length > 0) {
        room_list.forEach((room) => {
            // console.log(room.key, except_rooms);
            if (room.val().newest_message) {
                let message_content = (room.val().newest_message_type == "image") ? "<span><i>(gửi hình ảnh)</i></span>" : ("<span>" + shortenStringDisplay(room.val().newest_message, 25) + "</span>");
                let time_display = formatTimestampDisplay(room.val().created_at);
                message_nav_container.innerHTML += 
                    "<li class='row w-100 chat-list m-0 p-0' id='" + room.key + "'>" +
                        "<div class='col-2 thumbnail-user d-flex align-items-center justify-content-center'>" +
                            "<img src='" + room.val().avatar_url + "'>" +
                        "</div>" +
                        "<div class='col-10 d-flex justify-content-center flex-column'>" +
                            "<div class='message-info d-flex justify-content-between'>" +
                                "<span class='fw-bold user-name'>" + shortenStringDisplay(room.val().user_name, 18) + "</span>" +
                                "<span class='message-timestamp' data-timestamp='" + room.val().created_at + "' data-read='" + room.val().is_read + "'>" + ((room.val().is_read) ? time_display : ("<b>" + time_display + "</b>")) + "</span>" +
                            "</div>" +
                            "<span class='message-content'>" + ((room.val().is_read) ? message_content : ("<b>" + message_content + "</b>")) + "</span>" +
                        "</div>" +
                    "</li>";
            }
        })
    }
}
autoUpdateTimeRepresent();

function scrollToLastMessage() {
    if (last_message) document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child").scrollIntoView({ behavior: "auto" });
}

message_input.oninput = function() {
    message_input.style.height = default_input_height + "px";
    message_input.style.height = (message_input.scrollHeight) + "px";
};

message_input.onkeypress = function(event) {
    if(event.which === 13 && !event.shiftKey) {
        event.preventDefault();
        sendMessage(event);
    }
};

// Catch choosing room event
document.getElementById('message-nav').addEventListener('click', function(event) {
    let targetElement = event.target;
    while (targetElement && targetElement !== document) {
        if (targetElement.classList.contains("chat-list")) {
            room_id = targetElement.id;
            console.log(current_room_list);
            if (current_room_list.filter(current_room => current_room.key == room_id).length == 1) {
                console.log("Pass")
                for (var item of document.querySelectorAll(".chat-list")) {
                    item.classList.remove('active');
                }
                targetElement.classList.add("active");
                syncMessage(room_id);
            } else {
                let choice = confirm("Are you sure to enter this room?");
                if (choice) {
                    for (var item of document.querySelectorAll(".chat-list")) {
                        item.classList.remove('active');
                    }
                    targetElement.classList.add("active");
                    syncMessage(room_id);
                }
            }
            break;
        }
        targetElement = targetElement.parentNode;
    }
});

//Handle file input
$(document).ready(function() {
    $('input#image').change(function() {
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        var input = this;

        if (input.files && input.files[0]) {
            console.log(input.files[0])
            // Check image file extension
            if (allowedExtensions.test(input.files[0].name)) {

                // Compress image
                const options = {
                    strict: true,
                    maxWidth: 1920,
                    maxHeight: 1920,
                    quality: 0.8,
                    mimeType: 'image/jpeg',
                    success(result) {
                        console.log(result);
                        var reader = new FileReader();
                        reader.onloadend = function(e) {
                            // uploadFile(new File([result], result.name, { type: result.type }), (url) => {
                            //     addMessage(url, "image");
                            // });
                            addMessage("assets/image/image" + Math.floor(Math.random() * 7 + 1) + ".jpg", "image");
                        };
                        reader.readAsDataURL(result);
                    },
                    error(e) {
                        console.error(e.message);
                    },
                };

                new ImageCompressor(input.files[0], options);
            } else {
                alert('Chỉ chấp nhận file ảnh có đuôi .jpg, .jpeg hoặc .png');
                $(this).val('');
            }

            
        }
    });
});

/*
*
*   Collections/Refs && Utils
*
*/
// Refs
const ref_rooms = ref(db, "rooms");
const ref_messages = ref(db, "messages")

// Base query
const queryMessageByRoomId = (room_id) => {
    return query(ref(db, 'messages'), orderByChild('room_id'), equalTo(room_id));
}
const queryRoom = () => {
    return query(ref(db, 'rooms'), orderByChild('created_at'));
}

// Add new message
function addMessage(message, message_type="text") {
    let new_message = push(ref_messages);
    set(new_message, {
        "room_id": room_id,
        "type": "admin",
        "system_id": user_data.uid,
        "message_type": message_type,
        "message": message,
        "is_read": false,
        "created_at": Date.now()
    });
}

// Sync rooms in real-time with database
onValue(queryRoom(), (rooms) => {
    current_room_list = [];
    waiting_room_list = [];
    rooms.forEach((room) => {
        if (room.val().system_id == user_data.uid) {
            // Auto read for current selected chat room
            if (!room.val().is_read && room.key == room_id) {
                if (unsubscribe_message != null) unsubscribe_message();
                console.log(room_id);
                const ref_selected_room = ref(db, "rooms/" + room_id);
                const update_room_data = {
                    "is_read": true,
                };
                update(ref_selected_room, update_room_data);
            } else {
                current_room_list.push(room);
            }
        } else {
            waiting_room_list.push(room);
        }
    });
    console.log(current_room_list, waiting_room_list)
    message_nav_container_current.innerHTML = "";
    message_nav_container_waiting.innerHTML = "";

    current_room_list = current_room_list.reverse();
    waiting_room_list = waiting_room_list.reverse();

    renderOldRooms(current_room_list, message_nav_container_current);
    renderOldRooms(waiting_room_list, message_nav_container_waiting);
}, (error) => {
    console.log(error);
});

// Sync message in real-time with database
var unsubscribe_message = null;
function syncMessage(room_id) {
    if (unsubscribe_message != null) unsubscribe_message();
    const ref_selected_room = ref(db, "rooms/" + room_id);
    const update_room_data = {
        "is_read": true,
        "system_id": user_data.uid
    };
    update(ref_selected_room, update_room_data);
    unsubscribe_message = onValue(queryMessageByRoomId(room_id), (messages) => {
        chat_history.innerHTML = "";
        last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
        last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
        renderOldMessage(messages);
    }, (error) => {
        console.log(error);
    });
}

// Check user is whether exist or new
// await get(query(ref(db, 'rooms'), orderByChild('system_id'), equalTo(user_data.uid))).then((rooms) => {
//     if (rooms.exists()) {
//         // Get existed room
//         rooms.forEach((room) => {
//             room_id = room.key;
//             current_room_list.push(room_id);
//         });
//         console.log(current_room_list);
//     } else {
//         // console.log("New user, making new room...");
//         // // Generate new room
//         // let new_room = push(ref_rooms);
//         // room_id = new_room.key;
//         // console.log(room_id);
//         // set(new_room, {
//         //     "user_id": user_data.uid
//         // })
//     }
//     }).catch((error) => {
//         console.error("Error: ", error);
//     });
