<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Mail;

class TestEmailWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.test-email-widget';

    public ?array $data = [];

    public function mount(): void
    {
        $email = auth()->user()?->email;

        if (filled($email)) {
            $this->form->fill([
                'email' => $email,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label('Recipient Email Address')
                    ->placeholder('Enter email address')
                    ->default(fn (): ?string => auth()->user()?->email),
            ])
            ->statePath('data');
    }

    public function sendTestEmail(): void
    {
        $data = $this->form->getState();

        try {
            Mail::raw('This is a test email', function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Test Email');
            });

            Notification::make()
                ->success()
                ->title('Test email sent successfully!')
                ->body("Email sent to {$data['email']}")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Failed to send email')
                ->body($e->getMessage())
                ->send();
        }
    }
}

