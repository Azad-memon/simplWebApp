<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>
    <audio id="notifySound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>
<audio id="silentSound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>

<script>
    // Laravel config se Firebase values
    const firebaseConfig = {
        apiKey: "{{ config('services.firebase.apiKey') }}",
        authDomain: "{{ config('services.firebase.authDomain') }}",
        projectId: "{{ config('services.firebase.projectId') }}",
        storageBucket: "{{ config('services.firebase.storageBucket') }}",
        messagingSenderId: "{{ config('services.firebase.messagingSenderId') }}",
        appId: "{{ config('services.firebase.appId') }}",
        measurementId: "{{ config('services.firebase.measurementId') }}",
    };
 //console.log(firebaseConfig);
    // Init Firebase


function askNotificationAndSoundPermission() {
    Swal.fire({
        title: "Enable Order Notifications & Sound?",
        text: "You will receive browser alerts with sound for new orders.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Enable",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {


            const silentAudio = document.getElementById("silentSound");
            silentAudio.play().then(() => {
                console.log("✅ Sound autoplay unlocked.");
                silentAudio.pause();
                silentAudio.currentTime = 0;
            }).catch(err => {
                console.error("❌ Could not unlock sound:", err);
            });


            Notification.requestPermission().then((permission) => {
                if (permission === "granted") {
                    console.log("✅ Notifications allowed!");


                    new Notification("Order Notifications", {
                        body: "You will now get order alerts with sound.",
                        icon: "https://em-cdn.eatmubarak.pk/restaurant_new/55087/logo/1628165998.png"
                    });


                    document.getElementById("notifySound").play();
                } else {
                    Swal.fire("Notifications Blocked", "Please allow from browser settings.", "error");
                }
            });
        }
    });
}
// Call this function on dashboard load or first user action
//askNotificationAndSoundPermission();



    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();


    function initFirebaseMessagingRegistration() {
        Notification.requestPermission().then((permission) => {
            console.log(permission);
            if (permission === "granted") {
                messaging.getToken({ vapidKey: "{{ config('services.firebase.vapidKey') }}" })
                    .then((token) => {
                        // Send this token to server
                        $.ajax({
                            url: '{{ route("admin.save.fcm.token") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                fcm_token: token
                            },
                            success: function (response) {
                                console.log('FCM token saved successfully.');
                            },
                            error: function (err) {
                                console.log('Could not save FCM token.', err);
                            },
                        });
                       // console.log("FCM Token:", token);


                    })
                    .catch((err) => {
                        console.log("Token error: ", err);
                    });
            }
        });
    }

    // Foreground notification
    messaging.onMessage(function (payload) {
        console.log("Message received. ", payload);
        new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: payload.notification.icon,
        });
    const div = document.createElement("div");
    div.innerHTML = `<strong>${payload.notification.title}</strong><br>${payload.notification.body}`;
    div.className = "custom-popup";
    document.body.appendChild(div);

    // Play sound
    const audio = new Audio('/sounds/notify.mp3');
    audio.play();
    });

    // Call function
    initFirebaseMessagingRegistration();
</script>
