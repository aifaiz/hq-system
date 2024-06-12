<?php 
namespace App\Services;

use App\Models\CommissionPay;
use App\Models\CommissionPayItem;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class CommissionService
{
    // used inside filament.
    public function markUnpaidCommission($agentID)
    {
        $totalUnpaidComm = Order::where('agent_id', $agentID)->where('pay_status', 'PAID')->where('agent_paid', 'NO')->sum('agent_comm');

        if(!empty($totalUnpaidComm)):
            $comRecord = new CommissionPay;
            $comRecord->ref = $this->generateRef();
            $comRecord->agent_id = $agentID;
            $comRecord->amount = $totalUnpaidComm;
            $comRecord->pay_at = Carbon::today()->format('Y-m-d H:i:s');
            $comRecord->save();

            $comID = $comRecord->id;

            $pendingCommOrders = Order::where('agent_id', $agentID)->where('pay_status', 'PAID')->where('agent_paid', 'NO')->get();
            $orderIDs = [];
            foreach($pendingCommOrders as $pc):
                $comItem = new CommissionPayItem;
                $comItem->commission_pay_id = $comID;
                $comItem->order_id = $pc->id;
                $comItem->save();
                $orderIDs[] = $pc->id;
            endforeach;

            // after all done. mark all pending comm to paid.
            Order::where('agent_id', $agentID)->whereIn('id', $orderIDs)->update(['agent_paid'=>'YES']);

            Notification::make()
                ->title('Marked PAID')
                ->body('Commission payment has been applied.')
                ->success()
                ->color('success')
                ->send();
        else:
            Notification::make()
                ->title('No Pending Commission')
                ->body('Commission payment is already paid before. its empty.')
                ->danger()
                ->color('danger')
                ->send();
        endif;

    }

    private function generateRef()
    {
        $ref = hash('crc32b', rand(99,999999).uniqid());
        return $this->checkRef($ref);
    }

    private function checkRef($ref)
    {
        $check = CommissionPay::where('ref', $ref)->value('ref');
        if($check):
            $ref = hash('crc32b', rand(99,999999).uniqid().rand(9,999999));
            return $this->checkRef($ref);
        else:
            return $ref;
        endif;
    }
}