<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <a href="{{ App\Models\Updater::orderByDesc('id')->first()->repository }}" rel="noopener noreferrer"
                    style="display: flex; justify-content: space-between; width: 190px;" target="_blank">
                    <x-heroicon-c-check-circle style="width: 20px !important; color:#079574;" /> Laravel
                    Task-Manager
                </a>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ App\Models\Updater::orderByDesc('id')->first()->version }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-y-1">
                <x-filament::link color="gray" href="https://github.com/victorratts13/laravel-task-manager" icon="heroicon-m-book-open"
                    icon-alias="panels::widgets.filament-info.open-documentation-button" rel="noopener noreferrer"
                    target="_blank">
                    {{ __('filament-panels::widgets/filament-info-widget.actions.open_documentation.label') }}
                </x-filament::link>

                <x-filament::link color="gray" href="/manager"
                    icon-alias="panels::widgets.filament-info.open-github-button" rel="noopener noreferrer">
                    <x-slot name="icon">
                        <x-heroicon-c-cog />

                    </x-slot>

                    Manager
                </x-filament::link>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
