<?php

setcookie("token", "", time() - 3600, "/", "", true, true);

header("Location: ../login/index.php");
exit();