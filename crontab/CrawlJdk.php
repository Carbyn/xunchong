<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlJdk', 'run']);

class CrawlJdk {

    public static function run() {
        $dir = dirname(__FILE__).'/jd';
        $files = scandir($dir);
        foreach($files as $file) {
            if ($file == '.' || $file == '..' || strrpos($file, '.csv') === false) {
                continue;
            }
            echo "$file begin\n";
            $cmd = 'php '.APPLICATION_PATH.'/crontab/CrawlJdkWorker.php '.$dir.'/'.$file.' '.$file;
            passthru($cmd);
            echo "$file end\n";
            sleep(3);
        }
        echo "done\n";
    }

}
