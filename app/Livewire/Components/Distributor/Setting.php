<?php

namespace App\Livewire\Components\Distributor;

use App\Helpers\SettingsHelper;
use App\Models\DistributorSetting;
use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Action;

class Setting extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    private ?array $fields = [];
    public ?array $data = [];
    private int $distributorID;
    private ?array $settingKeys = [];

    public function __construct()
    {
        $this->distributorID = auth()->id();
        $this->settingKeys = SettingsHelper::getDistributorKeys();
        $this->generateFields(); // need to fill up the fields upon generate. if not missing form html.        
    }

    public function mount()
    {
        $this->form->fill($this->data);
    }

    private function generateFields()
    {
        foreach($this->settingKeys as $k):
            $key = $k['key'];

            if($k['type'] == 'select'):
                $this->fields[$key] = Forms\Components\Select::make($key)
                    ->label($k['label'])
                    ->required()
                    ->options($k['options']);
            elseif($k['type'] == 'textarea'):
                $this->fields[$key] = Forms\Components\Textarea::make($key)
                    ->label($k['label'])
                    ->helperText($k['helpertext'] ?? '')
                    ->required();
            else:
                $this->fields[$key] = Forms\Components\TextInput::make($key)->label($k['label'])->required();
            endif;

            $value = DistributorSetting::where('distributor_id', $this->distributorID)->where('skey', $key)->value('sval');
            $this->data[$key] = $value ?? '';
        endforeach;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema($this->fields)
            ])
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
        foreach($this->data as $k=>$v):
            $exist = DistributorSetting::where('distributor_id', $this->distributorID)->where('skey', $k)->value('sval');

            if($exist):
                DistributorSetting::where('distributor_id', $this->distributorID)->where('skey', $k)->update([
                    'sval'=>$v
                ]);
            else:
                $setting = new DistributorSetting;
                $setting->distributor_id = $this->distributorID;
                $setting->skey = $k;
                $setting->sval = $v;
                $setting->save();
            endif;
            
        endforeach;
        Notification::make()
                    ->title('Updated')
                    ->body('Settings has been updated.')
                    ->info()
                    ->color('info')
                    ->send();
    }

    public function render()
    {
        return view('livewire.components.distributor.setting');
    }
}
