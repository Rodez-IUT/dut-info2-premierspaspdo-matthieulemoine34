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
                                   WHERE U.status_id = ?
                                   AND U.username LIKE ?
                                   ORDER BY username");
            $stmt->execute([2,"e%"]);
            

            while($row = $stmt->fetch()){
     
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['email'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                echo '</tr>';
            }
        ?>

        
        </table>
    
</body>
</html>