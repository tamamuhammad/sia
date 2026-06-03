<div>
    <div class="fixed inset-0 z-50 flex bg-gray-50 dark:bg-gray-900">
        
        <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center relative" 
             style="background-image: url('{{ asset('img/hero-image.jpg') }}');">
            
            <div class="absolute inset-0 bg-primary-900/60 mix-blend-multiply"></div>
            
            <div class="absolute bottom-0 left-0 p-16 text-white w-full">
                <h1 class="text-5xl font-bold mb-2 tracking-tight">SIA Komplek L</h1>
                <p class="text-lg opacity-90 leading-tight max-w-lg font-thin tracking-widest">
                    Sistem Informasi Absensi <br>PP. Al-Munawwir Komplek L
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 relative">
            
            <div class="w-full max-w-md p-8 sm:p-10 rounded-3xl">
                
                <div class="text-center mb-8">
                    <img src="{{ asset('img/brand-logo.png') }}" alt="Logo" class="h-14 mx-auto mb-4 bg-white rounded-full p-0.5">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Silahkan masukkan email dan password Anda untuk mengakses dasbor</p>
                </div>

                <form wire:submit="authenticate">
                    
                    {{ $this->form }}

                    <div class="mt-6">
                        <x-filament::button type="submit" size="lg" class="w-full">
                            Masuk ke Dasbor
                        </x-filament::button>
                    </div>
                    
                </form>
                
            </div>
        </div>

    </div>
</div>