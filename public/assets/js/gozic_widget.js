
    let iframeContainer = document.createElement("div");
    iframeContainer.id = 'gozic-widget';
    let iframeContent = document.createElement("iframe");
    iframeContent.id = 'gozic-iframe';
    iframeContent.src = 'https://appbanhang.gozic.vn/module/chat-firebase/iframe';
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
    
    iframeContainer.appendChild(iframeContent);
    document.body.appendChild(iframeContainer);
    document.head.innerHTML += (
        "<style>" +
            "#gozic-widget {" +
                "position: fixed;" +
                "bottom: " + bottom + "px;" +
                "right: " + right + "px;" +
                "width: 60px;" +
                "height: 60px;" +
                "padding: 5px;" +
                "z-index: 9999;" +
            "}" +
            "#gozic-widget iframe {" +
                "position: absolute;" +
                "bottom: 0px;" +
                "right: 0px;" +
                "max-height: calc(90vh - " + bottom + "px);" +
            "}" +
            "@media screen and (max-width: 576px) {" +
                "#gozic-widget {" +
                    "bottom: " + responsive_bottom + "px;" +
                    "right: " + responsive_right + "px;" +
                "}" +
                "#gozic-widget iframe {" +
                    "max-height: calc(80vh - " + responsive_bottom + "px);" +
                    "width: calc(100vw - " + (2*responsive_right) + "px);" +
                "}" +
            "}" +
        "</style>"
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

    document.querySelector("iframe#gozic-iframe").onload = function() {
        const data = {
            logo: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
            welcome_title: "Welcome to Gozic",
            avatar_admin: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
            avatar_system: "https://cdn.gokisoft.com/uploads/stores/97/2024/01/583143640.jpg",
            avatar_user: "https://appbanhang.gozic.vn/uploads/stores/97/2024/01/download-removebg-preview.png",
        }
        document.querySelector("iframe#gozic-iframe").contentWindow.postMessage(data, '*');
    }