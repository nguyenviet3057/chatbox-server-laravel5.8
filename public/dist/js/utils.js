function formatTimestampDisplay(time) {
    let time_diff_s = Math.floor((Date.now() - time)/1000);
    // console.log(time_diff_s);
    let represent = ""; 
    let time_represents = [
        {
            "time": 60*30,
            "represent": "30m ago"
        },
        {
            "time": 60*15,
            "represent": "15m ago"
        },
        {
            "time": 60*10,
            "represent": "10m ago"
        },
        {
            "time": 60*5,
            "represent": "5m ago"
        },
        {
            "time": 60,
            "represent": "1m ago"
        },
        {
            "time": 30,
            "represent": "30s ago"
        },
        {
            "time": 15,
            "represent": "15s ago"
        },
        {
            "time": 0,
            "represent": "just now"
        }
    ];
    if (60*60*36 <= time_diff_s) {
        let date = new Date(time);
        return date.getFullYear() + "/" + (date.getMonth() < 9 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1)) + "/" + (date.getDate() < 10 ? "0" + date.getDate() : date.getDate());
    }
    // if (60*60*24 <= time_diff_s) return "yesterday"; 
    if (60*60 <= time_diff_s) return Math.floor(time_diff_s / (60*60)) + "h ago";
    represent = time_represents[time_represents.findIndex(time_represent => time_diff_s >= time_represent.time)].represent;
    return represent;
}

function autoUpdateTimeRepresent() {
    setInterval(() => {
        // console.log("hi");
        $(".message-timestamp").each((index, element) => {
            let time_display = formatTimestampDisplay($(element).data("timestamp"));
            // console.log(time_display);
            $(element).html($(element).data("read") ? time_display : ("<b>" + time_display + "</b>"));
        })
    }, 15000);
}

function shortenStringDisplay(text, length) {
    if (text.length <= length) return text;
    return text.substr(0, length) + "...";
}

function reverseObjectKeys(originalObject) {
    const originalKeys = Object.keys(originalObject);
    const reversedKeys = originalKeys.reverse();
  
    const reversedObject = {};
  
    for (const key of reversedKeys) {
      reversedObject[key] = originalObject[key];
    }
  
    return reversedObject;
}

function makeid(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
    }
    return result;
}

// Upload image API
function uploadFile(file) {
    return new Promise((resolve, reject) => {
        // if (file != null) {
        //     if (!file.name.match(/.(jpg|jpeg|png|gif|bmp|webp)$/i))
        //         return alert('HĂ¬nh áº£nh báº¡n táº£i lĂªn khĂ´ng Ä‘Ăºng Ä‘á»‹nh dáº¡ng file, vui lĂ²ng kiá»ƒm tra láº¡i (jpg, jpeg, png, bmp, gif)');
        // }
        // if (file.size > (30 * 1024 * 1024)) { 
        //     return alert('Dung lÆ°á»£ng file áº£nh quĂ¡ lá»›n -> YĂªu cáº§u file dung lÆ°á»£ng <= 30M') 
        // }
        var url = '//gozic.vn/api/upload'
        // var url = '//appbanhang.gozic.vn/api/upload'
        // var url = '//localhost:8000/api/upload'
        var xhr = new XMLHttpRequest();
        var fd = new FormData();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function (e) {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var obj = JSON.parse(xhr.responseText)
                // console.log(obj);
                if (obj.status == 0) {
                    alert(obj.msg)
                    return
                }
                // callback(obj.url)
                resolve(obj.url);
            }
        };
        fd.append('tags', 'browser_upload');
        if (file != null) {
            fd.append('file', file);
        }
        fd.append('isWaterMask', false);
        fd.append('watermask', "");
        fd.append('idCat', 0);
        fd.append('color', "");
        fd.append('enabled', 1);
        xhr.send(fd);
    });
}

function shortenStringDisplay(text, length) {
    text = '' + text;
    if (text.length <= length) return text;
    return text.substr(0, length) + "...";
}