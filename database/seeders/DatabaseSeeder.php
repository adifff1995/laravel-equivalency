<?php

namespace Database\Seeders;

use App\Models\EquivalencyRequest;
use App\Models\RequestStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────────────────

        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@university.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $academic = User::create([
            'name'     => 'Academic Staff',
            'email'    => 'academic@university.edu',
            'password' => Hash::make('password'),
            'role'     => 'academic',
        ]);

        // ── Sample Requests ───────────────────────────────────────────────────

        $samples = [
            [
                'tracking_code' => 'EQ-SEED0001',
                'name'          => 'Ahmed Al-Rashidi',
                'student_id'    => '2021001',
                'email'         => 'ahmed.rashidi@student.edu',
                'phone'         => '+970 59 111 2233',
                'type'          => 'internal',
                'major'         => 'Computer Science',
                'old_student_id'=> '2019001',
                'new_student_id'=> '2021001',
                'courses'       => "CS101 - Introduction to Programming\nCS102 - Data Structures\nMATH201 - Calculus I",
                'status'        => 'entered',
                'created_by'    => null, // public submission
            ],
            [
                'tracking_code' => 'EQ-SEED0002',
                'name'          => 'Sara Hassan',
                'student_id'    => '2022045',
                'email'         => 'sara.hassan@student.edu',
                'phone'         => null,
                'type'          => 'external_bridge',
                'major'         => 'Information Technology',
                'university'    => 'Al-Quds Open University',
                'courses'       => "IT100 - Fundamentals of IT\nNET101 - Networking Basics\nDB101 - Database Fundamentals",
                'status'        => 'under_review',
                'created_by'    => null,
            ],
            [
                'tracking_code' => 'EQ-SEED0003',
                'name'          => 'Mohammed Khalil',
                'student_id'    => '2023012',
                'email'         => null,
                'phone'         => '+970 56 333 4455',
                'type'          => 'special',
                'major'         => 'Software Engineering',
                'courses'       => "SE101 - Software Design Principles\nSE201 - Agile Development",
                'status'        => 'new',
                'created_by'    => $admin->id, // admin-created
            ],
            [
                'tracking_code' => 'EQ-SEED0004',
                'name'          => 'Layla Nasser',
                'student_id'    => '2021088',
                'email'         => 'layla.nasser@student.edu',
                'phone'         => null,
                'type'          => 'external_other',
                'major'         => 'Computer Science',
                'university'    => 'Birzeit University',
                'courses'       => "CS201 - Algorithms\nCS301 - Operating Systems\nCS401 - Compiler Design",
                'status'        => 'approved',
                'notes'         => 'All courses verified and approved by academic committee.',
                'created_by'    => null,
            ],
            [
                'tracking_code' => 'EQ-SEED0005',
                'name'          => 'Omar Zayed',
                'student_id'    => '2022067',
                'email'         => 'omar.zayed@student.edu',
                'phone'         => '+970 59 555 6677',
                'type'          => 'internal',
                'major'         => 'Information Systems',
                'old_student_id'=> '2020067',
                'new_student_id'=> '2022067',
                'courses'       => "IS101 - Information Systems Fundamentals\nIS201 - Business Analysis",
                'status'        => 'rejected',
                'notes'         => 'Incomplete documentation. Please resubmit with official transcripts.',
                'created_by'    => null,
            ],
            [
                'tracking_code' => 'EQ-SEED0006',
                'name'          => 'Rania Abu-Salem',
                'student_id'    => '2023099',
                'email'         => 'rania.abu@student.edu',
                'phone'         => null,
                'type'          => 'external_bridge',
                'major'         => 'Computer Science',
                'university'    => 'An-Najah National University',
                'courses'       => "CS110 - Programming I\nCS120 - Programming II",
                'status'        => 'ready_for_entry',
                'created_by'    => null,
            ],
        ];

        // Status progression chains used to generate realistic history
        $progressionChain = [
            'new'             => [],
            'under_review'    => ['new' => 'under_review'],
            'ready_for_entry' => ['new' => 'under_review', 'under_review' => 'ready_for_entry'],
            'entered'         => ['new' => 'under_review', 'under_review' => 'ready_for_entry', 'ready_for_entry' => 'entered'],
            'approved'        => ['new' => 'under_review', 'under_review' => 'ready_for_entry', 'ready_for_entry' => 'entered', 'entered' => 'approved'],
            'rejected'        => ['new' => 'under_review', 'under_review' => 'ready_for_entry', 'ready_for_entry' => 'entered', 'entered' => 'rejected'],
        ];

        foreach ($samples as $sample) {
            $req = EquivalencyRequest::create($sample);

            $chain = $progressionChain[$sample['status']] ?? [];
            foreach ($chain as $from => $to) {
                $isLastStep = ($to === $sample['status']);
                RequestStatusHistory::create([
                    'request_id' => $req->id,
                    'old_status' => $from,
                    'new_status' => $to,
                    'notes'      => $isLastStep ? ($sample['notes'] ?? null) : null,
                    'changed_by' => $admin->id,
                    'created_at' => now()->subDays(count($chain) - array_search($from, array_keys($chain))),
                    'updated_at' => now()->subDays(count($chain) - array_search($from, array_keys($chain))),
                ]);
            }
        }

        $this->command->info('✅ Seeded: 2 users + 6 sample requests.');
        $this->command->line('   Admin:    admin@university.edu / password');
        $this->command->line('   Academic: academic@university.edu / password');
        $this->command->line('');
        $this->command->line('   Sample tracking codes to test the tracker:');
        $this->command->line('   EQ-SEED0001 (entered)   EQ-SEED0004 (approved)');
        $this->command->line('   EQ-SEED0002 (review)    EQ-SEED0005 (rejected)');
        $this->command->line('   EQ-SEED0003 (new)       EQ-SEED0006 (ready)');
        $this->command->line('');
        $this->command->line('   Public tracker: /track');
        $this->command->line('   Public form:    /submit');
    }
}
