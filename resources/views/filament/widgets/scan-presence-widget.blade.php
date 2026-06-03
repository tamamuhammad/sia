<x-filament-widgets::widget>
    <x-filament::section 
        icon="heroicon-o-viewfinder-circle" 
        heading="Live Scanner Presensi" 
        description="Arahkan kartu QR santri ke kamera untuk absensi secara otomatis."
    >
        <style>
            #widget-reader { border: none !important; }
            #widget-reader__dashboard_section_csr span { color: inherit !important; font-family: inherit !important; }
            #widget-reader__dashboard_section_swaplink { display: none !important; }
            #widget-reader__header_message { display: none !important; }
            #qr-shaded-region { border-color: rgba(16, 185, 129, 0.5) !important; } /* Kotak scan berwarna Emerald */
        </style>

        <div 
            x-data="{
                isProcessing: false,
                statusClass: 'p-4 rounded-xl ring-1 ring-gray-950/10 dark:ring-white/20 text-center',
                statusText: 'Siap menerima scan kartu...',
                
                initScanner() {
                    if (typeof Html5QrcodeScanner === 'undefined') {
                        let script = document.createElement('script');
                        script.src = 'https://unpkg.com/html5-qrcode';
                        script.onload = () => { this.startCamera(); };
                        document.head.appendChild(script);
                    } else {
                        this.startCamera();
                    }

                    window.addEventListener('scan-success', event => {
                        this.playBeep('success');
                        this.statusClass = 'p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 ring-1 ring-emerald-600/20 text-center';
                        this.statusText = event.detail.message;
                        setTimeout(() => { this.resetScanner(); }, 2000);
                    });

                    window.addEventListener('scan-warning', event => {
                        this.playBeep('error');
                        this.statusClass = 'p-4 rounded-xl bg-warning-50 dark:bg-warning-500/10 ring-1 ring-warning-600/20 text-center';
                        this.statusText = event.detail.message;
                        setTimeout(() => { this.resetScanner(); }, 2500);
                    });

                    window.addEventListener('scan-error', event => {
                        this.playBeep('error');
                        this.statusClass = 'p-4 rounded-xl bg-danger-50 dark:bg-danger-500/10 ring-1 ring-danger-600/20 text-center';
                        this.statusText = event.detail.message;
                        setTimeout(() => { this.resetScanner(); }, 2500);
                    });
                },

                startCamera() {
                    let html5QrcodeScanner = new Html5QrcodeScanner(
                        'widget-reader', 
                        { 
                            fps: 10, 
                            qrbox: { width: 280, height: 280 },
                            aspectRatio: 1.0,
                            showTorchButtonIfSupported: true
                        }, 
                        false
                    );
                    
                    html5QrcodeScanner.render((decodedText) => {
                        if (this.isProcessing) return;
                        this.isProcessing = true;

                        this.statusClass = 'p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 ring-1 ring-blue-600/20 text-center animate-pulse';
                        this.statusText = 'Memverifikasi kartu...';

                        $wire.processScan(decodedText);
                    });
                },

                resetScanner() {
                    this.isProcessing = false;
                    this.statusClass = 'p-4 rounded-xl bg-gray-50 dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 text-center';
                    this.statusText = 'Siap menerima scan kartu...';
                },

                playBeep(type) {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(type === 'success' ? 800 : 300, ctx.currentTime);
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + (type === 'success' ? 0.15 : 0.3));
                }
            }"
            x-init="initScanner()"
            class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start"
        >
            
            <div wire:ignore class="md:col-span-2 relative overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center ring-1 ring-gray-950/10 dark:ring-white/20" style="min-height: 350px;">
                <div id="widget-reader" class="absolute inset-0 flex flex-col justify-center" style="width: 100%"></div>
            </div>

            <div class="flex flex-col gap-4">
                
                <div :class="statusClass" class="transition-all duration-300 ease-in-out shadow-sm">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-200" x-text="statusText"></p>
                </div>

            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>