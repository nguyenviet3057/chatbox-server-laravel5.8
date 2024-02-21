let iframeContainer = document.createElement("div");
iframeContainer.id = 'gozic-widget';

let iframeAnimation = document.createElement("div");
iframeAnimation.id = 'gozic-widget-animation';

let iframeContent = document.createElement("iframe");
iframeContent.id = 'gozic-iframe';
// iframeContent.src = 'https://appbanhang.gozic.vn/module/chat-firebase/iframe';
iframeContent.src = 'http://localhost:8000/module/chat-firebase/iframe';
iframeContent.width = 60;
iframeContent.height = 60;
iframeContent.frameBorder = '0';
iframeContent.allowFullscreen = true;

// Config from server
let logo = "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg";
let welcome_title = "Welcome to Gozic";
let bottom = 50;
let right = 50;
let responsive_bottom = 10;
let responsive_right = 10;

iframeContent.setAttribute("data-logo", logo);
iframeContent.setAttribute("data-welcome-title", welcome_title);

iframeContainer.appendChild(iframeAnimation);
iframeContainer.appendChild(iframeContent);
document.body.appendChild(iframeContainer);
document.head.innerHTML += (
    `<style>
        #gozic-widget {
            position: fixed;
            bottom: ${bottom}px;
            right:  ${right}px;
            width: 60px;
            height: 60px;
            padding: 5px;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #gozic-widget-animation {
            position: absolute;
            background-color: #fdac7c !important;
            opacity: 0.25;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            box-shadow: 0px 0px 25px 4px #fdac7c;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #gozic-widget-animation::after {
            content: '';
            position: relative;
            width: 10px;
            height: 10px;
            border: 2px solid #ff5c00 !important;
            border-radius: 50%;
            animation: gozicAnimation 1s infinite linear;
        }
        @keyframes gozicAnimation {
            from {
                opacity: 0.5;
                padding: 10px;
            }
            to {
                opacity: 1;
                padding: 32px;
            }
        }
        #gozic-widget iframe {
            position: absolute;
            bottom: 0px;
            right: 0px;
            max-height: calc(90vh - ${bottom}px) !important;
            max-width: none !important;
        }
        @media screen and (max-width: 576px) {
            #gozic-widget {
                bottom: ${responsive_bottom}px;
                right: ${responsive_right}px;
            }
            #gozic-widget iframe {
                max-height: calc(80vh - ${responsive_bottom}px) !important;
                width: calc(100vw - ${(2*responsive_right)}px) !important;
            }
        }
    </style>`
)

// Toggle chat iframe size
let showChat = false;
window.onmessage = function(e) {
    if (e.data == 'showChat' && !showChat) {
        document.querySelector("div#gozic-widget > iframe").setAttribute("width", "440");
        document.querySelector("div#gozic-widget > iframe").setAttribute("height", "813");
        showChat = true;
    } else if (e.data == 'hideChat' && showChat) {
        document.querySelector("div#gozic-widget > iframe").setAttribute("width", "60");
        document.querySelector("div#gozic-widget > iframe").setAttribute("height", "60");
        showChat = false;
    }
};

// document.querySelector("iframe#gozic-iframe").onload = function() {
//     const data = {
//         logo: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
//         welcome_title: "Welcome to Gozic",
//         avatar_admin: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
//         avatar_system: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
//         avatar_user: "https://appbanhang.gozic.vn/uploads/stores/97/2024/01/download-removebg-preview.png",
//     }
//     document.querySelector("iframe#gozic-iframe").contentWindow.postMessage(data, '*');
// }