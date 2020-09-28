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
        $idLastMessageFile = $ini['idLastMessageFile'];
        if (file_exists($idLastMessageFile)) {
		$lastMessageId = (int)file_get_contents($idLastMessageFile);
	        $messages = $client->channel->getChannelMessages(['channel.id' => 758584661503901697, 'after' => $lastMessageId]);
	} else {
		$messages = $client->channel->getChannelMessages(['channel.id' => 758584661503901697]);
	}
	if (empty($messages)) {
		write_log_information("keine Nachrichten zu verarbeiten");
	} else {
		foreach ($messages as &$message) {
			write_log_information("start processing message " . $message->content);
			if (empty($message->content)) {
				write_log_information("No content in message");
			} else {
				$answer = get_answer($ini, $message);
				echo $answer;
			}
		}
		$lastMessageId = $messages[0]->id;
	        if (empty($lastMessageId)) {
	        	write_log_information("nothing todo");
	        } else {
	        	file_put_contents($idLastMessageFile, $lastMessageId); 
		}
	}
	
	function get_answer ($configuration, $userinput)
	{
		$servername = $configuration['servername'];
		$username = $configuration['username'];
		$password = $configuration['password'];
		$databasename = $configuration['databasename'];
		// Create connection
		$conn = new mysqli($servername, $username, $password);

		// Check connection
		if ($conn->connect_error) {
			$error_message = "Connection failed: " . $conn->connect_error;
			write_log_information($error_message);
		  	die($error_message);
		}
		$db_selected = mysqli_select_db($databasename, $conn);
		if (!$db_selected) {
			die ('Kann Datenbank ' . $databasename . ' nicht benutzen : ' . $db_selected->error);
		}
		$sql = "SELECT answer FROM question_answers WHERE question = '" . $userinput->content . "'";
		write_log_information("run query: " . $sql);
		$result = $db_selected->query($sql);
		if(!$result) 
		{
			write_log_information('Problem with query: ' . $db_selected->error);
		} else {
			var_dump($result);
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
		    			echo "answer: " . $row["answer"] . "<br>";
		  		}
			} else {
		  		echo "0 results";
			}
		}
		$conn->close();
	}
	
	function write_log_information($log_information)
	{
		file_put_contents('log_'.date("j.n.Y").'.log', date('l jS \of F Y h:i:s A') . " " . $log_information . "\n",FILE_APPEND);
	}

?>
