$(document).ready(function() {
    $("body").append(
        "<div id='gozic-widget'>" +
            "<iframe src='http://127.0.0.1:5500/gozic_widget.html' width='60' height='60' frameborder='0' allowfullscreen></iframe>" +
        "</div>");
    $("head").append(
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
            $("div#gozic-widget > iframe").prop("width", "440").prop("height", "813");
            showChat = true;
        } else if (e.data == 'hideChat' && showChat) {
            $("div#gozic-widget > iframe").prop("width", "60").prop("height", "60");
            showChat = false;
        }
    };
})