<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\Certificate;
use Carbon\Carbon;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Find Volunteer 1 (typically User ID 3)
        $volunteer = User::where('role', 'volunteer')->first();
        if (!$volunteer) {
            $this->command->warn('No volunteer found to seed certificates for.');
            return;
        }

        // Get some past events
        $events = Event::all();
        if ($events->isEmpty()) {
            $this->command->warn('No events found. Run EventSeeder first.');
            return;
        }

        // We want to set up three events for the volunteer:
        // 1. Event 1 (Santa Monica Beach Clean-Up) -> Attended & Certificate Generated
        // 2. Event 2 (Malibu Coastal Restoration) -> Attended & Certificate Generated
        // 3. Event 3 (Venice Beach Community Day) -> Attended but Certificate NOT Generated (ready to test)

        // Make sure the certificates directory exists
        Storage::disk('public')->makeDirectory('certificates');

        // Setup Event 1
        $event1 = $events->where('id', 1)->first() ?? $events->first();
        $this->setupAttendance($volunteer, $event1, 'present');
        $this->createCertificate($volunteer, $event1);

        // Setup Event 2
        $event2 = $events->where('id', 2)->first();
        if ($event2) {
            $this->setupAttendance($volunteer, $event2, 'present');
            $this->createCertificate($volunteer, $event2);
        }

        // Setup Event 3
        $event3 = $events->where('id', 3)->first();
        if ($event3) {
            $this->setupAttendance($volunteer, $event3, 'present');
            // Do NOT generate certificate so they can click "Generate" on the frontend
        }

        $this->command->info('Successfully seeded certificates and attendance data.');
    }

    private function setupAttendance($user, $event, $status)
    {
        // 1. Ensure registration exists
        EventRegistration::firstOrCreate(
            ['user_id' => $user->id, 'event_id' => $event->id],
            ['status' => 'registered']
        );

        // 2. Ensure attendance exists
        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'event_id' => $event->id],
            ['status' => $status]
        );
    }

    private function createCertificate($user, $event)
    {
        // Create Certificate Model record
        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'event_id' => $event->id],
            ['file_path' => '']
        );

        $serial = $certificate->serial_number;
        $name = strtoupper($user->name);
        $title = $event->title;
        $date = Carbon::parse($event->event_date)->format('F d, Y');
        $location = $event->location;
        $duration = $event->duration;
        $organizerName = $event->organizer ? $event->organizer->name : 'OceanCare Team';

        // Generate SVG
        $svg = $this->getSvgContent($name, $title, $date, $location, $duration, $serial, $organizerName);

        // Save file
        $filename = "certificates/cert_{$user->id}_{$event->id}.svg";
        Storage::disk('public')->put($filename, $svg);

        // Update path
        $certificate->file_path = "storage/" . $filename;
        $certificate->save();
    }

    private function getSvgContent($name, $title, $date, $location, $duration, $serial, $organizerName)
    {
        return '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1120 792" width="1120" height="792">
    <defs>
        <linearGradient id="bg-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#FCFDFF" />
            <stop offset="100%" stop-color="#F2F7FA" />
        </linearGradient>
        <linearGradient id="border-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#0F172A" />
            <stop offset="50%" stop-color="#0284C7" />
            <stop offset="100%" stop-color="#0369A1" />
        </linearGradient>
        <linearGradient id="gold-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#B45309" />
            <stop offset="50%" stop-color="#F59E0B" />
            <stop offset="100%" stop-color="#D97706" />
        </linearGradient>
    </defs>
    <rect width="1120" height="792" fill="url(#bg-grad)" />
    <path d="M 0,600 C 300,550 500,750 800,650 C 950,600 1050,700 1120,680 L 1120,792 L 0,792 Z" fill="#E0F2FE" opacity="0.3" />
    <path d="M 0,650 C 250,600 450,780 750,690 C 920,640 1020,720 1120,710 L 1120,792 L 0,792 Z" fill="#F0FDFA" opacity="0.4" />
    <rect x="30" y="30" width="1060" height="732" rx="8" fill="none" stroke="url(#border-grad)" stroke-width="12" />
    <rect x="45" y="45" width="1030" height="702" rx="6" fill="none" stroke="#D97706" stroke-width="2" opacity="0.7" />
    <rect x="55" y="55" width="1010" height="682" rx="4" fill="none" stroke="#E2E8F0" stroke-width="1" />
    <path d="M 30,80 L 80,30" stroke="#D97706" stroke-width="3" />
    <path d="M 30,90 L 90,30" stroke="#0284C7" stroke-width="2" />
    <path d="M 1090,80 L 1040,30" stroke="#D97706" stroke-width="3" />
    <path d="M 1090,90 L 1030,30" stroke="#0284C7" stroke-width="2" />
    <path d="M 30,712 L 80,762" stroke="#D97706" stroke-width="3" />
    <path d="M 30,702 L 90,762" stroke="#0284C7" stroke-width="2" />
    <path d="M 1090,712 L 1040,762" stroke="#D97706" stroke-width="3" />
    <path d="M 1090,702 L 1030,762" stroke="#0284C7" stroke-width="2" />
    <g transform="translate(560, 110)">
        <path d="M -15,-20 C -25,-20 -28,-10 -35,-10 C -42,-10 -45,-20 -55,-20 L -55,-15 C -45,-15 -42,-5 -35,-5 C -28,-5 -25,-15 -15,-15 C -5,-15 -2,-5 5,-5 C 12,-5 15,-15 25,-15 L 25,-20 C 15,-20 12,-10 5,-10 C -2,-10 -5,-20 -15,-20 Z" fill="#0284C7" />
        <path d="M -15,-10 C -25,-10 -28,0 -35,0 C -42,0 -45,-10 -55,-10 L -55,-5 C -45,-5 -42,5 -35,5 C -28,5 -25,-5 -15,-5 C -5,-5 -2,5 5,5 C 12,5 15,-5 25,-5 L 25,-10 C 15,-10 12,0 5,0 C -2,0 -5,-10 -15,-10 Z" fill="#0EA5E9" />
        <text y="25" font-family="\'Inter\', sans-serif" font-size="24" font-weight="bold" fill="#0F172A" text-anchor="middle" letter-spacing="4">OCEANCARE</text>
    </g>
    <text x="560" y="225" font-family="\'Inter\', sans-serif" font-size="36" font-weight="bold" fill="#0F172A" text-anchor="middle" letter-spacing="6">CERTIFICATE OF APPRECIATION</text>
    <text x="560" y="275" font-family="\'Inter\', sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle" letter-spacing="2">THIS CERTIFICATE IS PROUDLY PRESENTED TO</text>
    <text x="560" y="350" font-family="\'Inter\', sans-serif" font-size="42" font-weight="bold" fill="#0284C7" text-anchor="middle" letter-spacing="1">' . htmlspecialchars($name) . '</text>
    <line x1="330" y1="370" x2="790" y2="370" stroke="url(#gold-grad)" stroke-width="3" />
    <polygon points="560,366 565,370 560,374 555,370" fill="#D97706" />
    <text x="560" y="420" font-family="\'Inter\', sans-serif" font-size="16" fill="#475569" text-anchor="middle">for outstanding and dedicated service as a volunteer in</text>
    <text x="560" y="470" font-family="\'Inter\', sans-serif" font-size="26" font-weight="bold" fill="#0F172A" text-anchor="middle">"' . htmlspecialchars($title) . '"</text>
    <text x="560" y="520" font-family="\'Inter\', sans-serif" font-size="15" fill="#475569" text-anchor="middle">held on ' . htmlspecialchars($date) . ' at ' . htmlspecialchars($location) . '</text>
    <text x="560" y="555" font-family="\'Inter\', sans-serif" font-size="15" font-weight="600" fill="#0369A1" text-anchor="middle">Total Contribution: ' . htmlspecialchars($duration) . ' Hours of Volunteer Service</text>
    <g transform="translate(250, 660)">
        <path d="M 10,-25 Q 35,-45 45,-15 T 90,-25 T 130,-15" fill="none" stroke="#0F172A" stroke-width="2" stroke-linecap="round" opacity="0.8" />
        <line x1="0" y1="0" x2="160" y2="0" stroke="#CBD5E1" stroke-width="1.5" />
        <text x="80" y="20" font-family="\'Inter\', sans-serif" font-size="13" font-weight="bold" fill="#334155" text-anchor="middle">' . htmlspecialchars($organizerName) . '</text>
        <text x="80" y="36" font-family="\'Inter\', sans-serif" font-size="11" fill="#64748B" text-anchor="middle">Event Coordinator</text>
    </g>
    <g transform="translate(710, 660)">
        <path d="M 15,-20 C 30,-40 40,-10 65,-30 C 85,-45 105,-20 125,-25" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" opacity="0.8" />
        <line x1="0" y1="0" x2="160" y2="0" stroke="#CBD5E1" stroke-width="1.5" />
        <text x="80" y="20" font-family="\'Inter\', sans-serif" font-size="13" font-weight="bold" fill="#334155" text-anchor="middle">Sarah Jenkins</text>
        <text x="80" y="36" font-family="\'Inter\', sans-serif" font-size="11" fill="#64748B" text-anchor="middle">Director, OceanCare</text>
    </g>
    <g transform="translate(560, 650)">
        <path d="M 0,-35 L 10,-33 L 20,-30 L 28,-20 L 33,-10 L 35,0 L 33,10 L 28,20 L 20,30 L 10,33 L 0,35 L -10,33 L -20,30 L -28,20 L -33,10 L -35,0 L -33,-10 L -28,-20 L -20,-30 L -10,-33 Z" fill="url(#gold-grad)" />
        <circle cx="0" cy="0" r="30" fill="#D97706" />
        <circle cx="0" cy="0" r="26" fill="url(#gold-grad)" stroke="#FFFFFF" stroke-width="1.5" />
        <path d="M 0,-15 L 4,-4 L 15,-4 L 7,3 L 10,13 L 0,7 L -10,13 L -7,3 L -15,-4 L -4,-4 Z" fill="#FFFFFF" />
        <path d="M -15,25 L -25,65 L -5,55 L 10,65 L 0,25 Z" fill="#B45309" opacity="0.85" />
        <path d="M 0,25 L 15,65 L 0,55 L -10,65 L -5,25 Z" fill="#D97706" opacity="0.85" />
    </g>
    <text x="560" y="745" font-family="\'Inter\', sans-serif" font-size="10.5" font-weight="600" fill="#94A3B8" text-anchor="middle" letter-spacing="1.5">SERIAL NUMBER: ' . htmlspecialchars($serial) . '</text>
</svg>';
    }
}
