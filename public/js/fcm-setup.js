// FCM Setup & Token Retrieval
function initFcm() {
    // 1. Firebase Config - Replace with your Firebase project's web configuration
    const firebaseConfig = {
        apiKey: "YOUR_API_KEY",
        authDomain: "YOUR_AUTH_DOMAIN",
        projectId: "YOUR_PROJECT_ID",
        storageBucket: "YOUR_STORAGE_BUCKET",
        messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
        appId: "YOUR_APP_ID"
    };

    // 2. Initialize Firebase
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    
    const messaging = firebase.messaging();

    // 3. Request Permission and get Device Token
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');

            // Get FCM Token. Replace YOUR_VAPID_KEY with the web push certificate key (VAPID Key) from the Firebase Console.
            messaging.getToken({ vapidKey: 'YOUR_VAPID_KEY' })
                .then((currentToken) => {
                    if (currentToken) {
                        console.log('FCM Token generated: ', currentToken);
                        sendTokenToServer(currentToken);
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.error('An error occurred while retrieving token: ', err);
                });
        } else {
            console.warn('Unable to get permission to notify.');
        }
    });

    // 4. Handle foreground notifications (when user is active on the page)
    messaging.onMessage((payload) => {
        console.log('Message received in foreground: ', payload);
        
        // Show notification using Toastr if available, otherwise fallback to alert
        if (window.toastr) {
            toastr.info(payload.notification.body, payload.notification.title);
        } else {
            alert(`${payload.notification.title}: ${payload.notification.body}`);
        }
    });
}

// 5. Send FCM Token to Laravel backend using Axios
function sendTokenToServer(token) {
    axios.post('/fcm-token', {
        fcm_token: token
    })
    .then(response => {
        console.log('FCM Token successfully stored on server: ', response.data);
    })
    .catch(error => {
        console.error('Error storing FCM Token on server: ', error);
    });
}

// Automatically trigger FCM initialization once the script loads
if (typeof firebase !== 'undefined') {
    initFcm();
} else {
    console.error('Firebase SDK not loaded. Please make sure to import firebase-app and firebase-messaging CDN scripts first.');
}
