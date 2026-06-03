<div class="absolute bottom-3 -ml-2" style="width: var(--sidebar-width); padding-right: calc(var(--spacing) * 12);">
    <form action="{{ filament()->getLogoutUrl() }}" method="post" class="w-full">
        @csrf
        <button 
            type="submit" 
            class="flex items-center w-full gap-3 px-2 py-2 text-sm font-medium text-gray-700 transition duration-75 rounded-lg hover:bg-gray-100 focus:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5"
        >
            <x-heroicon-o-arrow-right-start-on-rectangle class="w-6 h-6 text-gray-400 dark:text-gray-500" />
            
            <span class="flex-1 text-left truncate">
                Keluar
            </span>
        </button>
    </form>
</div>