// Developed by World Domination Software LLC
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Redirecting to Contact Page</title>
        <meta http-equiv="refresh" content="0;url=contact.php">
    </head>
    <body>
        <?php 
        // Redirect to contact page
        header("Location: contact.php");
        exit();
        ?>
        <p>Redirecting to <a href="contact.php">contact page</a>...</p>

    </body>
</html>
