<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\InspectionImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $users = [
            ['username' => 'Admin Dev',    'email' => 'admin@portal.ph',       'password' => Hash::make('password'), 'token_type' => 'admin'],
            ['username' => 'A. Santos',    'email' => 'a.santos@portal.ph',    'password' => Hash::make('password'), 'token_type' => 'inspector'],
            ['username' => 'R. Cruz',      'email' => 'r.cruz@portal.ph',      'password' => Hash::make('password'), 'token_type' => 'inspector'],
            ['username' => 'J. Reyes',     'email' => 'j.reyes@portal.ph',     'password' => Hash::make('password'), 'token_type' => 'inspector'],
            ['username' => 'M. Bautista',  'email' => 'm.bautista@portal.ph',  'password' => Hash::make('password'), 'token_type' => 'reviewer'],
            ['username' => 'L. Garcia',    'email' => 'l.garcia@portal.ph',    'password' => Hash::make('password'), 'token_type' => 'inspector'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }

        // Vessels and exams
        $vessels = [
            ['id' => 1, 'name' => 'MV Pacific Endeavour'],
            ['id' => 2, 'name' => 'MT Horizon Star'],
            ['id' => 3, 'name' => 'MV Southern Cross'],
            ['id' => 4, 'name' => 'MT Ocean King'],
            ['id' => 5, 'name' => 'MT Northern Passage'],
            ['id' => 6, 'name' => 'MV Coral Sea'],
            ['id' => 7, 'name' => 'MT Eastern Wind'],
            ['id' => 8, 'name' => 'MV Manila Bay'],
            ['id' => 9, 'name' => 'MT Visayan Star'],
        ];

        $types       = ['Hull', 'Engine', 'Safety', 'Structural', 'Electrical'];
        $inspectors  = ['A. Santos', 'R. Cruz', 'J. Reyes', 'M. Bautista', 'L. Garcia'];
        $examCounter = 1;

        foreach ($vessels as $vessel) {
            $numExams = rand(3, 8);
            for ($i = 0; $i < $numExams; $i++) {
                $type        = $types[array_rand($types)];
                $inspector   = $inspectors[array_rand($inspectors)];
                $submittedAt = now()->subDays(rand(1, 180));

                $exam = Exam::create([
                    'exam_id'          => 'EX-' . now()->year . '-' . str_pad($examCounter, 4, '0', STR_PAD_LEFT),
                    'vessel_name'      => $vessel['name'],
                    'person_in_charge' => $inspector,
                    'submitted_date'   => $submittedAt,
                    'submitted_by'     => $inspector,
                    'email'            => strtolower(str_replace('. ', '.', $inspector)) . '@portal.ph',
                    'answers'          => $this->generateAnswers($type),
                ]);

                // Add inspection images for each exam
                $numImages = rand(4, 20);
                for ($j = 1; $j <= $numImages; $j++) {
                    InspectionImage::create([
                        'vessel_id'        => $vessel['id'],
                        'inspection_id'    => $exam->id,
                        'image_name'       => strtolower($type) . '_' . $vessel['id'] . '_img' . str_pad($j, 2, '0', STR_PAD_LEFT) . '.jpg',
                        'image_data'       => $this->getSampleBase64(),
                        'image_mime_type'  => 'image/jpeg',
                        'inspection_type'  => $type,
                        'created_at'       => $submittedAt,
                        'updated_at'       => $submittedAt,
                    ]);
                }

                $examCounter++;
            }
        }

        echo "Seeded: " . User::count() . " users, " . Exam::count() . " exams, " . InspectionImage::count() . " images\n";
    }

    private function generateAnswers(string $type): array
    {
        $questions = [
            'Hull'       => ['Hull integrity OK?', 'Corrosion visible?', 'Paint condition?', 'Waterline clear?', 'Propeller condition?'],
            'Engine'     => ['Engine oil level?', 'Coolant level?', 'Fuel system OK?', 'Exhaust condition?', 'Vibration normal?'],
            'Safety'     => ['Life jackets count?', 'Fire extinguishers OK?', 'Emergency exits clear?', 'Flares valid?', 'First aid kit OK?'],
            'Structural' => ['Frame condition?', 'Welds inspected?', 'Deck integrity?', 'Bulkheads OK?', 'Rivets/bolts secure?'],
            'Electrical' => ['Main panel OK?', 'Wiring insulation?', 'Navigation lights?', 'Battery banks?', 'Generator tested?'],
        ];

        $answers = [];
        foreach ($questions[$type] ?? $questions['Hull'] as $idx => $q) {
            $answers['q' . ($idx + 1)] = [
                'question' => $q,
                'answer'   => ['Yes', 'No', 'N/A', 'Needs Repair'][rand(0, 3)],
                'notes'    => rand(0, 1) ? 'Inspector note: ' . fake()->sentence(6) : null,
            ];
        }
        return $answers;
    }

    /**
     * Returns a tiny valid 1x1 pixel JPEG as base64
     * Replace with actual image storage logic in production
     */
    private function getSampleBase64(): string
    {
        return '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AJQAB/9k=';
    }
}
