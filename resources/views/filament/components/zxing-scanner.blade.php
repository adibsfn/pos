<div style="margin-top:20px; position:relative;">
    <video id="video" style="width:100%; max-width:500px; border-radius:10px;"></video>

    <!-- overlay -->
    {{-- <div id="scan-overlay"></div> --}}
</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>

<script>
let codeReader = null;

window.startZXingScanner = async function () {

    const beep = new Audio("https://actions.google.com/sounds/v1/alarms/beep_short.ogg");

    codeReader = new ZXing.BrowserMultiFormatReader();

    try {
        const devices = await codeReader.listVideoInputDevices();

        if (!devices.length) {
            alert("Kamera tidak ditemukan");
            return;
        }

        const selectedDeviceId = devices[0].deviceId;

        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {

            if (result) {

                console.log("✅ HASIL:", result.text);

                beep.play();

                let input = null;

                // ✅ CREATE (ID pasti ada)
                input = document.querySelector('#form\\.barcode');

                // ✅ EDIT (modal)
                if (!input) {
                    let modal = document.querySelector('[role="dialog"]');
                    if (modal) {
                        input = modal.querySelector('input[name*="barcode"]');
                    }
                }

                console.log("INPUT TERPILIH:", input);

                if (input) {
                    input.value = result.text;

                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // stop supaya tidak double scan
                codeReader.reset();
            }

            if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
            }

        });

    } catch (error) {
        console.error("ERROR CAMERA:", error);
    }
};
</script>

{{-- <style>
#scan-overlay {
    position: absolute;
    top: 60%;
    left: 50%;
    width: 80%;
    height: 120px;
    transform: translate(-50%, -50%);
    border: 3px solid red;
    border-radius: 10px;
    pointer-events: none;
}
</style> --}}
