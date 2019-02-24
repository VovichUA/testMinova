<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
<?php if (!empty($_SESSION['error'])) :?>
    <div class="alert alert-danger" role="alert">
        <?= $_SESSION['error'] ?>
    </div>
<?php $_SESSION['error'] = null; ?>
<?php endif;?>
        <form action="form.php" method="post">
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <label for="validationServer01">Введите URL-адрес</label>
                    <input type="text" name="url" class="form-control" required>
                </div>
                <div class="col-md-3"></div>
            </div>
            <br>
            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Success</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>