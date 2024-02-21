self.onnotificationclick = (event) => {
    // console.log("On notification click: ", event.notification.tag);
    event.notification.close();

    event.waitUntil(
        Promise.all([
            self.registration.getNotifications(),
            clients.matchAll({
                type: "window",
                includeUncontrolled: true,
            })
        ]).then(([notifications, clientList]) => {
            notifications.forEach(function(notification) {
                notification.close();
            });
      
            for (const client of clientList) {
                if (client.url.includes("/") && "focus" in client) {
                    return client.focus();
                }
            }
            
            if (clients.openWindow) {
                return clients.openWindow("/");
            }
        })
    );
};

importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyDw2S01aViwowyyJ-A0m7pVTX8OIZF2VJU",
    authDomain: "ziczacapp.firebaseapp.com",
    databaseURL: "https://ziczacapp-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "ziczacapp",
    storageBucket: "ziczacapp.appspot.com",
    messagingSenderId: "1054197522212",
    appId: "1:1054197522212:web:02a6765b198580e53f9db1",
    measurementId: "G-8XQG0CX88E"
});

const messaging = firebase.messaging();

// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// Keep in mind that FCM will still show notification messages automatically 
// and you should use data messages for custom notifications.
// For more info see: 
// https://firebase.google.com/docs/cloud-messaging/concept-options
// messaging.onBackgroundMessage(function(payload) {
//     console.log('[firebase-messaging-sw.js] Received background message ', payload);
//     // Customize notification here
//     const notificationTitle = 'Background Message Title';
//     const notificationOptions = {
//         body: 'Background Message body.',
//         icon: '/firebase-logo.png'
//     };

//     self.registration.showNotification(notificationTitle, notificationOptions);
// });
