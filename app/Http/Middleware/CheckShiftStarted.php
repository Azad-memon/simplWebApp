<?php

namespace App\Http\Middleware;

use App\Models\ShiftCashNote;
use App\Models\ShiftUser;
use Closure;
use Illuminate\Http\Request;

class CheckShiftStarted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user && ($user->role_id == 4 || $user->role_id == 6)) {
            return $next($request);
        }
        //     $staffId = auth()->id();

        //     // Find the last opening shift
        //     $openingShift = ShiftCashNote::where('user_id', $staffId)
        //         ->where('entry_type', 'opening')
        //         ->orderBy('created_at', 'desc')
        //         ->first();

        //     //dd( $openingShift);
        //     // Find closing shift created *after* that opening
        //     if($openingShift!=""){
        //     $closingShift = ShiftCashNote::where('user_id', $staffId)
        //         ->where('entry_type', 'closing')
        //         ->where('created_at', '>', optional($openingShift)->created_at)
        //         ->first();

        //     // Check validity
        //     if (! $openingShift || $closingShift) {
        //         if ($request->expectsJson()) {
        //             return response()->json([
        //                 'status' => false,
        //                 'message' => 'Please start your shift first before accessing POS.',
        //             ], 403);
        //         }

        //         return redirect()->route('shift.inventory')
        //             ->with('error', 'Please start your shift first.');
        //     }
        // }else{
        //       return redirect()->route('shift.inventory')
        //             ->with('error', 'Please start your shift first.');
        // }
        $staffId = auth()->id();
        $branchId = auth()->user()->branchstaff()->first()?->branch_id;

        $activeShift = ShiftUser::where('user_id', $staffId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
          //  ->latest('shift_date')
            ->first();

        if (! $activeShift) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please start your shift before accessing POS.',
                ], 403);
            }

            return redirect()->route('shift.inventory')
                ->with('error', 'Please start your shift first.');
        }

        // return redirect()->route('shift.inventory')->with('error', 'Please start your shift first.');
        return $next($request);
    }
}
