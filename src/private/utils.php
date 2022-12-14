<?php

/**
 * Main class to handle all back-end operations
 * @author Jaume López (EncryptEx)
 */


namespace Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PDO;

# start vendors
require realpath('./../../vendor/autoload.php');

# change timezone
date_default_timezone_set('Europe/Madrid');

# import all credentials to $_ENV superglobal
require 'cred.php';


class Utilities
{

    /**
     * Connects to the DB and creates a new PDO object
     * @return PDO Database connection object 
     */
    private function databaseConnect()
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=UTF8MB4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // highly recommended
            PDO::ATTR_EMULATE_PREPARES => false // ALWAYS! ALWAYS! ALWAYS!
        ];
        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
    }

    /**
     * Checks if that trigger string does exist
     * @return bool True if does exist
     */
    public function doesExist(string $urlTriggerer)
    {
        # Connect to DB
        $pdo = $this->databaseConnect();

        # Check if does exist, either return error msg
        $SQL_SELECT = "SELECT id, name, actionType, triggerOwner FROM `triggers` WHERE stringUrl=:stringUrl LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['stringUrl' => $urlTriggerer];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return ['success' => True, 'id' => $row['id'], 'action' => $row['actionType'], 'ownerId' => $row['triggerOwner'], 'name' => $row['name']];
            }
        }
        return ['success' => False];
    }


    /**
     * Saves into the DB the trigger log: triggerid and timestamp
     * @return bool True if was saved successfully
     */
    public function triggerLog(int $triggerId, string $data)
    {
        $pdo = $this->databaseConnect();

        $SQL_INSERT = "INSERT INTO `action-logs` (id, triggerId, timestamp, extraData) VALUES (NULL, :triggerId, :timestamp, :extraData)";

        $insrtstmnt = $pdo->prepare($SQL_INSERT);

        if ($data != "") {
            // data recieved, save with value
            $input = ['triggerId' => $triggerId, 'timestamp' => time(), 'extraData' => $data];
        } else {
            $input = ['triggerId' => $triggerId, 'timestamp' => time(), 'extraData' => NULL];
        }
        return $insrtstmnt->execute($input);
    }

    /**
     * Returns the name of that actionID
     * @return string Action's name
     */
    public function getActionName(int $actionId)
    {
        # Connect to DB
        $pdo = $this->databaseConnect();

        # Check if does exist, either return error msg
        $SQL_SELECT = "SELECT name FROM `action-types` WHERE id=:id LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['id' => $actionId];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return $row['name'];
            }
        }
        return False;
    }


    /** 
     * Retrieves the Data of that User Id
     * @return array email and name, else returns a bool false
     */
    private function getOwnerData(int $ownerId)
    {
        # Connect to DB
        $pdo = $this->databaseConnect();

        # Check if does exist, either return error msg
        $SQL_SELECT = "SELECT email, name FROM `users` WHERE id=:id LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['id' => $ownerId];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return ['success' => True, 'email' => $row['email'], 'name' => $row['name']];
            }
        }
        return ['success' => False];
    }

    /**
     * Sends an email to the ownerid telling that the triggerId's name has been triggered
     * 
     * @throws http_status_code_500 when fails to retrieve the email
     * 
     * @return array bool: success is false when something went wrong, explained in message
     * 
     */
    public function sendEmailTo(int $ownerId, string $triggerName, string $stringUrl, $dataRecieved='')
    {

        // Initiate mailer class 
        $mail = new PHPMailer();

        //Server settings
        $mail->isSMTP();
        $mail->SMTPDebug = 0; # Set 0 for non-debug, 2 for full debug.
        $mail->SMTPAuth = TRUE;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // tls implicit
        $mail->Port = $_ENV['MAIL_PORT']; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`, ELSE 465  
        $mail->Username = $_ENV['MAIL_SENDER'];
        $mail->Password = $_ENV['MAIL_PWD'];
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Mailer = "smtp";              //Send using SMTP

        // retrieve the data of the trigger's owner
        $ownerData = $this->getOwnerData($ownerId);
        if (!$ownerData['success']) {
            return ['success' => False, 'message' => 'Could not retrieve the owner\'s data'];
        }
        // Add recipient   
        $mail->addAddress($ownerData['email']);

        // Add custom name. from
        $mail->setFrom($_ENV['MAIL_SENDER'], 'Alerts');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = '⌛ ' . htmlentities($triggerName) . ' has completed! ';

        // prepare raw template
        $contentNonParsed = file_get_contents(realpath(__DIR__ . '/templates/email.html'));

        // get actual readable timestamp
        $timestampToPrint = htmlentities(date("d/m/Y H:i:s", time()));

        // replace placeholders from template
        $actualBody = str_replace("{{ username }}",  $ownerData['name'], $contentNonParsed);
        $actualBody = str_replace("{{ timestamp }}", $timestampToPrint, $actualBody);
        $actualBody = str_replace("{{ taskName }}", $triggerName, $actualBody);

        // if data passed, replace it with some nice html, otherwise, remove the template tag
        if ($dataRecieved != "") {
            $dataParsed = "<p>And recieved this data: <code>" . htmlentities($dataRecieved) . "</code></p>";
        } else {
            $dataParsed = "";
        }
        $actualBody = str_replace("{{ extraData }}", $dataParsed, $actualBody);
        if (isset($_SERVER['HTTPS'])) {
            $extraS = "s";
        } else {
            $extraS = "";
        }
        $actualPath = "http" . $extraS . "://" . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_FILENAME']), "", $_SERVER['PHP_SELF']); // if project is in subfolder, useful when coding in local with an ending / 
        $actualBody = str_replace("{{ SERVER_URL }}",  $actualPath . "view.php?t=" . htmlentities($stringUrl), $actualBody);

        $mail->Body    = trim($actualBody);
        // TODO add aLtermative text for non-html users
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $result = $mail->send();

        if (!$result) {
            return ['success' => $result, 'message' => 'Failed when trying to send the email'];
        }
        return ['success' => $result];
    }

    /** 
     * Returns all user data if email and hashed password does match
     * @return array with values: success (bool), name (string), id (int), status (int)
     */
    public function Login(string $email, string $password)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT id, name, status FROM `users` WHERE email=:email AND password=:password LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['email' => $email, 'password' => $password];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return ['success' => True, 'name' => $row['name'], 'id' => $row['id'], 'status' => $row['status']];
            }
        }
        return  ['success' => False];
    }

    /** 
     * If user is not logged in, redirect it to the landing
     * @return redirect
     */
    public function redirectIfNotLogged()
    {
        if (!isset($_SESSION['userid'])) {
            header("location:index.php");
            die("Redirecting...");
        }
    }

    /** 
     * Returns all triggers owned by certain userId
     * @return array if success = true, second element is an array of dictionaries: name, stringUrl, actionType
     * @return array if success = false, nothing attached
     */
    public function getAllTriggersFromUser(int $userid)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT * FROM `triggers` WHERE triggerOwner=:triggerOwner";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerOwner' => $userid];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            $toReturn = ['success' => True];
            $allTriggers = array();
            foreach ($selectStmt as $row) {
                array_push($allTriggers, ['name' => $row['name'], 'stringUrl' => $row['stringUrl'], 'actionType' => $row['actionType']]);
            }
            $toReturn['allTriggers'] = $allTriggers;
            return $toReturn;
        }
        return  ['success' => False];
    }

    /**
     * Returns the data arround that specific stringUrl
     * @return array if success: id, name, stringUrl, actionType
     * @return array if success == false, nothing attached
     */
    public function getTriggerData(int $userid, string $stringUrl)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT * FROM `triggers` WHERE triggerOwner=:triggerOwner AND stringUrl=:stringUrl LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerOwner' => $userid, 'stringUrl' => $stringUrl];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return ['success' => True, 'id' => $row['id'], 'name' => $row['name'], 'stringUrl' => $row['stringUrl'], 'actionType' => $row['actionType']];
            }
        }
        return  ['success' => False];
    }


    /**
     * Retrieves the last timestamp that the triggerId was triggered
     * @return int timestamp, 0 when never
     */
    public function getLastTrigger(int $triggerId)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT timestamp FROM `action-logs` WHERE triggerId=:triggerId ORDER BY `timestamp` DESC LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerId' => $triggerId];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return $row['timestamp'];
            }
        }
        return  0;
    }

    /**
     * Retrieves the number of triggers from a specific triggerId
     * @return int quantity
     */
    public function getTriggerTotal(int $triggerId)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT COUNT(*) FROM `action-logs` WHERE triggerId=:triggerId LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerId' => $triggerId];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                return $row['COUNT(*)'];
            }
        }
        return  0;
    }

    /** 
     * Returns a human redable format date (10 min ago)
     * @return string
     */
    public function time_since($since)
    {
        $chunks = array(
            array(31536000, 'year'),
            array(2592000, 'month'),
            array(604800, 'week'),
            array(86400, 'day'),
            array(3600, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
        return $print;
    }


    /** Returns a string convertible to a JS array with all the timestamps of a triggerId 
     *  but grouping the ocurrences by day, thanks to https://stackoverflow.com/questions/5970938/group-by-day-from-timestamp
     * @return string Javascript array
     */
    public function getHistoricTimestamps($triggerId)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT DATE(FROM_UNIXTIME(timestamp)) AS ForDate, COUNT(*) AS ocurrences FROM `action-logs`   WHERE triggerId=:triggerId GROUP BY DATE(FROM_UNIXTIME(timestamp)) ORDER BY ForDate";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerId' => $triggerId];
        $selectStmt->execute($input);

        $toReturn = "";
        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                $toReturn .= "['" . $row['ForDate'] . "'," . $row['ocurrences'] . "],";
            }
            return substr($toReturn, 0, -1); // remove the final extra comma 
        }
        return  0;
    }

    /** 
     * Returns all action types. by default is an email.
     * succes in pos 'success'
     * @return array action types in pos 'actionTypes', id and name
     * @return array with non-success return
     */
    public function getActionTypes()
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT * FROM `action-types`";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   [];
        $selectStmt->execute($input);

        $toReturn = array();
        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                array_push($toReturn, ['id' => $row['id'], 'name' => $row['name']]);
            }
            return ['success' => True, 'actionTypes' => $toReturn];
        }
        return  ['success' => false];
    }


    /**
     * Generates a random string with n length 
     * thanks to http://stackoverflow.com/questions/4356289/ddg#4356295
     * @return string random chars
     */
    public function generateString(int $length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Saves into the DB a new trigger
     * @return bool True if was saved successfully
     */
    public function saveTrigger(string $name, string $triggerUrl, int $actionType, int $ownerId)
    {
        $pdo = $this->databaseConnect();

        $SQL_INSERT = "INSERT INTO `triggers` (id, stringUrl, name, actionType, triggerOwner) VALUES (NULL, :stringUrl, :name, :actionType, :triggerOwner)";

        $insrtstmnt = $pdo->prepare($SQL_INSERT);

        $input = ['stringUrl' => $triggerUrl, 'name' => $name, 'actionType' => $actionType, 'triggerOwner' => $ownerId];

        return $insrtstmnt->execute($input);
    }

    /**
     * Replaces the name passed of a previously saved trigger 
     * @return bool True if was saved successfully
     */
    public function updateTrigger(int $id, string $name, int $ownerId)
    {
        $pdo = $this->databaseConnect();

        $SQL_UPDATE = "UPDATE `triggers` SET name=:name WHERE id=:id AND triggerOwner=:triggerOwner LIMIT 1";

        $updateStmnt = $pdo->prepare($SQL_UPDATE);

        $input = ['name' => $name, 'id' => $id, 'triggerOwner' => $ownerId];

        return $updateStmnt->execute($input);
    }

    /**
     * Removes a trigger and its historical values 
     * @return bool True if was saved successfully
     */
    public function deleteTrigger(int $id, int $ownerId)
    {
        $pdo = $this->databaseConnect();

        $SQL_DELETE = "DELETE FROM `triggers` WHERE id=:id AND triggerOwner=:triggerOwner LIMIT 1";

        $deleteStmnt = $pdo->prepare($SQL_DELETE);

        $input = ['id' => $id, 'triggerOwner' => $ownerId];

        $firstResult =  $deleteStmnt->execute($input);
        if (!$firstResult) {
            return $firstResult;
        }

        // if removed successfully, continue deleting the historical values
        $SQL_DELETE_LOGS = "DELETE FROM `action-logs` WHERE triggerId=:triggerId";

        $deleteStmnt2 = $pdo->prepare($SQL_DELETE_LOGS);

        $input2 = ['triggerId' => $id];

        return $deleteStmnt2->execute($input2);
    }

    /** 
     * Returns all data with a specified limit, by default is none.
     * Always returns the lastest data (eg limit is 100 => last 100 alerts data)
     * succes in pos 'success'
     * @return array action types in pos 'allData', triggerTimestamp, data
     * @return array with non-success return
     */
    public function getAllData(int $triggerId, int $sqlLimit = 0)
    {
        $pdo = $this->databaseConnect();
        if ($sqlLimit == 0) {
            $limitToPrint = "";
        } else {
            $limitToPrint = "LIMIT " . $sqlLimit;
        }
        $SQL_SELECT = "SELECT * FROM `action-logs` WHERE triggerId=:triggerId  AND extraData IS NOT NULL ORDER BY `timestamp` DESC " . $limitToPrint;
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['triggerId' => $triggerId];
        $selectStmt->execute($input);

        $toReturn = array();
        if ($selectStmt->rowCount() > 0) {
            foreach ($selectStmt as $row) {
                array_push($toReturn, ['extraData' => $row['extraData'], 'timestamp' => $row['timestamp']]);
            }
            return ['success' => True, 'allData' => $toReturn];
        }
        return  ['success' => false];
    }

    public function doesUserExist(string $email)
    {
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT id FROM `users` WHERE email=:email LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['email' => $email];
        $selectStmt->execute($input);

        if ($selectStmt->rowCount() > 0) {
            return True;
        }
        return  False;
    }

    /** 
     * Creates a new user inside the database with status 0 by default
     * @return bool true if saved correctly
     * @return bool false if user already exists or an error ocurred
     */
    public function Register(string $name, string $email, string $password, int $status = 0)
    {
        $pdo = $this->databaseConnect();
        if ($this->doesUserExist($email)) {
            return False;
        }

        $SQL_INSERT = "INSERT INTO `users` (id, name, email, password, status) VALUES (NULL, :name, :email, :password, :status)";

        $insrtstmnt = $pdo->prepare($SQL_INSERT);

        $input = ['name' => $name, 'email' => $email, 'password' => $password, 'status' => $status]; // by default status of account is 0

        return $insrtstmnt->execute($input);
    }

    /** Sends an email verification to the email specified
     * @return bool value is true when email sent correctly
     */
    public function sendEmailVerification(string $email, string $name)
    {
        // Initiate mailer class 
        $mail = new PHPMailer();

        //Server settings
        $mail->isSMTP();
        $mail->SMTPDebug = 0; # Set 0 for non-debug, 2 for full debug.
        $mail->SMTPAuth = TRUE;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // tls implicit
        $mail->Port = $_ENV['MAIL_PORT']; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`, ELSE 465  
        $mail->Username = $_ENV['MAIL_SENDER'];
        $mail->Password = $_ENV['MAIL_PWD'];
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Mailer = "smtp";              //Send using SMTP

        // Add recipient   
        $mail->addAddress($email);

        // Add custom name. from
        $mail->setFrom($_ENV['MAIL_SENDER'], 'Alerty Email Verification');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Confirmation instructions';

        // prepare raw email verification template
        $contentNonParsed = file_get_contents(realpath(__DIR__ . '/templates/verification.html'));

        // replace placeholders from template
        $actualBody = str_replace("{{ username }}",  $name, $contentNonParsed);

        if (isset($_SERVER['HTTPS'])) {
            $extraS = "s";
        } else {
            $extraS = "";
        }
        $actualPath = "http" . $extraS . "://" . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_FILENAME']), "", $_SERVER['PHP_SELF']); // if project is in subfolder, useful when coding in local with an ending / 
        $verifyLink = $actualPath . "verify.php?email=" . htmlentities($email) . "&eh=" . hash("SHA256", $_ENV['HASH_SALT'] . $email);

        $actualBody = str_replace("{{ SERVER_URL }}",  $verifyLink, $actualBody);

        $mail->Body = trim($actualBody);

        $mail->AltBody = 'Welcome ' . htmlentities($name) . "!\n 
        You can confirm your account email through the link below:\n
        " . $verifyLink . "\n This is an automated email";

        $result = $mail->send();

        if (!$result) {
            return ['success' => $result, 'message' => 'Failed when trying to send the email'];
        }
        return ['success' => $result];
    }

    private function isEmailVerified(string $email){
        $pdo = $this->databaseConnect();
        $SQL_SELECT = "SELECT id FROM `users` WHERE email=:email AND status=:status LIMIT 1";
        $selectStmt = $pdo->prepare($SQL_SELECT);
        $input =   ['email' => $email, 'status'=>1];
        $selectStmt->execute($input);
        
        if ($selectStmt->rowCount() > 0) {
            return True;
        }
        return False;
    }

    /** 
     * Verifies a user by setting its status code to 1
     */
    public function Verify(string $email){
        if($this->isEmailVerified($email)) {
            return False;
        }
        $pdo = $this->databaseConnect();

        $SQL_UPDATE = "UPDATE `users` SET status=:status WHERE email=:email LIMIT 1";

        $updateStmnt = $pdo->prepare($SQL_UPDATE);

        $input = ['status' => 1, 'email' => $email];

        return boolval($updateStmnt->execute($input));
    }
}
