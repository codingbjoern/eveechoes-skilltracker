#!/usr/bin/env php
<?php
	require_once(__DIR__ . '/vendor/autoload.php');

	use RestCord\DiscordClient;
        use React\EventLoop\Factory;

        $ini = parse_ini_file('config.ini');

	// Client ID of our bot.
	// (Create a new Application at https://discordapp.com/developers/applications/me)
	$clientID = $ini['clientID'];
	$clientSecret = $ini['clientSecret'];

	// Token for our Bot user for the above application.
	$token = $ini['token'];

	// Testing Server and channel. We will send a message here when we start up.
	$testServer = $ini['testServer'];
	$testChannel = $ini['testChannel'];

	// Create the DiscordClient and let the user know how to invite the bot
        $client = new DiscordClient(['token' => $token]);
        $timestampLastMessageFile = $ini['timestampLastMessageFile'];
        if (file_exists($timestampLastMessageFile)) {
		$lastRun = file_get_contents($timestampLastMessageFile);
	        $messages = $client->channel->getChannelMessages(['channel.id' => 758584661503901697, 'channel.after' => "$lastRun"]);
	} else {
		$messages = $client->channel->getChannelMessages(['channel.id' => 758584661503901697]);
	}
	var_dump($messages);
	$lastMessageTimestamp = $messages[0]->timestamp->format('Y-m-d H:i:s.u');
        if (empty($lastMessageTimestamp)) {
        	echo "nothing todo";
        } else {
        	file_put_contents($timestampLastMessageFile, $lastMessageTimestamp); 
	}


