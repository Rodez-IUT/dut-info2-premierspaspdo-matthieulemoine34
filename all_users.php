<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>tous les utilisateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>

    <?php
        $host = "localhost";
        $db   = "my-activities";
        $user = "root";
        $pass = "root";
        $charset = "utf8mb4";
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
             $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
             throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    ?>

    <form method="get" action="all_users.php">
        <label for="debut">Commence par la lettre : </label>
        <input type="text" name="debut" id="debut" size="2">
        <label for="statu">  le statu doit etre : </label>
        <select name="statu" id="statu">
            <option value=""></option>
            <option value=1>Waiting for account validation</option>
            <option value=2>Active account</option>
            <option value=3>Waiting for account deletaion</option>
        </select>
        <input type="submit" value="Ok">
    </form>

    <h1> ALL USER </h1>

        <table>
            <tr>
            <td>ID</td>
            <td>Username</td>
            <td>Email</td>
            <td>Status</td>
            </tr>


        <?php

            $stmt = $pdo->prepare("SELECT U.id,U.username,U.email,S.name 
                                   FROM users U 
                                   JOIN status S 
                                   ON S.id = U.status_id
                                   WHERE U.status_id IN (?,?,?)
                                   AND U.username LIKE ?
                                   ORDER BY username");
            $stat[0] = 1;
            $stat[1] = 2;
            $stat[2] = 3;
            $name="%";

            $delet = $pdo->prepare('INSERT INTO action_log (action_date,action_name,user_id)
                                    VALUES (NOW(),"askDeletion",?)');

            $change = $pdo->prepare('UPDATE users 
                                     SET status_id = 3
                                     WHERE id = ?');

            if (isset($_GET['action']) && $_GET['action']=="askDeletion" && isset($_GET['debut'])){
                $stmt->execute([1,2,3,$_GET['debut']]);
                while($rows = $stmt->fetch()){
                    $delet->execute([$rows['id']]);
                    $change->execute([$rows['id']]);
                }
                
            }
            


            if (isset($_GET['statu']) || isset($_GET['debut'])) {
                if (isset($_GET['statu']) && $_GET['statu']!="" && isset($_GET['debut']) && $_GET['debut']!= "") {
                    $stat[0] = $stat[1] = $stat[2] = $_GET['statu'];
                    $name = $_GET['debut']."%";
                } elseif ($_GET['debut']== "" && $_GET['statu'] == "" ){
                    $stat[0] = 1; 
                    $stat[1] = 2;
                    $stat[2] = 3;
                    $name = "%";
                } elseif ($_GET['statu']=="" && $_GET['debut']!= "" ) {
                    $stat[0] = 1;
                    $stat[1] = 2;
                    $stat[2] = 3;
                    $name = $_GET['debut']."%";
                } elseif ($_GET['debut']=="" && $_GET['statu']!="") {
                    $stat[0] = $stat[1] = $stat[2] = $_GET['statu'];
                    $name = "%";
                }
            } else {
                $stat[0] = 1;
                $stat[1] = 2;
                $stat[2] = 3;
                $name = "%";
            }
            
            $stmt->execute([$stat[0],$stat[1],$stat[2],$name]);
            
            
            while($row = $stmt->fetch()){
     
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['email'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                if ($row['name'] != "Waiting for account deletion" ){
                    echo '<td>   
                            <a href="all_users.php?debut='.$row['username'].'&statu=3&action=askDeletion">Ask deletation
                            </a>
                          </td>';
                }
                echo '</tr>';
            }

            


        ?>

        
        
        </table>
    
</body>
</html>