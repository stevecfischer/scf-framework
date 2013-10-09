<?php
/*
* Road map
*
* 1st check : versions - server vs git - file called ver_x.x.x.wfc 			<<< DONE
*						Match ? -> No update, DONE                 			<<< DONE 
*						Don't Match ? Update needed, 2nd check     			<<< DONE
* 
* 2nd check : get server version, get github files for this old version     <<< DONE
*					check if files match 									<<< DONE
*					No ? -> client has made changes, stop there 			<<< DONE
*					Yes ? -> No changes, safe to update 					<<< DONE
*			   
* Update : zip download, replace of a folder, start investigating
*
*/
error_reporting(E_ALL);
define('GIT_USER','stevecfischer');
define('GIT_REPO','scf-framework');

include 'wfc_update_functions.php';

function wfc_callsLeft() {
	//Quick api rate check
	$limit=json_decode(@file_get_contents('https://api.github.com/rate_limit'));
	if($limit->rate->remaining==0)
		exit('<span style="color:red;font-size:25px;margin-top:25px;">0 call remaining, reset at '.date('h:i:s A',$limit->rate->reset).'</span><br /><br /><br />');
	else
		return $limit->rate->remaining;
}

function wfc_generateToken() {
	$today=time();
	return $token=sha1(mktime(date('H',$today),0,0,date('n',$today),date('j',$today),date('Y',$today)).$_SERVER['PHP_SELF']);
}
function wfc_manage_update() {
	if(isset($_GET['check_diffs'])&&$_GET['check_diffs']==true)
		return wfc_check_diffs();
	else if(isset($_GET['update']) && $_GET['update']==$token)
		return wfc_doUpdate();
	else
		return wfc_check_update();
}
function wfc_check_update() {
	$gr = new GRepo(GIT_USER, GIT_REPO);
	echo 'Git : '.$git=get_git_version();
	echo '<br />Local : '.$loc=get_local_version().'<br />';
	if(!$git)
		echo 'A version file is missing on git, need to stop there.';
	else if(!$loc)
		echo 'A version file is missing on local, someone has probably made changes, do not update.';
	else
	{
		$mostRecent=version_comparee($loc, $git);
		if(!$mostRecent)
			echo 'Both same version, we are fine !';
		else if($mostRecent==$loc)
			echo 'Local has a higher version, someone has probably made changes, do not update.';
		else
			echo 'An update is available : <strong>Version '.$git.'</strong><br />
		<form method="POST" action ="'.$_SERVER['PHP_SELF'].'?page=wfc_theme_customizer.php&check_diffs=true"><input type="submit" value="Check diffs" /></form>';
	}
}
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
		foreach($tags as $tag) if($tag->name==$loc){
			$return.='Found : '.$tag->zipball_url.'<br />';
			$exists=true;
			 $target_url = $tag->zipball_url;  
			 $userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)'; 
			 if(!file_exists(WFC_PT.'../working_directory'))
				mkdir(WFC_PT.'../working_directory'); 
			 $file_zip = WFC_PT.'../working_directory/Ver_'.$tag->name.'.zip';  
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
			foreach($tags as $tag) if($tag->name==$git){
				$return.='Found : '.$tag->zipball_url.'<br />';
				$exists=true;
				$target_url = $tag->zipball_url;  
				$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)'; 
				if(!file_exists(WFC_PT.'../working_directory'))
					mkdir(WFC_PT.'../working_directory'); 
				$file_zip = WFC_PT.'../working_directory/Ver_'.$tag->name.'.zip';  
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

function wfc_print_api_limit() {
	$limit=json_decode(@file_get_contents('https://api.github.com/rate_limit'));

	echo '<div style="width:100%;text-align:center;"><span style="color:blue;font-size:15px;">'.$limit->rate->remaining.' calls remaining, reset at '.date('h:i:s A',$limit->rate->reset).'</span></div>';
}
function wfc_DisplayMonitor($monitor) {
	echo '<div style="width:100%;text-align:center;">Execution time : '.Monitor::HumanTime($monitor->getElapsedTime()).' - Max memory allocated : '.Monitor::HumanSize($monitor->GetMemoryPeak()).'</div>';
}

?>
