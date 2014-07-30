<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <?php
        include_once 'config.php';
        include_once('PHPMailer_5.2.2/class.phpmailer.php');

        //connect to database
        mysql_connect(DB_HOST, DB_USER, DB_PASS);
        mysql_select_db(DB_NAME);
        mysql_set_charset('utf8');
        $sentMarks = array();
        $users = array();

        //get sent marks
        $query = "SELECT * FROM `sentmarks`";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $sentMarks[] = $row;
        }

        // get users
        $query = "SELECT * FROM `users`";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $users[] = $row;
        }

        // send marks to users in all years
        for ($year = 1; $year <= 5; $year++) {
            echo "<br/>" . "year:{$year}" . "<br/>";
            sendMarksToUsers(15, $year, $sentMarks, $users);
        }

        function sendMarksToUsers($faculty, $year, $sentMarks, $users) {
            $dom = new DOMDocument();
            $dom->loadHTMLFile(BASE_URL . "system/showSubjects.php?facultyID={$faculty}&subjectYear={$year}");
            $subjects = $dom->getElementsByTagName('a');
            foreach ($subjects as $subject) {
                $href = $subject->getAttribute('href');

                if (substr($href, strlen($href) - 3) == 'pdf' && !markAlreadySent($sentMarks, $href)) {
                    $subjectName = $subject->nodeValue;
                    $absoluteHref = BASE_URL . substr($href, 3);
                    echo $subjectName . "<br/>";

                    //prepare email
                    $mail = new PHPMailer();
                    $body = $absoluteHref . SIGNATURE;
                    $mail->CharSet = 'UTF-8';
                    $mail->SetFrom(SUPPORT_MAIL, CORP_NAME);
                    $mail->AddReplyTo(SUPPORT_MAIL, CORP_NAME);
                    $mail->Subject = $subjectName;
                    $mail->MsgHTML($body);

                    //prepare receivers    
                    foreach ($users as $user) {
                        if ($user['faculty'] == $faculty && $user['year'] == $year) {
                            $mail->AddBCC($user['email']);
                            echo "--" . $user['email'] . "<br/>";
                        }
                    }

                    //send email
                    $mail->Send();

                    //save the sentMarks in the database to prevent sending it the next time
                    $query = "INSERT INTO `sentmarks` (`id`, `name`, `href`, `faculty`, `year`, `date`) VALUES (NULL, '{$subjectName}', '{$href}', {$faculty}, {$year}, NOW());";
                    mysql_query($query);
                }
            }
        }

        function markAlreadySent($sentMarks, $href) {
            foreach ($sentMarks as $sentMark) {
                if ($sentMark['href'] == $href) {
                    return true;
                }
            }
            return false;
        }
        ?>



    </body>
</html>
