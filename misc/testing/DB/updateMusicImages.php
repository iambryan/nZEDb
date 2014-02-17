<?php
require_once dirname(__FILE__) . '/../../../www/config.php';

$db = new DB();
$covers = $updated = $deleted = 0;
$c = new ColorCLI();

$row = $db->queryDirect("SELECT value FROM site WHERE setting = 'coverspath'");
if ($row) {
	Util::setCoversConstant($row[0]['value']);
} else {
	die("Unable to set Covers' constant!\n");
}
$path2covers = nZEDb_COVERS . 'music' . DS;

if ($argc == 1 || $argv[1] != 'true') {
    exit($c->error("\nThis script will check all images in covers/music and compare to db->musicinfo.\nTo run:\nphp $argv[0] true\n"));
}

$dirItr = new RecursiveDirectoryIterator($path2covers);
$itr = new RecursiveIteratorIterator($dirItr, RecursiveIteratorIterator::LEAVES_ONLY);
foreach ($itr as $filePath) {
    if (is_file($filePath) && preg_match('/\d+\.jpg/', $filePath)) {
        preg_match('/(\d+)\.jpg/', basename($filePath), $match);
        if (isset($match[1])) {
            $run = $db->queryDirect("UPDATE musicinfo SET cover = 1 WHERE cover = 0 AND id = " . $match[1]);
            if ($run->rowCount() >= 1) {
                $covers++;
            } else {
                $run = $db->queryDirect("SELECT id FROM musicinfo WHERE id = " . $match[1]);
                if ($run->rowCount() == 0) {
                    echo $c->info($filePath . " not found in db.");
                }
            }
        }
    }
}

$qry = $db->queryDirect("SELECT id FROM musicinfo WHERE cover = 1");
foreach ($qry as $rows) {
    if (!is_file($path2covers . $rows['id'] . '.jpg')) {
        $db->queryDirect("UPDATE musicinfo SET cover = 0 WHERE cover = 1 AND id = " . $rows['id']);
        echo $c->info($musiccovers . $rows['id'] . ".jpg does not exist.");
        $deleted++;
    }
}
echo $c->header($covers . " covers set.");
echo $c->header($deleted . " music unset.");
