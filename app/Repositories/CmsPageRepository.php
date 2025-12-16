<?php

namespace App\Repositories;

use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Models\Popup;
use App\Models\ModelUserActivityLog;
use App\Repositories\Interfaces\CmsPageRepositoryInterface;

class CmsPageRepository implements CmsPageRepositoryInterface
{
    public function all()
    {
        return CmsPage::latest()->get();
    }

    public function find($id): ?CmsPage
    {
        return CmsPage::find($id);
    }

    public function create(array $data): CmsPage
    {
        return CmsPage::create($data);
    }

    public function update($id, array $data): bool
    {
        $cmsPage = CmsPage::find($id);
        return $cmsPage ? $cmsPage->update($data) : false;
    }

    public function delete($id): bool
    {
        $cmsPage = CmsPage::find($id);
        return $cmsPage ? (bool) $cmsPage->delete() : false;
    }
    public function toggleStatus(array $data)
    {
        $cmsPage = CmsPage::findOrFail($data['id']);
        $cmsPage->is_active = $data['status'];
        $cmsPage->save();

        // ModelUserActivityLog::logActivity(
        //     Auth::id(),
        //     "has toggled status of coupon with ID {$coupon->id} to " . ($coupon->status ? 'active' : 'inactive')
        // );

        return $cmsPage;
    }
    public function findwithslug($slug): ?CmsPage
    {
        return CmsPage::where('slug', $slug)->first();
    }
    public function savecontact($data)
    {
        // dd($data);
        return ContactUs::create($data);
    }
    public function storePopup($data)
    {

        //dd($data);
        $popup = Popup::first();
        if ($popup) {
            $popup->update([
                'image' => $data['images']['image'] ?? $popup->image,
                'is_active'=>$data['is_active']??$popup->is_active
            ]);
        } else {
            $popup = Popup::create([
                'image' => $data['images']['image'] ?? null,
                'is_active'=>$data['is_active']??$popup->is_active
            ]);
        }

        return $popup;
    }
      public function getPopup()
    {

        return Popup::where('is_active',1)->first();
    }

}
