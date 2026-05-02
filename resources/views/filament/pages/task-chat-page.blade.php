<x-filament-panels::page>
    <div class="max-w-5xl mx-auto">
        @livewire('task-chat', ['userTask' => $this->userTask], key('task-chat-'.$this->userTask->id))
    </div>
</x-filament-panels::page>
