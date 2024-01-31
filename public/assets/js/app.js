// Import modules
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-analytics.js";
import { getAuth, connectAuthEmulator } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
// import { getDatabase, connectDatabaseEmulator, ref, child, push, get, set, update, serverTimestamp, onValue, off, query, orderByChild, equalTo, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";
import { getFirestore, connectFirestoreEmulator, collection, orderBy, getDocs, doc, getDoc, addDoc, setDoc, updateDoc, serverTimestamp, onSnapshot, query, where } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

// Initialize Firebase
// const firebaseConfig = {
//     apiKey: "AIzaSyB7OEUgaGaipy8nWbAYXacrLNNNVEYZm_4",
//     authDomain: "chat-bot-ec322.firebaseapp.com",
//     databaseURL: "https://chat-bot-ec322-default-rtdb.asia-southeast1.firebasedatabase.app",
//     projectId: "chat-bot-ec322",
//     storageBucket: "chat-bot-ec322.appspot.com",
//     messagingSenderId: "765269828752",
//     appId: "1:765269828752:web:c173315fcc8095e82b6415",
//     measurementId: "G-52EMCT879B"
// };
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
// Use emulators
// connectDatabaseEmulator(db, 'localhost', 9000);
// connectAuthEmulator(auth, "http://127.0.0.1:9099");
// connectFirestoreEmulator(fs, '127.0.0.1', 8082);
// const system_data = {
//     id: "S10001",
//     name: "Nguyen Admin",
//     email: "admin@gmail.com",
//     gender: 1,
//     avatarUrl: "/assets/image/system.jpg",
// }
let customer_data = {};
let avatar_list = {
    "admin": "/assets/image/icon-gozic.png",
    "system": "/assets/image/icon-gozic.png"
}
$(document).ready(function() {
    $("p.firebase-emulator-warning").remove();
})

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

// Declare markdown converter object
var md = window.markdownit();
// Remember the old renderer if overridden, or proxy to the default renderer.
var defaultRender = md.renderer.rules.link_open || function (tokens, idx, options, env, self) {
    return self.renderToken(tokens, idx, options);
};
md.renderer.rules.link_open = function (tokens, idx, options, env, self) {
    // Add a new `target` attribute, or replace the value of the existing one.
    tokens[idx].attrSet('target', '_blank');

    // Pass the token to the default renderer.
    return defaultRender(tokens, idx, options, env, self);
};

// Declair common variables
var current_room_list = [];
var waiting_room_list = [];
var is_show_all_current = false;
var is_show_all_waiting = false;
var room_id = null; // current selected room
var max_room = 3;

var reply = {
    id: "",
    check: 1,
    reply: "",
    name: "",
    images: []
}

// scrollToLastMessage();

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

function renderMessage(message, is_reply=false, reply, avatar_url = avatar_list) {
    console.log(is_reply, reply);
    let message_content = "";
    switch (message.type) {
        case "image":
            message_content = "<img src='" + message.content + "'>";
            break;
        case "markdown":
            message_content = "<div>" + md.render(message.content) + "</div>";
            break;
        default:
            message_content = "<span>" + message.content + "</span>";
            break;
    }

    let reply_content = "";
    if (is_reply) {
        if (reply.type == "text") reply_content = "<div class='reply-note'><span>" + shortenStringDisplay(reply.message, 30) + "</span></div>";
        else reply_content = "<div class='reply-note'><img src='" + reply.images[0] +"'></div>";
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
function renderOldMessage(messages, participants=[]) {
    // console.log(messages, participants);
    messages.forEach((message) => {
        console.log(message.data())
        let message_data = {
            id: message.id,
            type: "text",
            content: message.data().chat,
            role: ""
        };
        if (message.data().images.length > 0) {
            message_data.type = "image";
            message_data.content = message.data().images[0];
        }

        let is_reply = message.data().check != 1 ?? false;
        let reply_data = {
            type: "text",
            message: message.data().reply,
            images: []
        }
        if ([3, 4, 5, 6].includes(message.data().check)) {
            console.log()
            reply_data.type = "image";
            reply_data.message = "";
            reply_data.images = message.data().replyimages;
        }
        switch (message.data().senderId) {
            case 0:
                message_data.role = "system";
                renderMessage(message_data, is_reply, reply_data);
                break;
            case participants[1]:
                message_data.role = "system";
                renderMessage(message_data, is_reply, reply_data);
                break;
            case participants[0]:
                message_data.role = "user";
                renderMessage(message_data, is_reply, reply_data);
                break;
        }
    });
    let loadedImages = 0;
    $("#chat-history img").on('load', function () {
        loadedImages++;
        // Check if all images are loaded because of using link src
        if (loadedImages === $("#chat-history img").length) {
            scrollToLastMessage();
        }
    });
}

// Re-render old rooms
async function renderOldRooms(room_list=[], message_nav_container) {
    let message_container_element = "";
    room_list.forEach((room) => {
        message_container_element += 
            "<li class='row d-flex align-items-center w-100 chat-list m-0 p-0' id='" + room.id + "'>" +
                "<div class='col-xs-2 thumbnail-user d-flex align-items-center justify-content-center'>" +
                    "<img src='" + room.customer.avatar_url + "'>" +
                "</div>" +
                "<div class='col-xs-10 d-flex justify-content-center flex-column'>" +
                    "<div class='message-info d-flex justify-content-between'>" +
                        "<span class='fw-bold user-name'>" + shortenStringDisplay(room.customer.name, 12) + "</span>" +
                        "<span class='message-timestamp' data-timestamp='" + room.timestamp.seconds + "' data-unread='" + room.unread + "'>" + ((room.unread) ? ("<b>" + room.time_display + "</b>") : room.time_display) + "</span>" +
                    "</div>" +
                    "<span class='message-content'>" + ((room.unread) ? ("<b>" + room.message_content + "</b>") : room.message_content) + "</span>" +
                "</div>" +
            "</li>";
        }
    );
    message_nav_container.innerHTML = message_container_element;
}
autoUpdateTimeRepresent();

function scrollToLastMessage() {
    if (last_message) $("#chat-history .chat-detail:last-child .chat-message-list .w-100:last-child .chat-message:last-child")[0].scrollIntoView({ behavior: "auto" });
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
            // console.log(current_room_list);
            if (current_room_list.filter(current_room => current_room.id == room_id).length == 1) {
                console.log("Pass")
                $(".chat-overlay").remove();
                for (var item of document.querySelectorAll(".chat-list")) {
                    item.classList.remove('active');
                }
                targetElement.classList.add("active");
                syncMessage(room_id);
            } else {
                let choice = confirm("Are you sure to enter this room?");
                if (choice) {
                    $(".chat-overlay").remove();
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

$(document).on("click", "div#show-all-current", function() {
    showAllCurrent();
});
$(document).on("click", "div#show-less-current", function() {
    showLessCurrent();
});

$(document).on("click", "div#show-all-waiting", function() {
    showAllWaiting();
});
$(document).on("click", "div#show-less-waiting", function() {
    showLessWaiting();
});

function showAllCurrent() {
    // console.log("show all current");
    $("#show-all-current").removeClass('d-flex').addClass('d-none');
    if (is_show_all_waiting) {
        showLessWaiting();
    }

    let section_status_span = $("#current-rooms-span").height() * 2;
    let section_chat_room_height = $("div.content-container").height();
    let section_current_height = $("#current-container").height();
    let section_waiting_height = $("#waiting-container").height();
    if (current_room_list.length > max_room) {
        $("#current-container").css({ "height": (section_chat_room_height - section_waiting_height - section_status_span) + "px", "overflow-y": "auto", "max-height": "none" });
        $("#show-less-current").removeClass('d-none').addClass('d-flex');
    } else {
        $("#current-container").css({ "height": "fit-content", "max-height": (section_current_height + section_status_span/2 < section_chat_room_height/2) ? "none" : "calc(50% - " + (section_status_span/2) + "px)" });
        $("#show-less-current").removeClass('d-flex').addClass('d-none');
    }
    message_nav_container_current.innerHTML = "";
    renderOldRooms(current_room_list, message_nav_container_current);

    is_show_all_current = true;
}

function showLessCurrent() {
    // console.log("show less current");
    let section_status_span = $("#current-rooms-span").height() * 2;
    let section_chat_room_height = $("div.content-container").height();
    let section_current_height = $("#current-container").height();
    let section_waiting_height = $("#waiting-container").height();
    $("#show-less-current").removeClass('d-flex').addClass('d-none');
    message_nav_container_current.innerHTML = "";
    $("#current-container").css({ "height": "fit-content", "max-height": (section_current_height + section_status_span/2 < section_chat_room_height/2) ? "none" : "calc(50% - " + (section_status_span/2) + "px)" });
    if (current_room_list.length > max_room) {
        renderOldRooms(current_room_list.slice(0, max_room), message_nav_container_current);
        $('#show-all-current').removeClass('d-none').addClass('d-flex');
    } else {
        renderOldRooms(current_room_list, message_nav_container_current);
        $('#show-all-current').removeClass('d-flex').addClass('d-none');
    }

    is_show_all_current = false;
}

function showAllWaiting() {
    // console.log("show all waiting");
    $("#show-all-waiting").removeClass('d-flex').addClass('d-none');
    if (is_show_all_current) {
        showLessCurrent();
    }

    let section_status_span = $("#current-rooms-span").height() * 2;
    let section_chat_room_height = $("div.content-container").height();
    let section_current_height = $("#current-container").height();
    let section_waiting_height = $("#waiting-container").height();
    if (waiting_room_list.length > max_room) {
        $("#waiting-container").css({ "height": (section_chat_room_height - section_current_height - section_status_span) + "px", "overflow-y": "auto", "max-height": "none" });
        $("#show-less-waiting").removeClass('d-none').addClass('d-flex');
    } else {
        $("#waiting-container").css({ "height": "fit-content", "max-height": (section_waiting_height + section_status_span/2 < section_chat_room_height/2) ? "none" : "calc(50% - " + (section_status_span/2) + "px)" });
        $("#show-less-waiting").removeClass('d-flex').addClass('d-none');
    }
    message_nav_container_waiting.innerHTML = "";
    renderOldRooms(waiting_room_list, message_nav_container_waiting);

    is_show_all_waiting = true;
}

function showLessWaiting() {
    // console.log("show less waiting");
    let section_status_span = $("#current-rooms-span").height() * 2;
    let section_chat_room_height = $("div.content-container").height();
    let section_current_height = $("#current-container").height();
    let section_waiting_height = $("#waiting-container").height();
    $("#show-less-waiting").removeClass('d-flex').addClass('d-none');
    message_nav_container_waiting.innerHTML = "";
    $("#waiting-container").css({ "height": "fit-content", "max-height": (section_waiting_height + section_status_span/2 < section_chat_room_height/2) ? "none" : "calc(50% - " + (section_status_span/2) + "px)" });
    if (waiting_room_list.length > max_room) {
        renderOldRooms(waiting_room_list.slice(0, max_room), message_nav_container_waiting);
        $('#show-all-waiting').removeClass('d-none').addClass('d-flex');
    } else {
        renderOldRooms(waiting_room_list, message_nav_container_waiting);
        $('#show-all-waiting').removeClass('d-flex').addClass('d-none');
    }

    is_show_all_waiting = false;
}

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
                            uploadFile(new File([result], result.name, { type: result.type }), (url) => {
                                if (reply.check == 4) {
                                    reply.check = 3;
                                    reply.images.push(url ?? "");
                                }
                                addMessage(url, "image");
                            });
                            // addMessage("/assets/image/image" + Math.floor(Math.random() * 7 + 1) + ".jpg", "image");
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
const col_rooms = collection(fs, "chat_rooms_gozic");
const col_messages = collection(fs, "messages");
const col_users = collection(fs, 'users_gozic')

// Base query
const docRoomByRoomId = (room_id) => {
    return doc(col_rooms, room_id);
}
const colMessageByRoomId = (room_id) => {
    let doc_room = docRoomByRoomId(room_id);
    return collection(doc_room, 'chat');
}

// Add new message
function addMessage(message, message_type="text") {
    const col_chat = collection(fs, "chat_rooms_gozic", room_id, "chat");

    const doc_chat_data = {
        "askimg": "",
        "chat": message,
        "check": reply.check,
        "id": "",
        "images": message_type == "text" ? [] : [
            message
        ],
        "price": 0,
        "receiverId": customer_data.id,
        "receiverName": customer_data.name,
        "receiverToken": "",
        "reply": reply.reply,
        "replyimages": reply.images,
        "senderId": system_data.id,
        "senderName": system_data.name,
        "senderPhone": system_data.phone,
        "timestamp": new Date(),
        "title": ""
    };
    addDoc(col_chat, doc_chat_data);

    const update_room = {
        "lastchat": message,
        "lastid": system_data.id,
        "receiverAvatar": customer_data.avatar_url,
        "receiverId": customer_data.id,
        "receiverName": customer_data.name,
        "receiverRoleId": 1,
        "timestamp": new Date(),
        "unread": 0,
        "threadId": ""
    };
    updateDoc(docRoomByRoomId(room_id), update_room);

    resetReply();
}

// Sync rooms in real-time with database
onSnapshot(query(col_rooms, orderBy('timestamp')), async (snapshot) => {
    console.log("changed")
    current_room_list = [];
    waiting_room_list = [];
    const rooms = snapshot.docs;
    // console.log(rooms)
    await Promise.all(rooms.map(async (room) => {
        console.log(room.data());
        let room_data = room.data();
        room_data.id = room.id;
        let doc_room = await getDoc(doc(fs, 'chat_rooms_gozic', room.id));
        room_data.message_content = "<span>" + shortenStringDisplay(doc_room.data().lastchat, 18) + "</span>";
        room_data.time_display = formatTimestampDisplay(doc_room.data().timestamp.seconds);
        if (doc_room.data().senderId == 0 || (doc_room.data().senderId == system_data.id && doc_room.data().senderName == system_data.name)) {
            room_data.customer = {
                name: doc_room.data().receiverName,
                avatar_url: doc_room.data().receiverAvatar
            }
        } else if (doc_room.data().receiverId == 0 || doc_room.data().receiverId == system_data.id && doc_room.data().receiverName == system_data.name) {
            room_data.customer = {
                name: doc_room.data().senderName,
                avatar_url: doc_room.data().senderAvatar
            }
        }
        // console.log(room_data);
        if (room.data().participants[1] == system_data.id) {
            // Auto read for current selected chat room
            if (room.id == room_id) {
                // if (unsubscribe_message != null) unsubscribe_message();
                // console.log(room_id);
                const update_room_data = {
                    "unread": 0,
                };
                updateDoc(docRoomByRoomId(room_id), update_room_data);
                current_room_list.push(room_data);
            } else {
                current_room_list.push(room_data);
            }
        } else {
            waiting_room_list.push(room_data);
        }
    }));
    // console.log(current_room_list, waiting_room_list)
    // message_nav_container_current.innerHTML = "";
    // message_nav_container_waiting.innerHTML = "";

    current_room_list = current_room_list.reverse();
    waiting_room_list = waiting_room_list.reverse();

    if (is_show_all_current) showAllCurrent();
    else showLessCurrent();
    if (is_show_all_waiting) showAllWaiting();
    else showLessWaiting();
}, (error) => {
    console.log(error);
});

// Sync message in real-time with database
var unsubscribe_message = null;
function syncMessage(room_id) {
    if (unsubscribe_message != null) unsubscribe_message();
    const doc_room = doc(fs, 'chat_rooms_gozic', room_id);
    let participants = null;
    getDoc(doc_room).then((room) => {
        if (room.exists()) {
            if (room.data().senderId == 0 || (room.data().senderId == system_data.id && room.data().senderName == system_data.name)) {
                customer_data = {
                    id: room.data().receiverId,
                    name: room.data().receiverName,
                    avatar_url: room.data().receiverAvatar
                }
            } else if (room.data().receiverId == 0 || room.data().receiverId == system_data.id && room.data().receiverName == system_data.name) {
                customer_data = {
                    id: room.data().senderId,
                    name: room.data().senderName,
                    avatar_url: room.data().senderAvatar
                }
            }
        
            const update_room = {
                "participants": [
                    customer_data.id,
                    system_data.id
                ],
                "unread": 0
            };
            participants = update_room.participants;
            // console.log(participants)
            avatar_list['user'] = customer_data.avatar_url;
            updateDoc(doc_room, update_room);
            // console.log(room_id);
            getDocs(query(colMessageByRoomId(room_id), orderBy("timestamp"))).then((messages_doc) => {
                const messages = messages_doc.docs;
                // console.log(messages);
                chat_history.innerHTML = "";
                last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
                last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
                renderOldMessage(messages, participants);
            });
            unsubscribe_message = onSnapshot(query(colMessageByRoomId(room_id), orderBy("timestamp")), (snapshot) => {
                const messages = snapshot.docs;
                // console.log(messages);
                chat_history.innerHTML = "";
                last_message_list = document.querySelector("#chat-history .chat-detail:last-child");
                last_message = document.querySelector("#chat-history .chat-detail:last-child .chat-message:last-child");
                renderOldMessage(messages, participants);
            })
        }
    }, (error) => {
        console.log(error);
    });
}

// Handle reply message
$(document).on('click', ".message-reply", function() {
    reply.id = $(this).prop("id");
    reply.name = $(this).parent().hasClass("user-message") ? customer_data.name : system_data.name;
    $("#reply-detail #reply-name").text(shortenStringDisplay(reply.name, 18));
    switch ($(this).data("message-type")) {
        case "image":
            reply.check = 4;
            reply.reply = "";
            reply.images = [
                $(this).parent(".chat-message").children("img").eq(0).prop("src")
            ];
            $("#reply-detail #reply-content").text("[Hình ảnh]");
            $("#reply-image").addClass('d-show').prop("src", $(this).parent(".chat-message").children("img").eq(0).prop("src"));
            break;
        case "text":
            reply.check = 2;
            reply.reply = $(this).parent(".chat-message").children("span").eq(0).text();
            reply.images = [];
            $("#reply-detail #reply-content").text(shortenStringDisplay(reply.reply, 30));
            $("#reply-image").removeClass('d-show');
            $("#chat-history").removeClass('with-reply');
            break;
    }
    $("#chat-history").addClass('with-reply');
    $("#chat-reply").addClass('d-flex');
})
function resetReply() {
    reply.id = "";
    reply.reply = "";
    reply.check = 1;
    $("#reply-image").removeClass('d-show');
    $("#chat-history").removeClass('with-reply');
    $("#chat-reply").removeClass('d-flex');
}
$(document).on('click', "#reply-cancel", function() {
    resetReply();
})