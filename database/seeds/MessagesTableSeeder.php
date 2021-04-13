<?php

use Illuminate\Database\Seeder;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = 'Welcome to Our Great Community!
                    It is my great pleasure to welcome you to the AlumniMatch community. 
                    Here you will find a fun, safe, and trusted digital connection between you and all of your college alumni. 
                    My advice to you when getting started is to simply click on each of the sections in the Navigation, 
                    which can be found in the bottom left by clicking the Menu looking button. 
                    You can view my profile to learn more about me and PLEASE be sure to update your location settings to ON and let other alumni know where you are in a controlled, secure way. 
                    Build incredible relationships and live a more fulfilling and meaningful life because of people from your alma mater. 
                    Sincerely, Jon Lunardi, Creator of AlumniMatch';
        $messages = [
//            [
//                'sid' => 1,
//                'rid' => 2,
//                'title' => 'Welcome to Our Great Community!',
//                'content' => $content
//            ]
        ];

        foreach ($messages as $message) {
            \App\Models\Message::create($message);
        }
    }
}
