<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Fetch all members with relevant dates
        $members = Member::select('id', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'date_of_death', 'status')
            ->whereNotNull('date_of_birth')
            ->get();

        $events = [];
        $upcomingEvents = [];
        $today = Carbon::today();
        $next30Days = Carbon::today()->addDays(30);

        foreach ($members as $member) {
            // 1. Birthdays
            if ($member->date_of_birth) {
                $dob = $member->date_of_birth;
                
                // Calculate next birthday
                $birthdayThisYear = Carbon::createFromDate($today->year, $dob->month, $dob->day);
                $nextBirthday = $birthdayThisYear->copy();
                
                if ($nextBirthday->isPast() && !$nextBirthday->isToday()) {
                    $nextBirthday->addYear();
                }

                // Add to full calendar events (recurring yearly)
                // We'll generate for current year and next year to cover boundaries
                $years = [$today->year, $today->year + 1];
                foreach ($years as $year) {
                    $eventDate = Carbon::createFromDate($year, $dob->month, $dob->day);
                    $age = $eventDate->diffInYears($dob);
                    
                    $events[] = [
                        'title' => "🎂 {$member->first_name}'s " . ($age > 0 ? $age . "th " : "") . "Birthday",
                        'start' => $eventDate->format('Y-m-d'),
                        'backgroundColor' => '#00a65a', // Success Green
                        'borderColor' => '#00a65a',
                        'className' => 'event-birthday',
                        'url' => route('members.dashboard', $member->id),
                        'allDay' => true,
                    ];
                }

                // Add to upcoming list if within 30 days
                if ($nextBirthday->between($today, $next30Days)) {
                    $upcomingEvents[] = [
                        'type' => 'birthday',
                        'member' => $member,
                        'date' => $nextBirthday,
                        'age' => $nextBirthday->diffInYears($dob),
                        'days_left' => $today->diffInDays($nextBirthday),
                    ];
                }
            }

            // 2. Death Anniversaries
            if ($member->status === 'deceased' && $member->date_of_death) {
                $dod = $member->date_of_death;
                
                // Calculate next anniversary
                $anniversaryThisYear = Carbon::createFromDate($today->year, $dod->month, $dod->day);
                $nextAnniversary = $anniversaryThisYear->copy();
                
                if ($nextAnniversary->isPast() && !$nextAnniversary->isToday()) {
                    $nextAnniversary->addYear();
                }

                // Add to full calendar
                $years = [$today->year, $today->year + 1];
                foreach ($years as $year) {
                    $eventDate = Carbon::createFromDate($year, $dod->month, $dod->day);
                    $yearsGone = $eventDate->diffInYears($dod);
                    
                    if ($yearsGone > 0) {
                        $events[] = [
                            'title' => "🕊️ {$member->first_name}'s " . $yearsGone . "th Anniversary",
                            'start' => $eventDate->format('Y-m-d'),
                            'backgroundColor' => '#6c757d', // Secondary Gray
                            'borderColor' => '#6c757d',
                            'className' => 'event-death',
                            'url' => route('members.dashboard', $member->id),
                            'allDay' => true,
                        ];
                    }
                }

                // Add to upcoming list
                if ($nextAnniversary->between($today, $next30Days)) {
                    $upcomingEvents[] = [
                        'type' => 'death',
                        'member' => $member,
                        'date' => $nextAnniversary,
                        'years' => $nextAnniversary->diffInYears($dod),
                        'days_left' => $today->diffInDays($nextAnniversary),
                    ];
                }
            }
        }

        // 3. Marriage Anniversaries
        $marriages = \App\Models\Marriage::with(['husband', 'wife'])
            ->where('status', 'active')
            ->whereNotNull('marriage_date')
            ->get();

        foreach ($marriages as $marriage) {
            $dom = $marriage->marriage_date;
            
            // Calculate next anniversary
            $anniversaryThisYear = Carbon::createFromDate($today->year, $dom->month, $dom->day);
            $nextAnniversary = $anniversaryThisYear->copy();
            
            if ($nextAnniversary->isPast() && !$nextAnniversary->isToday()) {
                $nextAnniversary->addYear();
            }

            // Add to full calendar
            $years = [$today->year, $today->year + 1];
            foreach ($years as $year) {
                $eventDate = Carbon::createFromDate($year, $dom->month, $dom->day);
                $yearsMarried = $eventDate->diffInYears($dom);
                
                if ($yearsMarried > 0) {
                    $events[] = [
                        'title' => "💍 {$marriage->husband->first_name} & {$marriage->wife->first_name}'s " . $yearsMarried . "th Anniversary",
                        'start' => $eventDate->format('Y-m-d'),
                        'backgroundColor' => '#f39c12', // Warning Orange
                        'borderColor' => '#f39c12',
                        'className' => 'event-marriage',
                        'allDay' => true,
                    ];
                }
            }

            // Add to upcoming list
            if ($nextAnniversary->between($today, $next30Days)) {
                $upcomingEvents[] = [
                    'type' => 'marriage',
                    'husband' => $marriage->husband,
                    'wife' => $marriage->wife,
                    'date' => $nextAnniversary,
                    'years' => $nextAnniversary->diffInYears($dom),
                    'days_left' => $today->diffInDays($nextAnniversary),
                ];
            }
        }

        // Sort upcoming events by date
        usort($upcomingEvents, function ($a, $b) {
            return $a['date']->timestamp <=> $b['date']->timestamp;
        });

        return view('calendar.index', compact('events', 'upcomingEvents'));
    }

    public function export()
    {
        $events = $this->getAllEventsForExport();
        
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//Family Tree//NONSGML v1.0//EN\r\n";
        $icsContent .= "CALSCALE:GREGORIAN\r\n";
        $icsContent .= "METHOD:PUBLISH\r\n";
        
        foreach ($events as $event) {
            $icsContent .= "BEGIN:VEVENT\r\n";
            $icsContent .= "DTSTART;VALUE=DATE:" . $event['start'] . "\r\n";
            $icsContent .= "SUMMARY:" . $event['title'] . "\r\n";
            $icsContent .= "UID:" . uniqid() . "@familytree.local\r\n";
            $icsContent .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $icsContent .= "END:VEVENT\r\n";
        }
        
        $icsContent .= "END:VCALENDAR";

        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="family_calendar.ics"');
    }

    private function getAllEventsForExport()
    {
        // Re-use logic to get all events for current and next year
        // For brevity, I'll just call index logic internally or refactor. 
        // To keep it simple and robust, I'll duplicate the core logic slightly or refactor into a service later.
        // For now, let's just grab the basic events for the current year.
        
        $events = [];
        $today = Carbon::today();
        $members = Member::all();
        $marriages = \App\Models\Marriage::with(['husband', 'wife'])->active()->get();

        foreach ($members as $member) {
            if ($member->date_of_birth) {
                $events[] = [
                    'title' => "🎂 {$member->first_name}'s Birthday",
                    'start' => Carbon::createFromDate($today->year, $member->date_of_birth->month, $member->date_of_birth->day)->format('Ymd'),
                ];
            }
            if ($member->status === 'deceased' && $member->date_of_death) {
                $events[] = [
                    'title' => "🕊️ {$member->first_name}'s Death Anniversary",
                    'start' => Carbon::createFromDate($today->year, $member->date_of_death->month, $member->date_of_death->day)->format('Ymd'),
                ];
            }
        }

        foreach ($marriages as $marriage) {
            if ($marriage->marriage_date) {
                $events[] = [
                    'title' => "💍 {$marriage->husband->first_name} & {$marriage->wife->first_name}'s Anniversary",
                    'start' => Carbon::createFromDate($today->year, $marriage->marriage_date->month, $marriage->marriage_date->day)->format('Ymd'),
                ];
            }
        }

        return $events;
    }
}
