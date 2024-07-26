<x-filament-panels::page>
    <x-card title="PS AUX | 5s uptime" style="overflow: auto; width: 100%;">
        <div style="display: flex; width: 100%; position: relative; flex-wrap: wrap;">
            @foreach ($psaux as $item)
                <div
                    style="display: flex; justify-content: space-between; width: 100%; border: dashed 2px; height: 40px; padding: 5px; border-radius: 5px; font-size: 20px; margin-bottom: 5px; position: relative;">
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->pid }}
                    </span> 
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->user }}
                    </span>
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->cpu }}
                    </span>
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->mem }}
                    </span>
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->command }}
                    </span>
                    <span style="diplay: flex; justify-conntent: start;">
                        {{ $item->time }}
                    </span>
                </div>
            @endforeach
        </div>
    </x-card>
</x-filament-panels::page>
