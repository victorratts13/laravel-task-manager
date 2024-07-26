/**
 * Laravel Task-manager
 * Core functions
 * Version: 1.1.0
 * channel: stable
 */

document.addEventListener('livewire:init', () => {
    console.log('core loaded');
    Livewire.on('page-process-details', (event) => {
        console.log(event);
        Livewire.dispatch('determine-command-type', {command: event.record.command});
    });
});
