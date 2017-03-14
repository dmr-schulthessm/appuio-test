<?php
    try {
        $host = getenv('OPENSHIFT_MYSQL_DB_HOST') ?: 'localhost';
        $user = getenv('OPENSHIFT_MYSQL_DB_USERNAME') ?: 'root';
        $pass = getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ?: 'secret';
        $dbname = getenv('OPENSHIFT_MYSQL_DB_DBNAME') ?: 'appuio_test';

        $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }

    if (!empty($_POST)) {
        $username = $_POST['username'];

        if (!$username) {
            throw new \Exception('Invalid username');
        }

        $stmt = $dbh->prepare('INSERT INTO users (username) VALUES (:username)');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    }

    if(!empty($_GET)) {
        if(isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'delete':
                    $stmt = $dbh->prepare('DELETE FROM users WHERE id = :id');
                    $stmt->bindParam(':id', $_GET['id']);
                    $stmt->execute();
                    break;
            }
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="username" autofocus>
        <button type="submit">create</button>
    </form>

    <table>
        <tr>
            <th>Username</th>
            <th>Options</th>
        </tr>
        <?php foreach($dbh->query('SELECT * FROM users') as $user): ?>
            <tr>
                <td><?php echo $user['username']; ?></td>
                <td><a href="index.php?action=delete&id=<?php echo $user['id']; ?>">x</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
