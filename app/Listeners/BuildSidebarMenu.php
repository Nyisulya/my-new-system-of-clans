<?php

namespace App\Listeners;

use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Models\Member;

class BuildSidebarMenu
{
    public function handle(BuildingMenu $event)
    {
        // Dynamic clan/family menu generation disabled
        // Users can access clans and families through the main navigation menu items
        
        // If you want to re-enable dynamic menus, uncomment the code below:
        /*
        $clans = \App\Models\Clan::with('families')
            ->where('is_spouse_clan', false)
            ->get();

        foreach ($clans as $clan) {
            $event->menu->add([
                'text' => $clan->name,
                'icon' => 'fas fa-users',
                'submenu' => $this->buildFamilySubmenu($clan),
            ]);
        }
        */
    }

    protected function buildFamilySubmenu($clan)
    {
        $submenu = [];

        foreach ($clan->families as $family) {
            $submenu[] = [
                'text' => $family->name,
                'url' => route('families.tree', $family->id),
                'icon' => 'fas fa-house-user',
            ];
        }

        return $submenu;
    }
}
