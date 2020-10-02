
// Initialize Firebase
/*var config = {
    apiKey: "AIzaSyC3Afm6nUD2OvT6ITkU2jc6wkTnl3YEfEk",
    authDomain: "vaceatecom-1533615062029.firebaseapp.com",
    databaseURL: "https://vaceatecom-1533615062029.firebaseio.com",
    projectId: "vaceatecom-1533615062029",
    storageBucket: "vaceatecom-1533615062029.appspot.com",
    messagingSenderId: "751470557847"
};
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.usePublicVapidKey("BCoZv1gCxOF84UsanCz3fVDh822GlvQj-UoUsRkA3AlsnDbiJ12fzJLgAIJ94EHMkVeAP4tncbS1socQvBOvM2Y");

messaging.requestPermission().then(function () {
    console.log('Notification permission granted.');
    
    // subsequent calls to getToken will return from cache.
    messaging.getToken().then(function (currentToken) {
        if (currentToken) {
            sendTokenToServer(currentToken);
            updateUIForPushEnabled(currentToken);
        } else {
            // Show permission request.
            console.log('No Instance ID token available. Request permission to generate one.');
            // Show permission UI.
            updateUIForPushPermissionRequired();
            setTokenSentToServer(false);
        }
    }).catch(function (err) {
        console.log('An error occurred while retrieving token. ', err);
        showToken('Error retrieving Instance ID token. ', err);
        setTokenSentToServer(false);
    });
}).catch(function (err) {
    console.log('Unable to get permission to notify.', err);
});*/


self.BASE_URL = '/#';
self.addEventListener('install', event => event.waitUntil(self.skipWaiting()));
self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));

self.addEventListener('notificationclick', function (event) {
    event.stopImmediatePropagation();
    event.notification.close();
    if (event.notification && event.notification.data.action) {
        const page = event.notification.data.action;
        let openInNewWindow = event.notification.data.openInNewWindow;
        let url = page;
        if ((url.startsWith('http') || url.startsWith('https')) && (openInNewWindow !== false)) {
            openInNewWindow = true;
        } else {
            url = self.BASE_URL + url;
        }
        event.waitUntil(clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(activeClients => {
            if (activeClients.length > 0 && !openInNewWindow) {
                for (var i = 0; i < activeClients.length; i++) {
                    var client = activeClients[i];
                    if ('focus' in client)
                        return client.navigate(url).then(client => client.focus()).catch(() => clients.openWindow(url));
                }
            }
            return clients.openWindow(url);
        })
        );
    }
});

importScripts('https://www.gstatic.com/firebasejs/5.4.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.4.2/firebase-messaging.js');

firebase.initializeApp({
    'messagingSenderId': '751470557847'
});

// Retrieve an instance of Firebase Messaging so that it can handle background messages.
var messaging = firebase.messaging();




// Handle Background Notifications

// If you would like to customize notifications that are received in the background (Web app is closed or not in browser focus) then you should implement this optional method
messaging.setBackgroundMessageHandler(function (payload) {
    //console.log('[firebase-messaging-sw.js] Received background message ', payload);



    var options = JSON.parse(payload.data.notification);

    // Customize notification here
    var notificationTitle = options.title;
    var notificationOptions = options;




    var not = self.registration.showNotification(notificationTitle,
        notificationOptions);
    return not;
});


