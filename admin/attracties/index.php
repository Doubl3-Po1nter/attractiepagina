<?php
session_start();
require_once '../backend/config.php';
if(!isset($_SESSION['user_id']))
{
    $msg = "Je moet eerst inloggen!";
    header("Location: $base_url/admin/login.php?msg=$msg");
    exit;
}
?>

<!doctype html>
<html lang="nl">

<head>
    <title>Attractiepagina / Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@400;600;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/main.css">
    <link rel="icon" href="<?php echo $base_url; ?>/favicon.ico" type="image/x-icon" />
</head>

<body>

    <?php require_once '../../header.php'; ?>
    <div class="container">

        <a href="create.php">Nieuwe attractie maken &gt;</a>

        <form method="POST">
            <select name="sort">
                <option value="">Kies een themagebied</option>
                <option value="familyland">familyland</option>
                <option value="waterland">waterland</option>
                <option value="adventureland">adventureland</option>
            </select>

            <button type="submit">Sorteren</button>
        </form>


        <?php require_once '../backend/conn.php'; 
        $themeland = $_GET['themeland'] ?? '';
        ?>

        <form method="GET" class="filter_form">

                <label>Themagebied:</label>
                <select name="themeland">
                    <option value="">Alles</option>
                    <option value="familyland" <?= (($_GET['themeland'] ?? '') == 'familyland') ? 'selected' : '' ?>>familyland</option>
                    <option value="waterland" <?= (($_GET['themeland'] ?? '') == 'waterland') ? 'selected' : '' ?>>waterland</option>
                    <option value="adventureland" <?= (($_GET['themeland'] ?? '') == 'adventureland') ? 'selected' : '' ?>>adventureland</option>
                </select>

                <button type="submit">Filter</button>
            </form>
        <?php
        require_once '../backend/conn.php';
        if ($themeland !== '')
        {
            $query = "SELECT * FROM rides WHERE themeland = :themeland ORDER BY title ASC";
            $statement = $conn->prepare($query);
            $statement->execute([
                ":themeland" => $themeland
            ]);
        } else 
        {
            $query = "SELECT * FROM rides ORDER BY title ASC";
            $statement = $conn->prepare($query);
            $statement->execute();
        }
        $rides = $statement->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table>
            <tr>
                <th>Titel</th>
                <th>Themagebied</th>
                <th>Min. lengte</th>
                <th>Beschrijving</th>
                <th>Fastpass</th>
                <th>Edit</th>
            </tr>
            <?php foreach($rides as $ride): ?>
                <tr>
                    <td><?php echo $ride['title']; ?></td>
                    <td><?php echo $ride['themeland']; ?></td>
                    <td><?php echo $ride['min_length']; ?></td>
                    <td><?php echo $ride['description']; ?></td>
                    <td>
                        <?php
                        if($ride['fast_pass']) {
                            echo 'Yes';
                        } else {
                            echo 'No';
                        }
                        ?>
                    </td>
                    <td><a href="edit.php?id=<?php echo $ride['id']; ?>">aanpassen</a></td>
                </tr>
            <?php endforeach; ?>
            <p>De lijst bevat <?php echo count($rides); ?> attracties.</p>

            
        </table>


    </div>

</body>

</html>
