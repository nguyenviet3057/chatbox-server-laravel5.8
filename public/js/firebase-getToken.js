import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";

const firebaseConfig = {
    // Firebase config from https://console.firebase.google.com/project/ziczacapp/settings/general (Web app)
}

const vapid = "BOdrLVZfmC0wgJc8PNMXS3ZY66jqpaU1KVee4Y1YNg5h8aEEHjbwcY4LqOPzzUc2h387XUsjsViaZfQuGpWWz9I" // Generated from https://console.firebase.google.com/project/ziczacapp/settings/cloudmessaging (Web configuration)

const app = initializeApp(firebaseConfig);
var messaging = getMessaging(app);

// Register Firebase service worker
var swRegistration;
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        if (registrations.length == 0) {
            navigator.serviceWorker.register('/plugins/chat/firebase-messaging-sw.js')
            .then(function(registration) {
                console.log('Service Worker registered:', registration);
                swRegistration = registration;
                if (Notification.permission === 'granted') checkUser();
            })
            .catch(function(error) {
                console.error('Service Worker registration failed:', error);
            });
        } else {
            swRegistration = registrations[0];
            if (Notification.permission === 'granted') checkUser();
        }
    });
} else {
    console.log('Service Worker is not supported.');
}

// Get Token from Firebase FCM
getToken(messaging, { serviceWorkerRegistration: swRegistration, vapidKey: vapid })
.then((currentToken) => {
    if (currentToken) {
        console.log(currentToken) // Token generated from Firebase
    } else {
        console.log('No registration token available. Request permission to generate one.');
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
});