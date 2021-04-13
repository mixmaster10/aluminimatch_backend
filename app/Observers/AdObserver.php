<?php

namespace App\Observers;

use App\Models\Ad;
use Illuminate\Http\Request;

class AdObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Handle the ad "created" event.
     *
     * @param  \App\Ad  $ad
     * @return void
     */
    public function created(Ad $ad)
    {
        //
    }

    /**
     * Handle the ad "updated" event.
     *
     * @param  \App\Ad  $ad
     * @return void
     */
    public function updated(Ad $ad)
    {
        //
        // $company= $ad->company();
        // $company->update($this->request->all());
    }

    /**
     * Handle the ad "deleted" event.
     *
     * @param  \App\Ad  $ad
     * @return void
     */
    public function deleted(Ad $ad)
    {
        //
    }

    /**
     * Handle the ad "restored" event.
     *
     * @param  \App\Ad  $ad
     * @return void
     */
    public function restored(Ad $ad)
    {
        //
    }

    /**
     * Handle the ad "force deleted" event.
     *
     * @param  \App\Ad  $ad
     * @return void
     */
    public function forceDeleted(Ad $ad)
    {
        //
    }
}
