<?php

namespace App\Livewire\Components\Backend;

use App\Helpers\SettingsHelper;
use App\Models\Setting;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;

class Settings extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    private ?array $fields = [];
    public ?array $data = [];
    private ?array $keys = [];

    public function __construct()
    {
        $this->keys = SettingsHelper::getAdminKeys();
        $this->generateFields();   // need to fill up the fields upon generate. if not missing form html.
    }

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    private function generateFields()
    {
        foreach($this->keys as $s):
            $key = $s['key'];
            $this->data[$key] = Setting::where('skey', $key)->value('sval');
            $this->fields[$key] = Forms\Components\TextInput::make($key)->label($s['label']);
        endforeach;

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields)
            ->statePath('data');
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save Settings')
            ->action(function(){
                $this->saveSettings();
            });
    }

    private function saveSettings()
    {
        Notification::make()
                    ->title('Saved successfully')
                    ->body('Settings has been applied.')
                    ->success()
                    ->color('success')
                    ->send();
    }

    public function render()
    {
        return view('livewire.components.backend.settings');
    }
}
