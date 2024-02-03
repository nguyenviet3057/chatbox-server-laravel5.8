window.onload = function() {
    console.log("abc")
    document.querySelector("body").innerHTML += (
        "<div id='gozic-widget'>" +
            "<iframe src='https://appbanhang.gozic.vn/module/chat-firebase/iframe' width='60' height='60' frameborder='0' allowfullscreen></iframe>" +
        "</div>");
    document.querySelector("head").innerHTML += (
        "<style>" +
            "#gozic-widget {" +
                "position: fixed;" +
                "bottom: 50px;" +
                "right: 50px;" +
                "width: 60px;" +
                "height: 60px;" +
                "padding: 5px;" +
                "z-index: 9999;" +
            "}" +
            "#gozic-widget iframe {" +
                "position: absolute;" +
                "bottom: 0px;" +
                "right: 0px;" +
                "max-height: 80vh;" +
            "}" +
            "@media screen and (max-width: 576px) {" +
                "#gozic-widget {" +
                    "bottom: 10px;" +
                    "right: 10px;" +
                "}" +
                "#gozic-widget iframe {" +
                    "max-height: 80vh;" +
                    "width: calc(100vw - 20px);" +
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
}