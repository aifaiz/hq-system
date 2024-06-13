<?php

namespace App\Livewire\Components\Distributor;

use App\Models\DistributorProductQty;
use App\Models\Product;
use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Action;

class Inventory extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    private ?array $fields = [];
    public ?array $data = [];
    private int $distributorID;
    private $products;

    public function __construct()
    {
        $this->distributorID = auth()->id();
        $this->getProducts();
        $this->generateFields();

        // dd($this->data);
        
    }

    public function mount(): void
    {
        $this->form->fill($this->data);
        // dd($this->data, $this->distributorID, $this->products);
    }

    private function getQty(int $productID)
    {
        $qty = DistributorProductQty::where('product_id', $productID)->where('distributor_id', $this->distributorID)->value('qty');

        if(!$qty){
            $qty = 0;
        }

        return $qty;
    }

    private function getProducts()
    {
        $this->products = Product::where('status', '1')->orderBy('created_at','DESC')->get();
        foreach($this->products as $p):
            $this->data['qty_'.$p->id] = $this->getQty($p->id);
        endforeach;

        // dd($this->data);

    }

    private function generateFields()
    {
        $fields = [];

        foreach($this->products as $p):
            $label = $p->name.' (RM '. $p->price.')';
            $fields[] = Forms\Components\TextInput::make('qty_'.$p->id)
                ->label($label)
                ->prefix('Qty')
                ->numeric();
        endforeach;
        $this->fields = $fields;

    }

    public function form(Form $form): Form
    {
        $this->generateFields(); // need to fill up the fields upon generate. if not missing form html.
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
        // $msg = '';
        foreach($this->data as $k=>$qty):
            $pid = explode('_',$k);
            $productID = $pid[1] ?? null;
            if($productID):
                $dqty = DistributorProductQty::where('product_id', $productID)->where('distributor_id', $this->distributorID)->first();

                if(!isset($dqty->id) && empty($dqty->id)):
                    $stock = new DistributorProductQty;
                    $stock->product_id = $productID;
                    $stock->distributor_id = $this->distributorID;
                    $stock->qty = $qty;
                    $stock->save();
                else:
                    DistributorProductQty::where('product_id', $productID)->where('distributor_id', $this->distributorID)->update(['qty'=>$qty]);
                endif;

                // $msg = json_encode(['k'=>$k,'d'=>$qty]);
            endif;
        endforeach;

        // dd($this->data);

        Notification::make()
                    ->title('Saved successfully')
                    ->body('Stocks has been updated.')
                    ->success()
                    ->color('success')
                    ->send();

        // $this->generateFields();
        // $this->render();
    }

    public function render()
    {
        return view('livewire.components.distributor.inventory');
    }
}
