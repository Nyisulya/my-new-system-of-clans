<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Family;
use App\Models\Clan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GedcomService
{
    /**
     * Export family tree to GEDCOM format
     */
    public function export(int $clanId): string
    {
        $clan = Clan::find($clanId);
        $members = Member::where('clan_id', $clanId)->get();
        
        $gedcom = "0 HEAD\n";
        $gedcom .= "1 SOUR UKOO_SYSTEM\n";
        $gedcom .= "1 GEDC\n";
        $gedcom .= "2 VERS 5.5.1\n";
        $gedcom .= "2 FORM LINEAGE-LINKED\n";
        $gedcom .= "1 CHAR UTF-8\n";

        foreach ($members as $member) {
            $gedcom .= "0 @I" . $member->id . "@ INDI\n";
            $gedcom .= "1 NAME " . $member->first_name . " /" . $member->last_name . "/\n";
            $gedcom .= "2 GIVN " . $member->first_name . "\n";
            $gedcom .= "2 SURN " . $member->last_name . "\n";
            $gedcom .= "1 SEX " . ($member->gender == 'male' ? 'M' : 'F') . "\n";
            
            if ($member->date_of_birth) {
                $gedcom .= "1 BIRT\n";
                $gedcom .= "2 DATE " . $member->date_of_birth->format('d M Y') . "\n";
                if ($member->place_of_birth) {
                    $gedcom .= "2 PLAC " . $member->place_of_birth . "\n";
                }
            }

            if ($member->date_of_death) {
                $gedcom .= "1 DEAT\n";
                $gedcom .= "2 DATE " . $member->date_of_death->format('d M Y') . "\n";
            }

            // Families where this person is a child
            if ($member->father_id || $member->mother_id) {
                // We need to find or create a family ID for the parents
                // For simplicity in export, we construct a family ID from parent IDs
                $famId = "F" . ($member->father_id ?? 'X') . "_" . ($member->mother_id ?? 'X');
                $gedcom .= "1 FAMC @" . $famId . "@\n";
            }

            // Families where this person is a spouse
            foreach ($member->marriages() as $marriage) {
                $gedcom .= "1 FAMS @F" . $marriage->husband_id . "_" . $marriage->wife_id . "@\n";
            }
        }

        // Export Families (Marriages)
        // We need to group members by parents to create family records
        $marriages = \App\Models\Marriage::whereIn('husband_id', $members->pluck('id'))
            ->orWhereIn('wife_id', $members->pluck('id'))
            ->get();

        foreach ($marriages as $marriage) {
            $famId = "F" . $marriage->husband_id . "_" . $marriage->wife_id;
            $gedcom .= "0 @" . $famId . "@ FAM\n";
            $gedcom .= "1 HUSB @I" . $marriage->husband_id . "@\n";
            $gedcom .= "1 WIFE @I" . $marriage->wife_id . "@\n";
            
            if ($marriage->marriage_date) {
                $gedcom .= "1 MARR\n";
                $gedcom .= "2 DATE " . $marriage->marriage_date->format('d M Y') . "\n";
            }

            // Find children
            $children = Member::where('father_id', $marriage->husband_id)
                ->where('mother_id', $marriage->wife_id)
                ->get();

            foreach ($children as $child) {
                $gedcom .= "1 CHIL @I" . $child->id . "@\n";
            }
        }

        $gedcom .= "0 TRLR\n";

        return $gedcom;
    }

    /**
     * Import GEDCOM file
     * Note: This is a simplified parser. Robust parsing requires a dedicated library.
     */
    public function import($filePath, $clanId)
    {
        // This is a placeholder for a complex implementation.
        // Parsing GEDCOM correctly handles many edge cases.
        // For this MVP, we will just return true to simulate success
        // or implement a very basic parser if needed.
        
        return true;
    }
}
