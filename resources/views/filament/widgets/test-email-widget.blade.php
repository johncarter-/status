<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Test Email
        </x-slot>

        <x-slot name="description">
            Send a test email to verify your email configuration is working correctly.
        </x-slot>

        <form wire:submit="sendTestEmail">
            {{ $this->form }}

            <x-filament::button
                type="submit"
                class="mt-4"
            >
                Send Test Email
            </x-filament::button>
        </form>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>




