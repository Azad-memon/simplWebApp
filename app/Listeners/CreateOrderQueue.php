<?php
namespace App\Listeners;

use App\Events\OrderAccepted;
use App\Models\OrderQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateOrderQueue
{
    public function handle(OrderAccepted $event)
    {
        $order = $event->order;

        // Only for accepted orders
        // if ($order->status !== 'processing') {
        //     return;
        // }

        $branchId = $order->branch_id;

        // Detect current shift based on order time
        // $shift = $this->detectShift($branchId, $order->created_at);

        // if (!$shift) {
        //     return; // No active shift found
        // }

        // // Identify today's first shift
        // $firstShift = $this->getTodayFirstShift($branchId);

        // // Queue is only created for first shift
        // if ($shift->id !== $firstShift->id) {
        //     return;
        // }
        $shift=1;

        // Create the queue
        $this->createQueue($order, $branchId, $shift);
    }

    // ------------------------------
    // Reuse your createQueue() logic
    // ------------------------------
    public function createQueue($order, $branchId, $shiftId)
    {
       // dd($order);
        $shift = \App\Models\StaffShift::find($shiftId);
       // $queueDate = $this->getShiftQueueDate($shift, $order->created_at);
       $queueDate = date('Y-m-d');

    // Close old queues (before this shift-date)
    // OrderQueue::where('branch_id', $branchId)
    //     ->where('shift_id', $shiftId)
    //     ->where('queue_date', '<', $queueDate)
    //     ->update(['status' => 'closed']);

    // Last queue number for this shift-date
//     $hasClosedQueue = OrderQueue::where('branch_id', $branchId)
//     ->where('shift_id', $shiftId)
//     ->where('queue_date', $queueDate)
//    // ->where('shift_closed', false)
//     ->where('status', 'closed')
//     ->first();

// if ($hasClosedQueue!== null) {
//     // Reset counter to 1 only if previous queues are closed
//     $nextNumber = 1;
//    // $hasClosedQueue->update(['shift_closed' => true]);
// } else {
    // Get last open queue for same branch, shift, date

    $lastQueue = OrderQueue::where('branch_id', $branchId)
        //->where('queue_date', $queueDate)
        ->orderBy('queue_number', 'DESC')
        ->first();

    // Increment from last queue number if exists
    $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

    $order->update(['queue_number' => $nextNumber]);
    Log::info('Queue Debug', [
        'next_number' => $nextNumber,
        'order_id'    => $order->id,
    ]);

    return OrderQueue::create([
        'order_id'     => $order->id,
        'branch_id'    => $branchId,
        'shift_id'     => $shift->id,
        'queue_number' => $nextNumber,
        'queue_date'   => $queueDate,
        'status'       => 'open',
    ]);
    }

    // ------------------------------
    // Reuse getShiftQueueDate()
    // ------------------------------
   public function getShiftQueueDate($shift, $orderTime = null)
{
    $orderTime = $orderTime ?? now();

    $shiftStart = $orderTime->copy()->setTimeFromTimeString($shift->start_time);
    $shiftEnd   = $orderTime->copy()->setTimeFromTimeString($shift->end_time);

    // Overnight shift
    if ($shiftEnd->lessThan($shiftStart)) {
        // Order after midnight but before shift ends → queue_date = previous day
        if ($orderTime->lessThan($shiftEnd)) {
            return $shiftStart->subDay()->toDateString();
        }
    }

    // Normal shift
    return $shiftStart->toDateString();
}

    // ------------------------------
    // Reuse detectShift()
    // ------------------------------
    public function detectShift($branchId, $orderTime = null)
{
    $orderTime = $orderTime ?? now();
    $shifts = \App\Models\StaffShift::where("branch_id", $branchId)->get();

    foreach ($shifts as $shift) {
        $endTime = $shift->end_time === '24:00:00' ? '23:59:59' : $shift->end_time;

        $start = $orderTime->copy()->setTimeFromTimeString($shift->start_time);
        $end   = $orderTime->copy()->setTimeFromTimeString($endTime);

        // Normal shift
        if ($start <= $end && $orderTime->between($start, $end)) {
            return $shift;
        }

        // Overnight shift
        if ($start > $end && ($orderTime >= $start || $orderTime <= $end)) {
            return $shift;
        }
    }

    return null;
}

    // ------------------------------
    // Reuse getTodayFirstShift()
    // ------------------------------
    public function getTodayFirstShift($branchId)
    {
        return \App\Models\StaffShift::where('branch_id', $branchId)
            ->orderBy('start_time', 'asc')
            ->first();
    }

}
