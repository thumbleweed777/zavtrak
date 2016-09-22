<?php

 $exist = $db->super_query("SELECT id  FROM " . PREFIX . "_taginator WHERE tag='{$request}' OR slug='{$slug}' ", true);