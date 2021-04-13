<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CollegesTableSeeder::class);
        $this->call(AthletesTableSeeder::class);
        $this->call(DegreesTableSeeder::class);
        $this->call(IbcsTableSeeder::class);
        $this->call(IndustriesTableSeeder::class);
        $this->call(OrganizationsTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UserLoginsTableSeeder::class);
        $this->call(UserCoordsTableSeeder::class);
        $this->call(HobbiesTableSeeder::class);
        $this->call(UserInviteCodesTableSeeder::class);

        $this->call(UserMatchWeightsTableSeeder::class);
        $this->call(UserDegreesTableSeeder::class);
        $this->call(UserOrgsTableSeeder::class);
        $this->call(UserAthletesTableSeeder::class);
        $this->call(UserGenderAgesTableSeeder::class);
        $this->call(UserEthnicitiesTableSeeder::class);
        $this->call(UserSpeakLanguagesTableSeeder::class);
        $this->call(UserLearnLanguagesTableSeeder::class);
        $this->call(UserLearnLanguageScalesTableSeeder::class);
        $this->call(UserReligionsTableSeeder::class);
        $this->call(UserRelationshipsTableSeeder::class);
        $this->call(UserRelationshipMarriedTableSeeder::class);
        $this->call(UserRelationshipSingleTableSeeder::class);
        $this->call(UserRelationshipKidsTableSeeder::class);
        $this->call(UserRelationshipFoodsTableSeeder::class);
        $this->call(UserWorkCareersTableSeeder::class);;
        $this->call(UserIndustiesTableSeeder::class);
        $this->call(UserHomesTableSeeder::class);
        $this->call(UserHomeTownsTableSeeder::class);
        $this->call(UserHealthsTableSeeder::class);
        $this->call(UserHobbiesTableSeeder::class);
        $this->call(UserCausesTableSeeder::class);
        $this->call(UserSchoolsTableSeeder::class);
        $this->call(VisitsTableSeeder::class);
        $this->call(FriendRequestsTableSeeder::class);
        $this->call(FriendsTableSeeder::class);
        $this->call(MessagesTableSeeder::class);
    }
}
