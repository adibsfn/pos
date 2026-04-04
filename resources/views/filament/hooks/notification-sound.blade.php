<audio id="notif-sound" preload="auto">
    <source src="https://www.soundjay.com/buttons_c2026/sounds/button-09a.mp3" type="audio/mpeg">
</audio>

<script>
    let lastCount = null;
    let audioUnlocked = false;

    const sound = document.getElementById('notif-sound');

    document.addEventListener('click', () => {
        if (!audioUnlocked) {
            sound.play().then(() => {
                sound.pause();
                sound.currentTime = 0;
                audioUnlocked = true;
                // console.log('🔓 Audio unlocked');
            }).catch(() => {});
        }
    }, { once: true });

    async function checkNotifications() {
        try {
            const res = await fetch('/notif-count');
            if (!res.ok) return;

            const count = await res.json();

            // console.log('notif count:', count);

            if (lastCount === null) {
                lastCount = count;
                return;
            }

            if (count > lastCount) {
                // console.log('🔥 notif baru terdeteksi');

                if (audioUnlocked) {
                    sound.currentTime = 0;
                    sound.play().then(() => {
                        // console.log('🔔 bunyi notif!');
                    }).catch((e) => {
                        // console.log('❌ gagal play:', e);
                    });
                } else {
                    // console.log('❌ audio belum unlock');
                }
            }

            lastCount = count;

        } catch (err) {
            console.log('error notif:', err);
        }
    }

    setInterval(checkNotifications, 5000);
</script>
