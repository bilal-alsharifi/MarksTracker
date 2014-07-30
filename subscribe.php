<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <title></title>
    </head>
    <body>
        <div id="subscribe">
            <?php

            function sanitize($v) {
                $v1 = htmlspecialchars($v);
                $result = mysql_real_escape_string($v1);
                $result = $v1;
                return $result;
            }

            if (isset($_POST['submit'])) {

                $name = sanitize($_POST['name']);
                $email = sanitize($_POST['email']);
                $year = $_POST['year'];

                if (isset($name) && !empty($name) && isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    //connect to database
                    include_once 'config.php';
                    mysql_connect(DB_HOST, DB_USER, DB_PASS);
                    mysql_select_db(DB_NAME);
					mysql_set_charset('utf8');
                    $query = "INSERT INTO `users` (`id`, `name`, `email`, `faculty`, `year`) VALUES (NULL, '{$name}', '{$email}', 15, {$year});";
                    mysql_query($query);
                    echo "{$email} has been added successfully.";
                } else {
                    echo "please check the entered info!";
                }
            } else {
                ?>
                <form method="post">
                    <table>
                        <tr>
                            <td>
                                name : 
                            </td>
                            <td>
                                <input type="text" name="name" id="name" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                email : 
                            </td>
                            <td>
                                <input type="text" name="email" id="email" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                year : 
                            </td>
                            <td>
                                <select name="year" id="year">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>                             
                            </td>
                            <td>
                                <input type="submit" name="submit" id ="submit" />
                            </td>
                        </tr>
                    </table>


                </form>
                <?php
            }
            ?>
        </div>
    </body>
</html>
