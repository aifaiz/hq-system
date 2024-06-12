<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <a
                    href="https://filamentphp.com"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    Commissions
                </a>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Unpaid
                </p>
            </div>

            <div class="flex flex-col items-end gap-y-1">
                RM {{$totalComm}}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>