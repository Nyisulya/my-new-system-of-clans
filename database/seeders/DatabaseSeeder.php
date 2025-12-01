<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Clan;
use App\Models\Family;
use App\Models\Branch;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Users with different roles
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@familytree.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $editor = User::create([
            'name' => 'Editor User',
            'email' => 'editor@familytree.com',
            'password' => Hash::make('password'),
            'role' => 'editor',
            'is_active' => true,
        ]);

        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@familytree.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
        ]);

        echo "✓ Created 3 users (admin, editor, viewer)\n";

        // Create Clans
        $doeClan = Clan::create([
            'name' => 'Doe Clan',
            'description' => 'The historic Doe family lineage, tracing back to Ireland in the 1800s.',
            'founding_date' => '1850-01-01',
            'origin_location' => 'County Cork, Ireland',
            'is_active' => true,
        ]);

        $smithClan = Clan::create([
            'name' => 'Smith Clan',
            'description' => 'The Smith family heritage from Scotland.',
            'founding_date' => '1820-06-15',
            'origin_location' => 'Edinburgh, Scotland',
            'is_active' => true,
        ]);

        echo "✓ Created 2 clans\n";

        // Create Families
        $doeMainFamily = Family::create([
            'clan_id' => $doeClan->id,
            'name' => 'Main Doe Family',
            'surname' => 'Doe',
            'description' => 'The primary Doe family branch',
            'origin_place' => 'New York, USA',
            'established_date' => '1920-01-01',
            'is_active' => true,
        ]);

        $doeWesternFamily = Family::create([
            'clan_id' => $doeClan->id,
            'name' => 'Western Doe Family',
            'surname' => 'Doe',
            'description' => 'Doe family branch that settled in California',
            'origin_place' => 'San Francisco, CA',
            'established_date' => '1950-05-10',
            'is_active' => true,
        ]);

        echo "✓ Created 2 families\n";

        // Create Branches
        $bostonBranch = Branch::create([
            'family_id' => $doeMainFamily->id,
            'name' => 'Boston Branch',
            'description' => 'Doe family members in the Boston area',
            'location' => 'Boston, MA',
            'is_active' => true,
        ]);

        $nycBranch = Branch::create([
            'family_id' => $doeMainFamily->id,
            'name' => 'New York Branch',
            'description' => 'Doe family members in New York City',
            'location' => 'New York, NY',
            'is_active' => true,
        ]);

        echo "✓ Created 2 branches\n";

        // Create Multi-Generation Family Tree
        
        // Generation 1 - Founders (born ~1850)
        $johnFounder = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'first_name' => 'John',
            'middle_name' => 'Patrick',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1850-03-15',
            'place_of_birth' => 'County Cork, Ireland',
            'status' => 'deceased',
            'date_of_death' => '1925-12-10',
            'place_of_death' => 'New York, NY',
            'generation_number' => 1,
            'biography' => 'Immigrated from Ireland to America in 1870. Founded the Doe family presence in New York.',
            'occupation' => 'Merchant',
            'created_by' => $admin->id,
        ]);

        $maryFounder = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'first_name' => 'Mary',
            'middle_name' => 'Catherine',
            'last_name' => 'Doe',
            'maiden_name' => 'O\'Brien',
            'gender' => 'female',
            'date_of_birth' => '1852-07-20',
            'place_of_birth' => 'County Cork, Ireland',
            'status' => 'deceased',
            'date_of_death' => '1930-05-18',
            'place_of_death' => 'New York, NY',
            'generation_number' => 1,
            'created_by' => $admin->id,
        ]);

        // Create marriage
        \App\Models\Marriage::create([
            'husband_id' => $johnFounder->id,
            'wife_id' => $maryFounder->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        // Generation 2 - Their children (born ~1875-1885)
        $robertGen2 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Robert',
            'middle_name' => 'James',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1875-09-12',
            'place_of_birth' => 'New York, NY',
            'father_id' => $johnFounder->id,
            'mother_id' => $maryFounder->id,
            'status' => 'deceased',
            'date_of_death' => '1955-03-22',
            'place_of_death' => 'New York, NY',
            'occupation' => 'Banker',
            'created_by' => $admin->id,
        ]);

        $elizabethGen2 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'first_name' => 'Elizabeth',
            'middle_name' => 'Rose',
            'last_name' => 'Doe',
            'maiden_name' => 'Sullivan',
            'gender' => 'female',
            'date_of_birth' => '1878-04-05',
            'place_of_birth' => 'Boston, MA',
            'status' => 'deceased',
            'date_of_death' => '1960-11-30',
            'place_of_death' => 'New York, NY',
            'created_by' => $admin->id,
        ]);

        \App\Models\Marriage::create([
            'husband_id' => $robertGen2->id,
            'wife_id' => $elizabethGen2->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $thomasGen2 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $bostonBranch->id,
            'first_name' => 'Thomas',
            'middle_name' => 'William',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1880-12-03',
            'place_of_birth' => 'New York, NY',
            'father_id' => $johnFounder->id,
            'mother_id' => $maryFounder->id,
            'status' => 'deceased',
            'date_of_death' => '1965-07-14',
            'place_of_death' => 'Boston, MA',
            'occupation' => 'Doctor',
            'created_by' => $admin->id,
        ]);

        // Generation 3 (born ~1905-1920)
        $johnGen3 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'John',
            'middle_name' => 'Robert',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1905-06-18',
            'place_of_birth' => 'New York, NY',
            'father_id' => $robertGen2->id,
            'mother_id' => $elizabethGen2->id,
            'status' => 'deceased',
            'date_of_death' => '1985-09-05',
            'occupation' => 'Engineer',
            'created_by' => $admin->id,
        ]);

        $margaretGen3 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Margaret',
            'middle_name' => 'Anne',
            'last_name' => 'Doe',
            'maiden_name' => 'Kelly',
            'gender' => 'female',
            'date_of_birth' => '1908-02-14',
            'place_of_birth' => 'Boston, MA',
            'status' => 'deceased',
            'date_of_death' => '1995-12-25',
            'created_by' => $admin->id,
        ]);

        \App\Models\Marriage::create([
            'husband_id' => $johnGen3->id,
            'wife_id' => $margaretGen3->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        // Generation 4 (born ~1935-1955)
        $michaelGen4 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Michael',
            'middle_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1940-11-22',
            'place_of_birth' => 'New York, NY',
            'father_id' => $johnGen3->id,
            'mother_id' => $margaretGen3->id,
            'status' => 'alive',
            'email' => 'michael.doe@example.com',
            'phone' => '+1-555-0101',
            'address' => '123 Park Avenue',
            'city' => 'New York',
            'country' => 'USA',
            'occupation' => 'Retired Lawyer',
            'created_by' => $admin->id,
        ]);

        $susanGen4 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Susan',
            'middle_name' => 'Marie',
            'last_name' => 'Doe',
            'maiden_name' => 'white',
            'gender' => 'female',
            'date_of_birth' => '1942-08-30',
            'place_of_birth' => 'Philadelphia, PA',
            'status' => 'alive',
            'email' => 'susan.doe@example.com',
            'phone' => '+1-555-0102',
            'occupation' => 'Teacher',
            'created_by' => $admin->id,
        ]);

        \App\Models\Marriage::create([
            'husband_id' => $michaelGen4->id,
            'wife_id' => $susanGen4->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        // Generation 5 (born ~1970-1985)
        $davidGen5 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'David',
            'middle_name' => 'Michael',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1970-05-15',
            'place_of_birth' => 'New York, NY',
            'father_id' => $michaelGen4->id,
            'mother_id' => $susanGen4->id,
            'status' => 'alive',
            'email' => 'david.doe@example.com',
            'phone' => '+1-555-0103',
            'address' => '789 Broadway',
            'city' => 'New York',
            'country' => 'USA',
            'occupation' => 'Software Engineer',
            'biography' => 'Tech entrepreneur and family historian. Working on digitizing family records.',
            'created_by' => $admin->id,
        ]);

        $sarahGen5 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Sarah',
            'middle_name' => 'Elizabeth',
            'last_name' => 'Doe',
            'maiden_name' => 'Johnson',
            'gender' => 'female',
            'date_of_birth' => '1972-09-08',
            'place_of_birth' => 'Boston, MA',
            'status' => 'alive',
            'email' => 'sarah.doe@example.com',
            'phone' => '+1-555-0104',
            'occupation' => 'Marketing Director',
            'created_by' => $admin->id,
        ]);

        \App\Models\Marriage::create([
            'husband_id' => $davidGen5->id,
            'wife_id' => $sarahGen5->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $jenniferGen5 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Jennifer',
            'middle_name' => 'Lynn',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '1975-12-20',
            'place_of_birth' => 'New York, NY',
            'father_id' => $michaelGen4->id,
            'mother_id' => $susanGen4->id,
            'status' => 'alive',
            'email' => 'jennifer.doe@example.com',
            'phone' => '+1-555-0105',
            'occupation' => 'Architect',
            'created_by' => $admin->id,
        ]);

        // Generation 6 (born ~2000-2015)
        $emilyGen6 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'Emily',
            'middle_name' => 'Grace',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '2000-03-25',
            'place_of_birth' => 'New York, NY',
            'father_id' => $davidGen5->id,
            'mother_id' => $sarahGen5->id,
            'status' => 'alive',
            'email' => 'emily.doe@example.com',
            'occupation' => 'College Student',
            'biography' => 'Studying Computer Science at MIT',
            'created_by' => $admin->id,
        ]);

        $jamesGen6 = Member::create([
            'clan_id' => $doeClan->id,
            'family_id' => $doeMainFamily->id,
            'branch_id' => $nycBranch->id,
            'first_name' => 'James',
            'middle_name' => 'Alexander',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '2005-07-12',
            'place_of_birth' => 'New York, NY',
            'father_id' => $davidGen5->id,
            'mother_id' => $sarahGen5->id,
            'status' => 'alive',
            'occupation' => 'High School Student',
            'created_by' => $admin->id,
        ]);

        echo "✓ Created 16 family members across 6 generations\n";
        echo "\n";
        echo "========================================\n";
        echo "Demo Data Seeded Successfully!\n";
        echo "========================================\n\n";
        
        echo "Login Credentials:\n";
        echo "  Admin:  admin@familytree.com / password\n";
        echo "  Editor: editor@familytree.com / password\n";
        echo "  Viewer: viewer@familytree.com / password\n\n";
        
        echo "Summary:\n";
        echo "  - 3 users (admin, editor, viewer)\n";
        echo "  - 2 clans\n";
        echo "  - 2 families\n";
        echo "  - 2 branches\n";
        echo "  - 16 members (6 generations)\n\n";
        
        echo "Family Tree Structure:\n";
        echo "  Generation 1: John & Mary (Founders, 1850s)\n";
        echo "  Generation 2: Robert & Thomas (Children)\n";
        echo "  Generation 3: John & Elizabeth\n";
        echo "  Generation 4: Michael & Susan\n";
        echo "  Generation 5: David, Sarah, Jennifer\n";
        echo "  Generation 6: Emily, James (Current Youth)\n\n";
    }
}
