<?php

use Illuminate\Database\Seeder;

class NotificationTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
    		array( 
    	    'name' => 'win Lms',
            'slug' => 'win_lms',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'Congratulations, you are through to the next round of {{leaugeName}}', 
            'is_active' => 'yes'
          ),
    		array( 
    	    'name' => 'loss Lms',
            'slug' => 'loss_lms',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'YOU ARE OUT! Sorry but your pick lost this week in {{leaugeName}}!', 
            'is_active' => 'yes'
          ),
    		array( 
    	    'name' => 'win Lml',
            'slug' => 'win_lml',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'Congratulations, you got 3 points in Round {{roundNumber}} of {{leaugeName}}', 
            'is_active' => 'yes'
          ),
    		array( 
    	    'name' => 'loss Lml',
            'slug' => 'loss_lml',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'Your pick lost this week in {{leaugeName}}, better luck next round!', 
            'is_active' => 'yes'
          ),
    		array( 
    	    'name' => 'League Join',
            'slug' => 'leaugeJoin',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => '{{userName}} has Joined {{leaugeName}}', 
            'is_active' => 'yes'
          ),
            array( 
            'name' => 'League Join Request',
            'slug' => 'leaugeJoinrequest',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => '{{userName}} has requested to join {{leaugeName}}', 
            'is_active' => 'yes'
          ),

            array( 
            'name' => 'League Join Request Accept',
            'slug' => 'leaugeJoinrequestaccept',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'Your request to Join the League {{leaugeName}} has been {{status}}', 
            'is_active' => 'yes'
          ),  

            array( 
            'name' => 'Make League Admin',
            'slug' => 'makeleagueadmin',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => '{{message}}', 
            'is_active' => 'yes'
          ),    

    		array( 
    	    'name' => 'Team not Picked',
            'slug' => 'team_not_picked',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => 'Please pick your team the next round of {{leaugeName}} is about to start', 
            'is_active' => 'yes'
          ),
            array( 
            'name' => 'Tag User In Comment',
            'slug' => 'tag_user_in_comment',
            'mode' => 'system',
            'type' => 'system' ,
            'long_description' => '{{userName}} tag you in a comment', 
            'is_active' => 'yes'
          )
    	);

    	$mailNotificationTemplate = array(
    		array( 
    	    'name' => 'Loss Lms',
            'slug' => 'loss_lms',
            'mode' => 'mail',
            'type' => 'mail' ,
            'long_description' => 'Hi {{firstName}}<br> We are sorry but you were knocked out of the ‘{{leagueName}}’ because your pick {{teamName}} lost or drew.<br>Don’t worry you can always start a new knock out competition.<br><br>Thanks for playing last man standing!', 
            'is_active' => 'yes'
            ),
            array( 
    	    'name' => 'win Lms',
            'slug' => 'win_lms',
            'mode' => 'mail',
            'type' => 'mail' ,
            'long_description' => 'Hi {{firstName}}<br> congrats you are through to the next round of the ‘{{leagueName}}’ because your pick {{teamName}} won.<br><br>Thanks for playing last man standing!', 
            'is_active' => 'yes'
           ),
            array( 
    	    'name' => 'Loss Lms',
            'slug' => 'loss_lml',
            'mode' => 'mail',
            'type' => 'mail' ,
            'long_description' => 'Hi {{firstName}}<br> You lost of the  ‘{{leagueName}}’ because your pick {{teamName}} lost.<br>Don’t worry try it again.<br><br>Thanks for playing LML!', 
            'is_active' => 'yes'
            ),
            array( 
    	    'name' => 'win Lms',
            'slug' => 'win_lms',
            'mode' => 'mail',
            'type' => 'mail' ,
            'long_description' => 'Hi {{firstName}}<br> congrats you are through to the next round of the ‘{{leagueName}}’ because your pick {{teamName}} won.<br><br>Thanks for playing LML!', 
            'is_active' => 'yes'
           )
    	);


    	foreach ($data as $key => $value) {
	        $Permission = \Modules\Sport\Models\NotificationTemplate::create([
	            'name' => $value['name'],
	            'slug' => $value['slug'],
	            'mode' => $value['mode'],
	            'type' => $value['type'],
	            'long_description' => $value['long_description'],
	            'is_active' => $value['is_active']
	        ]);
        }
        foreach ($mailNotificationTemplate as $key => $value) {
	        $Permission = \Modules\Sport\Models\NotificationTemplate::create([
	            'name' => $value['name'],
	            'slug' => $value['slug'],
	            'mode' => $value['mode'],
	            'type' => $value['type'],
	            'long_description' => $value['long_description'],
	            'is_active' => $value['is_active']
	        ]);
        }
    }
}
