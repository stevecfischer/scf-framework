<?php
/*
* Functions - update theme WFC
* Thibault Miclo - 09/23/13
* WFC Theme
*
*/
define('IGNORE',serialize(array('.gitattributes','.gitignore','README.md','README')));

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

function get_git_version() {
	$gr = new GRepo('bqk-');
	$ver_git='';
	$all=$gr->getRepoContents('');
	foreach($all as $f) if(substr($f->name,-3)=='wfc'){
			$name=substr($f->name,0,-4);
			 if(explode('_',$name)[0]=='Ver')
				return $ver_git=explode('_',$name)[1];
			else
				return false;
		}
	return ($ver_git!='') ? $ver_git : false;
}

function get_local_version() {
	$ver_local='';
	if ($handle = opendir(WFC_PT.'../')) {
	    while (false !== ($entry = readdir($handle)))if(substr($entry,-3)=='wfc') {
	        $name=substr($entry,0,-4);
	        if(explode('_',$name)[0]=='Ver')
				return $ver_local=explode('_',$name)[1];
			else
				return false;
	    }
	    closedir($handle);
	}
	return ($ver_local!='') ? $ver_local : false;
}

class GRepo
{
    protected 
        // needs "user"
        $src_userRepos = "https://api.github.com/users/%s/repos",
        // needs "user,repo"
        $src_userRepoDetails = "https://api.github.com/repos/bqk-/themeUpdater",
        $responseCode, $responseText,
        $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function listRepos() {
        $this->_request(
            sprintf($this->src_userRepos, $this->user));
        if ($this->responseCode != 200) {
            throw new Exception('Server error!'); // e.g
        }
        return json_decode($this->responseText);
    }

    public function getRepoDetails($repo) {
        $this->_request(
            sprintf($this->src_userRepoDetails, $this->user, $repo));
        if ($this->responseCode != 200) {
            throw new Exception('Server error!'); // e.g
        }
        return json_decode($this->responseText);
    }

    // Could be extended, e.g with CURL..
    private function _request($url) {
        $contents =@ file_get_contents($url);
        $this->responseCode = (false === $contents) ? 400 : 200;
        $this->responseText = $contents;
    }

    public function getRepoTags() {
       	$this->_request('https://api.github.com/repos/bqk-/themeUpdater/tags');
    	if ($this->responseCode != 200) {
            throw new Exception('Server error!'); // e.g
        }
        return json_decode($this->responseText);
    }

    public function getRepoContents($repo) {
       	$this->_request($this->src_userRepoDetails.'/contents'.$repo);
    	if ($this->responseCode != 200) {
            throw new Exception('Server error!'); // e.g
        }
        return json_decode($this->responseText);
    }

    public function getFileContent($html) {
    	$this->_request($html);
    	if ($this->responseCode != 200) {
            throw new Exception('Server error!'); // e.g
        }
        return json_decode($this->responseText);
    }
}
function read_file($entry) {
	return file_get_contents($entry);	
}
function version_comparee($ver1,$ver2) {
	$tab=explode('.',$ver1);
	$tab2=explode('.',$ver2);

	if($tab[0]>$tab2[0])
		return $ver1;
	if($tab2[0]>$tab[0])
		return $ver2;
	if($tab[1]>$tab2[1])
		return $ver1;
	if($tab2[1]>$tab[1])
		return $ver2;
	if($tab[2]>$tab2[2])
		return $ver1;
	if($tab2[2]>$tab[2])
		return $ver2;
	return false;
}


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

function wfc_display_percent($p) {
    $p=round($p*100);
    if($p<50)
        return '<span style="font-weight:bolder;color:red">'.$p.'</span>';
    else if($p<80)
        return '<span style="font-weight:bolder;color:orange">'.$p.'</span>';
    else
        return '<span style="font-weight:bolder;color:green">'.$p.'</span>';
}

?>