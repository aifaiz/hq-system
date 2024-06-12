<?php

namespace App\Livewire\Components\Backend;

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

    public function mount(): void
    {
        $this->generateFields();
        $this->form->fill($this->data);
    }

    private function generateFields()
    {
        $setting = Setting::all();
        $fields = [];
        $data = [];

        foreach($setting as $s):
            $inputKey = 'sval_'.$s->id;
            $data[$inputKey] = $s->sval;
            $fields[] = Forms\Components\TextInput::make($inputKey)->label($s->skey);
        endforeach;

        $this->data = $data;
        $this->fields = $fields;

    }

    public function form(Form $form): Form
    {
        $this->generateFields(); // need to fill up the fields upon generate. if not missing form html.
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

        $this->generateFields();
        $this->render();
    }

    public function render()
    {
        return view('livewire.components.backend.settings');
    }
}
