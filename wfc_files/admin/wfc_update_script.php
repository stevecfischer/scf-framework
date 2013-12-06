<?php
/**
* Add update feature to WFC Framework
* Go to 'your site name' in the left sidebar
* then 'Update' box
*
* The update is verified and done from the github
* repository 'scf-framework' and need a version file on it
* with the format Ver_X.X.X.wfc in the name
*
* In order to be able to update, you will need to make
* a diff on files to make sure that you haven't change any framework
* files on your website. This is to avoid any looses when
* doing the update.
*
*
* @package WFC-framework
* @author Thibault Miclo
* @version 1.2
* @since 5.2
*/
/**
* Configure error reporting
* - E_ALL to debug
* - E_ALL ^ E_NOTICE ^ E_STRICT in production
*
* @since 1.0
*/
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
/**
* Configure repo information there
* - GIT_USER, a valid github user
* - GIT_REPO, a valid github repo
*
* @since 1.2
*/
define('GIT_USER','stevecfischer');
define('GIT_REPO','scf-framework');
/**
* List of files
* To ignore them in the diff
*
* @since 1.0
*/
define('IGNORE',serialize(array('.gitattributes','.gitignore','README.md','README')));
/**
* includes the functions used for the update & diffs
* includes the Monitor class to track time & memory
* includes the GRepo class to connect to github
*
* @since 1.0
*/
require_once WFC_ADM.'/wfc_monitor_class.php'; //Monitor class
require_once WFC_ADM.'/wfc_grepo_class.php'; //GRepo Class
/**
* Check if we still have some calls left in the github api
* Since the script doesn't use auth, we have 60 calls each hour
* - Terminates the script if no call left, and diplays the time we will have the reset
* - return the number of calls left
* More infos about the api limit rate : http://developer.github.com/v3/rate_limit/
*
* @since 1.0
* @return int $calls number of calls left
*/
function wfc_callsLeft() {
    //Quick api rate check
    $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context  = stream_context_create($options);
    $limit=json_decode(file_get_contents('https://api.github.com/rate_limit', false, $context));
    if($limit->rate->remaining==0)
    {
        echo '<span style="color:red;font-size:25px;margin-top:25px;">0 call remaining, reset at '.date('h:i:s A',$limit->rate->reset).'</span><br /><br /><br />';
        return 0;
    }
    else
        return $limit->rate->remaining;
}
/**
* Generate a token for the current hour
* Used to make sure that the last diff has been made less than 1h ago
*
* @since 1.0
* @return string $token
*/
function wfc_generateToken() {
    $today=time();
    return $token=sha1(mktime(date('H',$today),0,0,date('n',$today),date('j',$today),date('Y',$today)).$_SERVER['PHP_SELF']);
}
/**
* Put the right content in the update box
* Based on $_GET infos
*
* @since 1.1
* @return function the function launched to display the content
*/
function wfc_manage_update() {
    if(isset($_GET['force_update'])&&$_GET['force_update']==true&&!empty($_POST["update_url"]))
        return wfc_force_update();
    else if(isset($_GET['check_diffs'])&&$_GET['check_diffs']==true)
        return wfc_check_diffs();
    else if(isset($_GET['update']) && $_GET['update']==$token)
        echo wfc_doUpdate();
    else
        return wfc_check_update();
}
/**
* Default view for the update box - FIRST STEP
* Checks versions between local & git
* Displays if an update is available
*
* @since 1.1
*/
function wfc_check_update() {
    echo 'GitHub is broken ? Update from there : <form method="POST" action ="'.$_SERVER['PHP_SELF'].'?page=wfc_theme_customizer.php&force_update=true"><input type="text" value="" name="update_url" /><input type="submit" value="Update" onclick="return confirm(\'Did you do a backup before ? Backup Manager can help you with that !\');" /></form>';
    $gr = new GRepo(GIT_USER, GIT_REPO);
    $loc=get_local_version();
    $git=get_git_version();
    echo 'Git : '.$git.' - <a href="'.$_SERVER['PHP_SELF'].'?page=wfc_theme_customizer.php&force_refresh">Refresh</a>';
    echo '<br />Local : '.$loc.'<br />';
    if(!$git)
        echo 'A version file is missing on git, need to stop there.';
    else if(!$loc)
        echo 'A version file is missing on local, someone has probably made changes, do not update.';
    else
    {
        $mostRecent=version_comparee($loc, $git);
        if($mostRecent==0)
            echo 'Both same version, we are fine !';
        else if($mostRecent>-1)
            echo 'Local has a higher version, someone has probably made changes, do not update.';
        else
            echo 'An update is available : <strong>Version '.$git.'</strong><br />
        <form method="POST" action ="'.$_SERVER['PHP_SELF'].'?page=wfc_theme_customizer.php&check_diffs=true"><input type="submit" value="Check diffs" /></form>';
    }
}
/**
* Diffs view for the update box - SECOND STEP
* Download user current version on github
* Make diffs between user local file and this old version
* - All same : Safe update
* - Files differ : Displays a % of files safe, warns the user
*
* @since 1.1
* @return string $return content for the update box
*/
function wfc_check_diffs() {
    if(isset($_GET['check_diffs'])&&$_GET['check_diffs']==true){
        $gr = new GRepo(GIT_USER, GIT_REPO);
        //Time to check if the server files are all the same as last version !
        //First, download the version of the server on git
        $tags=$gr->getRepoTags();
        $git=get_git_version();
        $loc=get_local_version();
        $exists=false;
        $return='';
        foreach($tags as $tag) if(substr($tag->name,1)==$loc){
            $return.='Found : '.$tag->zipball_url.'<br />';
            $exists=true;
            $target_url = $tag->zipball_url;
            $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
            if(!file_exists(WFC_PT.'../working_directory'))
               mkdir(WFC_PT.'../working_directory');
            else
            {
                rrmdir(WFC_PT.'../working_directory');
                mkdir(WFC_PT.'../working_directory');
            }
             $file_zip = WFC_PT.'../working_directory/Ver_'.substr($tag->name,1).'.zip';
             //echo "<br>Starting<br>Target_url: $target_url";
             //echo "<br>Headers stripped out";
             // make the cURL request to $target_url
             $ch = curl_init();
             $fp = fopen($file_zip, "w+");
             curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
             curl_setopt($ch, CURLOPT_URL,$target_url);
             curl_setopt($ch, CURLOPT_FAILONERROR, true);
             curl_setopt($ch, CURLOPT_HEADER,0);
             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
             curl_setopt($ch, CURLOPT_AUTOREFERER, true);
             curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
             curl_setopt($ch, CURLOPT_TIMEOUT, 10);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
             curl_setopt($ch, CURLOPT_FILE, $fp);
             $page = curl_exec($ch);
             if (!$page) {
               $return.= "<br />cURL error number:" .curl_errno($ch);
               $return.= "<br />cURL error:" . curl_error($ch);
               return $return;
             }
             curl_close($ch);
             //echo "<br />Downloaded file: $target_url";
             // echo "<br />Saved as file: $file_zip";
             // echo "<br />About to unzip ...";
             // Un zip the file
             $zip = new ZipArchive;
               if (! $zip) {
                 $return.="<br>Could not make ZipArchive object.";
                 return $return;
               }
               if($zip->open("$file_zip") != "true") {
                   $return.= "<br>Could not open $file_zip";
                   return $return;
                     }
               $zip->extractTo(WFC_PT.'../working_directory/');
               $zip->close();
             //echo "<br />Unzipped file.<br /><br />";
             //Need to get folder name
            $folder_name='';
            if ($handle = opendir(WFC_PT.'../working_directory/')) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        if(is_dir(WFC_PT.'../working_directory/'.$entry))
                            $folder_name=$entry;
                    }
                }
                closedir($handle);
            }
            if($folder_name!='') {
                //Process diff on files
                $old_files=array();
                $current_files=array();
                $path_to_old=WFC_PT.'../working_directory/'.$folder_name.'/wfc_files/';
                $path_to_current=WFC_PT;
                $old_nb_carac=strlen($path_to_old)+1;
                $current_nb_carac=strlen($path_to_current)+1;
                $old_files=listFolderFilesArr($path_to_old,array(),$old_nb_carac,$old_files);
                $current_files=listFolderFilesArr($path_to_current,array(),$current_nb_carac,$current_files);
                $old_files=array_map('sha1_file',$old_files);
                $current_files=array_map('sha1_file', $current_files);
                /*
                echo '<pre>';
                print_r($old_files);
                print_r($current_files);
                echo '</pre>';
                */
                $missing='';
                $do_update=true;
                $good=0;
                $bad=0;
                foreach($old_files as $f=>$sha) if(!in_array($f, unserialize(IGNORE))) {
                    if(array_key_exists ($f, $current_files)) {
                        if($sha==$current_files[$f]){
                            $return.= $f.' <span style="color:green;font-weight:bolder;">OK</span><br />';
                            $good++;
                        }
                        else
                        {
                            $do_update=false;
                            $bad++;
                            $return.= $f.' <span style="color:red;font-weight:bolder;">NO</span><br />';
                        }
                        unset($current_files[$f]);
                    }
                    else
                    {
                        $do_update=false;
                        $bad++;
                        $missing.='<span style="color:#FF6600;font-weight:bold;">File : <strong style="color:black;">'.$f.'</strong> is missing on local.</span><br />';
                    }
                }
                foreach($current_files as $f=>$sha) if(!in_array($f, unserialize(IGNORE))) {
                    $do_update=false;
                    $bad++;
                    $missing.='<span style="color:#FF6600;font-weight:bold;">File : <strong style="color:black;">'.$f.'</strong> is missing on GitHub.</span><br />';
                }
                $percent=$good/($good+$bad);
                if($do_update)
                    $message='Everything is fine, the system is safe to be updated.<br /><br />
                            Although all the best conditions are regrouped, you should backup your installation using the backup-manager plugin.<br />
                            To view the result of the tests, click outside of the box or on the cross in the top right corner. A button will allow to update from there too.<br />
                            Click on the button to proceed to the update : <form method="POST" action ="'.$_SERVER['PHP_SELF'].'?update='.$token.'"><input type="submit" value="Update" /></form>';
                else
                    $message='Some conditons are missing.<br /><br />
                            The system is '.wfc_display_percent($percent).' % safe to be upgraded, please read carefully the result of the test.<br />
                            If you still want to update, a button will allow you to do so from there.<br />
                            <form method="POST" action ="'.$_SERVER['PHP_SELF'].'?page=wfc_theme_customizer.php&update='.$token.'"><input type="submit" value="Update" /></form>';
                $return.=$missing.$message;
            }
            else
                $return.='Unable to find the name of the folder where the old version has been unzipped..<br />';
        }
    if(!$exists)
            $return.='The local version doesn\'t exists on git, someone has probably made changes, do not update';
    }
    else
        $return.='No $_GET data.';
    return $return;
}
/**
* View to do the update - THIRD STEP
* Verifies the token has been generated in this hour
* Downloads the last version on github
* Does the update by replacing the old wfc_files folder by the new
*
* @since 1.0
* @return string $result content for the update box
*/
function wfc_doUpdate() {
    if(isset($_GET) && !empty($_GET) && $_GET['update']==$token) {
        if(file_exists(WFC_PT.'../working_directory')) {//GO UPDATE
            $folder_name='';
            if ($handle = opendir(WFC_PT.'../working_directory')) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        if(is_dir(WFC_PT.'../working_directory/'.$entry))
                            $folder_name=$entry;
                    }
                }
                closedir($handle);
            }
            if($folder_name!='') {
                $path_to_old=WFC_PT.'../working_directory/'.$folder_name;
                rrmdir($path_to_old);
            }

            $path_to_current=WFC_PT.'../wfc_files/';

            $gr = new GRepo(GIT_USER, GIT_REPO);
            $tags=$gr->getRepoTags();
            $git=get_git_version();
            $loc=get_local_version();
            $exists=false;
            $return='';
            foreach($tags as $tag) if(substr($tag->name,1)==$git){
                $return.='Found : '.$tag->zipball_url.'<br />';
                $exists=true;
                $target_url = $tag->zipball_url;
                $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
                if(!file_exists(WFC_PT.'../working_directory'))
                    mkdir(WFC_PT.'../working_directory');
                $file_zip = WFC_PT.'../working_directory/Ver_'.substr($tag->name,1).'.zip';
                //echo "<br>Starting<br>Target_url: $target_url";
                //echo "<br>Headers stripped out";
                // make the cURL request to $target_url
                $ch = curl_init();
                $fp = fopen($file_zip, "w+");
                curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
                curl_setopt($ch, CURLOPT_URL,$target_url);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                curl_setopt($ch, CURLOPT_HEADER,0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_FILE, $fp);
                $page = curl_exec($ch);
                if (!$page) {
                   $return.= "<br />cURL error number:" .curl_errno($ch);
                   $return.= "<br />cURL error:" . curl_error($ch);
                   return $return;
                }
                curl_close($ch);
                 //echo "<br />Downloaded file: $target_url";
                 // echo "<br />Saved as file: $file_zip";
                 // echo "<br />About to unzip ...";
                 // Un zip the file
                $zip = new ZipArchive;
                if (! $zip) {
                    $return.="<br>Could not make ZipArchive object.";
                    return $return;
                }
                if($zip->open("$file_zip") != "true") {
                    $return.= "<br>Could not open $file_zip";
                    return $return;
                }
                $zip->extractTo(WFC_PT.'../working_directory/');
                $zip->close();
            }
            $folder_name='';
            if ($handle = opendir(WFC_PT.'../working_directory')) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        if(is_dir(WFC_PT.'../working_directory/'.$entry))
                            $folder_name=$entry;
                    }
                }
                closedir($handle);
            }

            if($folder_name!='') {
                $path_to_old=WFC_PT.'../working_directory/'.$folder_name.'/wfc_files';
                //Time to delete all current files
                rrmdir($path_to_current); //custom function, windows...
                if(rename($path_to_old, $path_to_current)) {
                    //Do not forget to update version file !
                    unlink(WFC_PT.'../Ver_'.$loc.'.wfc');
                    $f=fopen(WFC_PT.'../Ver_'.$git.'.wfc','w+');
                    fwrite($f,'VERSION FILE - DO NOT DELETE');
                    fclose($f);
                    $result='WFC theme is now up-to-date !';
                    unset($zip);
                    @chmod(WFC_PT.'../working_directory', 0777);
                    @chmod(WFC_PT.'../working_directory/Ver_'.$git.'.zip', 0777);
                    rrmdir(WFC_PT.'../working_directory');
                }
                else
                    $result='Unable to replace old files.. Change permissions on wfc_files folder.';
            }
            else
                $result='Unable to find the new files, make sure to make the diffs first !';
        }
        else
            $result='Unable to find the old files, make sure to make the diffs first !';
    }
    else if(isset($_GET) && !empty($_GET) && $_GET['update']!=$token)
        $result='Unable to update, the security token is outdated, make sure to make the diffs first !';
    return $result;
}
/**
 * In case of a github failure, or API change 
 * Grab $_POST['update_url'] from the form
 * 
 * Replace with the file from the url
 * No other checking, use carefully
 * @since 1.3
 * @return string $return string to be displayed on the panel
 */
function wfc_force_update() {
    $link=$_POST["update_url"];
    $return='';
    $folder_name='';
   $path_to_current=WFC_PT.'../wfc_files/';
    if(!file_exists(WFC_PT.'../working_directory'))
        mkdir(WFC_PT.'../working_directory');
    else
    {
        rrmdir(WFC_PT.'../working_directory');
        mkdir(WFC_PT.'../working_directory');
    }
    $file_zip = WFC_PT.'../working_directory/Ver_force_update.zip';
    //echo "<br>Starting<br>Target_url: $target_url";
    //echo "<br>Headers stripped out";
    // make the cURL request to $target_url
    $ch = curl_init();
    $fp = fopen($file_zip, "w+");
    $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL,$link);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    $page = curl_exec($ch);
    if (!$page) {
       $return.= "<br />cURL error number:" .curl_errno($ch);
       $return.= "<br />cURL error:" . curl_error($ch);
       return $return;
    }
    curl_close($ch);
     //echo "<br />Downloaded file: $target_url";
     // echo "<br />Saved as file: $file_zip";
     // echo "<br />About to unzip ...";
     // Un zip the file
    $zip = new ZipArchive;
    if (! $zip) {
        $return.="<br>Could not make ZipArchive object.";
        return $return;
    }
    if($zip->open("$file_zip") != "true") {
        $return.= "<br>Could not open $file_zip";
        return $return;
    }
    $zip->extractTo(WFC_PT.'../working_directory/');
    $zip->close();

    $folder_name='';
    if ($handle = opendir(WFC_PT.'../working_directory')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if(is_dir(WFC_PT.'../working_directory/'.$entry))
                    $folder_name=$entry;
            }
        }
        closedir($handle);
    }
    if($folder_name!='') {
        $path_to_old=WFC_PT.'../working_directory/'.$folder_name.'/wfc_files';
        //Time to delete all current files
        rrmdir($path_to_current); //custom function, windows...
        if(rename($path_to_old, $path_to_current)) {
            //Do not forget to update version file !
            unlink(WFC_PT.'../Ver_'.$loc.'.wfc');
            $f=fopen(WFC_PT.'../Ver_1.force.update.wfc','w+');
            fwrite($f,'VERSION FILE - DO NOT DELETE');
            fclose($f);
            $result='WFC theme is now up-to-date !';
            unset($zip);
            @chmod(WFC_PT.'../working_directory', 0777);
            @chmod(WFC_PT.'../working_directory/Ver_force_update.zip', 0777);
            rrmdir(WFC_PT.'../working_directory');
        }
        else
            $result='Unable to replace old files.. Change permissions on wfc_files folder.';
    }
        $result='Unable to find new files.';
}
/**
* Prints api calls left
*
* @since 1.0
*/
function wfc_print_api_limit() {
    $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context  = stream_context_create($options);
    $limit=json_decode(@file_get_contents('https://api.github.com/rate_limit',false,$context));

    echo '<div style="width:100%;text-align:center;"><span style="color:blue;font-size:15px;">'.$limit->rate->remaining.' calls remaining, reset at '.date('h:i:s A',$limit->rate->reset).'</span></div>';
}
/**
* Displays a nicely formated message with various infos :
* - Time for the script
* - Memory max used
*
* @since 1.0
* @param Monitor $monitor A Monitor instance
*/
function wfc_DisplayMonitor($monitor) {
    echo '<div style="width:100%;text-align:center;">Execution time : '.Monitor::HumanTime($monitor->GetElapsedTime()).' - Max memory allocated : '.Monitor::HumanSize($monitor->GetMemoryPeak()).'</div>';
}
/**
* Deletes a dir for real
* Deletes every files in an directory and its subdirectories
* Recursively
*
* @since 1.0
* @param string $path path to the dir you want to delete
*/
function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else @unlink($dir."/".$object);
       }
     }
     reset($objects);
     @rmdir($dir);
   }
 }
/**
* Gets the lastest version on git
*
* @since 1.0
* @return string $version lastest version
*/
function get_git_version() {
    if(empty($_COOKIE['git_version']) || isset($_GET['force_refresh']))
    {
         $gr = new GRepo(GIT_USER, GIT_REPO);
        $ver_git='';
        $all=$gr->getRepoContents('');
        foreach($all as $f) if(substr($f->name,-3)=='wfc'){
                $name=substr($f->name,0,-4);
                 $tempvar=explode('_',$name);
                 if($tempvar[0]=='Ver')
                    $ver_git=$tempvar[1];
            }
        if($ver_git!='')
        {
            setcookie('git_version',$ver_git,time()+3600*24);
            return $ver_git;
        }
        else
            return false;
    }
    else
        return $_COOKIE['git_version'];
}
/**
* Gets the local version
*
* @since 1.0
* @return string $version local version
*/
function get_local_version() {
    $ver_local='';
    if ($handle = opendir(WFC_PT.'..')) {
        while (false !== ($entry = readdir($handle)))if(substr($entry,-3)=='wfc') {
            $name=substr($entry,0,-4);
            $tempvar=explode('_',$name);
            if($tempvar[0]=='Ver')
                return $ver_local=$tempvar[1];
            else
                return false;
        }
        closedir($handle);
    }
    return ($ver_local!='') ? $ver_local : false;
}
/**
* Reads a file
*
* @since 1.0
* @deprecated not used anymore
* @param string $path path to file
* @return string $content content of a file
*/
function read_file($entry) {
    return file_get_contents($entry);
}
/**
* Compares 2 versions
*
* @since 1.0
* @param string $version1 a version string 'X.X.X'
* @param string $version2 a version string 'X.X.X'
* @return string $max the highest version
*/
function version_comparee($ver1,$ver2) {
    return version_compare($ver1,$ver2);
}
/**
* lists all the files in a folder and returns them in an array
* Also lists subfolders files, with relative path
* Return is : $files[relative/path/to/file]=absolute/path/to/file
*
* @since 1.0
* @param string $dir path to the dir
* @param array $exclude files to exclude in the array
* @param int $strip number of caracters to strip to get relative path from absolute
* @param array $files an array in which datas will be stored
* @return array $files the param array filled with datas
*/
function listFolderFilesArr($dir,$exclude=array(),$strip=0,$files){
    $ffs = @scandir($dir);
    foreach($ffs as $ff){
        if(is_array($exclude) and !in_array($ff,$exclude)){
            if($ff != '.' && $ff != '..'){
            if(!is_dir($dir.'/'.$ff)){
                $files[substr(ltrim($dir.'/'.$ff,'./'),$strip)]=ltrim($dir.'/'.$ff,'./');
            }
            if(is_dir($dir.'/'.$ff)) $files=listFolderFilesArr($dir.'/'.$ff,$exclude,$strip,$files);
            }
        }
    }
    return $files;
}
/**
* Displays percentage in a colorful way depending on how close it is to 100
*
* @since 1.2
* @param int $p percentage
* @return string $str colored percentage
*/
function wfc_display_percent($p) {
    $p=round($p*100);
    if($p<50)
        return '<span style="font-weight:bolder;color:red">'.$p.'</span>';
    else if($p<80)
        return '<span style="font-weight:bolder;color:orange">'.$p.'</span>';
    else
        return '<span style="font-weight:bolder;color:green">'.$p.'</span>';
}
