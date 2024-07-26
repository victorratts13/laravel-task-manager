<x-filament-panels::page>
    <script src="{{ asset('/js/core/core.js') }}"></script>
    <div style="display: flex; justify-content: start;">
        <x-avatar xl rounded="rounded-[1.25rem]" :src="asset($commandSource?->image)" style="width: 20vw; margin-right: 30px;"
            border="none" />
        <div style="display: flex; width: 100%; position: relative; flex-wrap: wrap;">
            <div
                style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                <div class="col hide-in-smarphone">
                    <div style="display: flex;">
                        <x-heroicon-c-command-line style="width: 30px; margin-right: 10px;" /> Command
                    </div>
                </div>
                <div class="col hide-in-desktop">
                    <x-heroicon-c-command-line style="width: 30px" />
                </div>
                <div class="col">
                    {{ $record->command }}
                </div>
            </div>
            <div
                style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                <div class="col hide-in-smarphone">
                    <div style="display: flex;">
                        <x-heroicon-c-key style="width: 30px; margin-right: 10px;" /> PID
                    </div>
                </div>
                <div class="col hide-in-desktop">
                    <x-heroicon-c-key style="width: 30px" />
                </div>
                <div class="col">
                    {{ $record->pid }}
                </div>
            </div>
            <div
                style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                <div class="col hide-in-smarphone">
                    <div style="display: flex;">
                        <x-heroicon-c-tag style="width: 30px; margin-right: 10px;" /> Tag
                    </div>
                </div>
                <div class="col hide-in-desktop">
                    <x-heroicon-c-tag style="width: 30px" />
                </div>
                <div class="col">
                    {{ $record->tag }}
                </div>
            </div>
            <div
                style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                <div class="col hide-in-smarphone">
                    <div style="display: flex;">
                        <x-heroicon-s-clock style="width: 30px; margin-right: 10px;" /> Last Exec.
                    </div>
                </div>
                <div class="col hide-in-desktop">
                    <x-heroicon-s-clock style="width: 30px" />
                </div>
                <div class="col">
                    {{ date('d/m/Y H:i', strtotime($record->last_execution)) }}
                </div>
            </div>
            <div
                style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                <div class="col hide-in-smarphone">
                    <div style="display: flex;">
                        <x-heroicon-c-adjustments-horizontal style="width: 30px; margin-right: 10px;" /> Interval
                    </div>
                </div>
                <div class="col hide-in-desktop">
                    <x-heroicon-c-adjustments-horizontal style="width: 30px" />
                </div>
                <div class="col">
                    {{ $record->interval }}Sec.
                </div>
            </div>
        </div>
    </div>
    @if ($record->logs()->count() > 0)
        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5 "
            style="border-radius: 5px;">
            <thead class="divide-y divide-gray-200 dark:divide-white/5">
                <tr class="bg-gray-50 dark:bg-white/5">
                    <th scope="col"
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-command">
                        Date/time</th>
                    <th scope="col"
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-command">
                        output</th>
                    <th scope="col"
                        class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-command">
                        service</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                @foreach ($record->logs()->get() as $item)
                    <tr
                        class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-id"
                            style="text-align: center;">
                            {{ date('d/m/Y', strtotime($item->created_at)) }}
                        </td>
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-id"
                            style="text-align: center;">
                            @if (strlen($item->output) > 30)
                                {{ substr($item->output, 0, 29) }}...
                            @else
                                {{ item->output }}
                            @endif
                        </td>
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-id" style="text-align: center;">
                            {{ $item->command }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <h1 style="margin: auto; font-size: 30px; display: flex;">
            <x-heroicon-s-information-circle style="width: 30px; margin-right: 10px;" /> No logs for this command
        </h1>
        <img src="{{ asset('/no-content.png') }}" style="margin: auto;" alt="no-content" />
    @endif
</x-filament-panels::page>
