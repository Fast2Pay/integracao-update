<?php
$root = "http://".$_SERVER['HTTP_HOST'];
$root .= dirname($_SERVER['SCRIPT_NAME']);
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>Controle de Saída - Fast2Pay</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link href="<?php echo $root.('/assets/css/style.css'); ?>" rel="stylesheet" type="text/css" />
</head>
<body>
    <header class="navbar navbar-expand flex-column flex-md-row bd-navbar justify-content-center">
        <img src="<?php echo $root.('/assets/image/logo.png'); ?>" alt="Fast2Pay">
    </header>

    <div class="container">
        <div class="search">
            <form action="<?php echo $root.('/controle/search'); ?>" method="POST">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group input-group-lg">
                            <input type="number" class="form-control" placeholder="Código da Mesa..." aria-label="Search for..." pattern="[0-9]*">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary" type="submit">Buscar</button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <img src="<?php echo $root.('/assets/image/logo_footer.png'); ?>" alt="Fast2Pay">
    </footer>

    <script>
        var base_url = "<?php echo $root; ?>";
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

    <!-- SweetAlert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" integrity="sha256-iXUYfkbVl5itd4bAkFH5mjMEN5ld9t3OHvXX3IU8UxU=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js" integrity="sha256-egVvxkq6UBCQyKzRBrDHu8miZ5FOaVrjSqQqauKglKc=" crossorigin="anonymous"></script>

    <script src="<?php echo $root.('/assets/js/common.js') ?>"></script>
</body>
</html>