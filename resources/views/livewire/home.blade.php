<div style="max-width: 500px; margin: auto; position: relative; top: 15vh; padding: 10px;">
    <x-avatar xl rounded="rounded-[1.25rem]" :src="asset('/app.png')"
        style="margin: auto; display: flex; justify-content: center; width: 60px; margin-bottom: 10px;" />
    <x-filament-widgets::widget class="fi-filament-info-widget">
        <x-filament::section>
            <div class="flex items-center gap-x-3" style="padding: 20px;">
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
    @foreach (App\Models\ServiceProccess::paginate(10) as $item)
        <x-filament-widgets::widget class="fi-filament-info-widget" style="margin-top: 10px;">
            <x-filament::section>
                <div class="flex items-center gap-x-3" style="padding: 20px;">
                    <div class="flex-1">
                        <a href="/manager/service-proccesses"
                            rel="noopener noreferrer"
                            style="display: flex; justify-content: left; width: 190px;" target="_blank">
                            <x-heroicon-m-command-line style="width: 20px !important; color:#093462; margin-right: 10px;" />
                            @if (strlen($item->command) > 20)
                                {{ substr( $item->command, 0, 19) }}...
                            @else
                                {{ $item->command }}
                            @endif
                        </a>

                        @if($item->status)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" style="color: #3d7a00;">
                            Start
                        </p>
                        @else
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" style="color: #7a0000;">
                            Stop
                        </p>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-y-1">
                        <x-filament::link color="gray" href="/manager/service-logs" icon="heroicon-c-bolt" icon-alias="panels::widgets.filament-info.open-documentation-button" rel="noopener noreferrer" target="_blank">
                            {{ $item->tag }}
                        </x-filament::link>

                        <x-filament::link color="gray" href="/manager/service-logs" icon-alias="panels::widgets.filament-info.open-github-button" rel="noopener noreferrer">
                            <x-slot name="icon">
                                <x-heroicon-s-clock />
                            </x-slot>

                            {{ date('d/m/Y H:i', strtotime($item->created_at)) }}
                        </x-filament::link>
                    </div>
                </div>
            </x-filament::section>
        </x-filament-widgets::widget>
    @endforeach
    {!! App\Models\ServiceProccess::paginate(10)->links() !!}
</div>
