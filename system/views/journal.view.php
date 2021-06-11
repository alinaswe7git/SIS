<?php
class journal {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}
	private function insertJournal($jid,$aid,$doi,$title,$simplePara,$author,$copyright,$pii){
		
		$sql = "INSERT INTO `journals` (`jid`, `aid`, `doi`, `title`, `simplePara`, `author`, `copyright`,`pii`) 
		VALUES ('$jid','$aid','$doi','$title','$simplePara','$author','$copyright','$pii');";
		
		$run = $this->core->database->doInsertQuery($sql);
		
		$ID = $this->core->database->id();
		return $ID;
	}
	private function updateurlJournal($id,$url){
		
		$sql = "UPDATE `journals` SET `url`='$url' WHERE `ID`='$id'";
				
		$run = $this->core->database->doInsertQuery($sql);
		
		$ID = $this->core->database->id();
		return;
	}
	private function processJournal($file){
		
		$jid="";$aid="";$doi="";$title="";$affiliation="";$simplePara="";$author="";$copyright="";$pii="";
		
		
		
		$filename = $file;
		$file = file_get_contents($file);

		$file = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $file);
		
		$content = simplexml_load_string($file);
		
		
		$item="item-info";
		
		foreach ($content->$item->children() as $key3=>$value3){
			if($key3 == "ce_doi"){
				$doi = $value3;
				echo $doi .' doi </br>' ;
			
			}
			if($key3 == "aid"){
				$aid = $value3;
				echo $aid .'  aid</br>' ;
			
			}
			if($key3 == "ce_pii"){
				$pii = $value3;
				echo $pii .' pii </br>' ;
			
			}
			if($key3 == "ce_copyright"){
				$copyright = $value3;
				echo $copyright .' copyright </br>' ;
			
			}
			if($key3 == "jid"){
				$jid = $value3;
				echo $jid .' jid </br>' ;
			
			}
			
		}
		
		//var_dump($content);
		
		foreach ($content->head->children() as $key=>$value){
			if($key == "ce_title"){
				$title = $value;
				echo $title.' title </br>' ;
			
			}
			
			if($key == "ce_dochead"){
				
				foreach ($content->head->$key->children() as $key2=>$value2) {
					if($key2 == "ce_textfn"){
						$textfn = $value2;
						//echo $textfn .' ce_textfn </br>' ;
					}
				
				}
			
			}
			
			if($key == "ce_author-group"){
				$name ="";
				foreach ($content->head->$key->children() as $key2=>$value2) {
					
					if($key2 == "ce_author"){
						
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							
							if($key3 == "ce_given-name"){
								if (!empty($value3)||$value3!="")
								{
									$name =$value3;
									//echo $textfn .' ce_author </br></br>' ;
								}
							}
							if($key3 == "ce_surname"){
								
								if (!empty($value3)||$value3!="")
								{
									$name .= ' '.$value3;
								}
							}
							//var_dump($key3);
							//echo 'author ['.$name.']  </br></br>' ;
							
						}
						
					}/*
					if($key == "affiliation"){
				
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							if($key3 == "ce_textfn"){
								$affiliation = $value3;
								echo $affiliation .' affiliation </br>' ;
							}
						
						}
					}
					*/
				
				}
				$author=$name;
				echo 'author ['.$author.']  </br></br>' ;
				
				
			
			}
			
			if($key == "ce_abstract"){
				foreach ($content->head->$key->children() as $key2=>$value2) {
					
					if($key2 == "ce_abstract-sec"){
						
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							
							if($key3 == "ce_simple-para"){
								if (!empty($value3)||$value3!="")
								{
									$simplePara =$value3;
									//echo $textfn .' ce_author </br></br>' ;
								}
							}
														
						}
						
					}
				
				}
				echo 'abstract ['.$simplePara.']  </br></br>' ;
			
			}
			
		}
		
		$ID = $this->insertJournal($jid,$aid,$doi,$title,$simplePara,$author,$copyright,$pii);
		
		
		
		return $ID;
	}

	public function importJournal(){
		$dir = $this->core->conf['conf']['dataStoreJournal'] . 'data/';
		$loc_='';
		$loc='';

		$di = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($di) as $filename => $file) {

			if(is_dir($file)){
								
				continue;
			} else if (basename($file) == "main.xml"){
				
				$loc=$file;
				$add = str_replace($dir,"",$loc);
				$loc_=str_replace("..","",$add);
				
				$id=$this->processJournal($file);
				
				$this->updateurlJournal($id,$loc_);
				
				echo $loc_."this is the url for $id </br>";
			}
		}
		//echo $loc.'    this is the url </br>';
	}
	public function manageJournal($item) {
		
		//echo'<h2><img src="https://access.astrialibrary.com/themes/polaris/asset/img/logo/logo.png?v=1.1.4"><br><br> To view other articles see the <a href="https://access.astrialibrary.com/s/mu/page/welcome">Astria Digital Library</a><br>Login with your EduRole Username/Password and go to Articles<br><br></h2>';
		echo'<form id="narrow" name="narrow" method="get" action="">
				<div class="toolbaritem" style="height: 80px; text-align: left">
					<span style="width: 80px; display: inline-block;">Title:</span> 
					<input type="text" name="title" class="submit" style="width: 230px; margin-top: -17px;"/>
					<span style="width: 80px; display: inline-block;">Author:</span> 
					<input type="text" name="author" class="submit" style="width: 230px; margin-top: -17px;"/><br>
					<span style="width: 80px; display: inline-block;">Abstract:</span> 
					<input type="text" name="abstract" class="submit" style="width: 230px;;"/>
					<span style="width: 80px; display: inline-block;">Search:</span> 
					<input type="submit" value="Search"  name="submit" style="width: 80px;"/>
			   </div>
			
		</form> <br> <hr>';
		
		if(!empty($_GET['title']) || !empty($_GET['author'])){
			
			$title = $_GET['title'];
			$author = $_GET['author'];
		
			$sql = "SELECT * FROM `journals` WHERE title like '%$title%' AND author like '%$author%'";

			
			$run = $this->core->database->doSelectQuery($sql);

			echo '<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
<hr>			<table style="valign: top;" width="" height="" border="0" cellpadding="3" cellspacing="0">'.
			'<tr class="heading">' .
			'<td width=""><b>Doi</b></td>' .
			'<td width="200px"><b>Title</b></td>' .
			'<td width=""><b>Author</b></td>' .
			'<td width=""><b>Abstract</b></td>' .
			'<td width=""><b>Action</b></td>' .
			'</tr>';

			while ($fetch = $run->fetch_assoc()) {
										
				
				$jid= $fetch['jid'];
				$aid= $fetch['aid'];
				$doi= $fetch['doi'];
				$title= $fetch['title'];
				$simplePara= substr($fetch['simplePara'],0, 250);
				$author= $fetch['author'];
				$copyright= $fetch['copyright'];
				$pii= $fetch['pii'];
				$url = $fetch['url'];
				$url_=str_replace("main.xml","main.pdf",$url);				
				$fullUrl = '/journals/data/'.$url_;
				echo '<tr style="border-bottom: 2px solid #CCC;">
					<td valign="top"><i>' . $doi . '</i></td>
					<td valign="top"><b>' . $title . '</b></td>
					<td valign="top">' . $author . '</td>
					<td valign="top">' . $simplePara. '....</td> ';
				echo '<td><center><a href="'.$fullUrl.'" > <span class="glyphicon glyphicon-file" aria-hidden="true" style="font-size: 20pt;"></span><br>View Document</a><center></td>';
				echo'</td></tr>';

			}

			echo '</table>';
		}
	}
	public function thesisJournal($item) {
		
		echo'<h2>Search Thesis<br><br></h2>';
		echo'<form id="narrow" name="narrow" method="get" action="">
				<div class="toolbaritem" style="height: 80px; text-align: left">
					<span style="width: 80px; display: inline-block;">Title:</span> 
					<input type="text" name="title" class="submit" style="width: 230px; margin-top: -17px;"/>
					<span style="width: 80px; display: inline-block;">Author:</span> 
					<input type="text" name="author" class="submit" style="width: 230px; margin-top: -17px;"/><br>
					<span style="width: 80px; display: inline-block;">Abstract:</span> 
					<input type="text" name="abstract" class="submit" style="width: 230px;"/>
					<span style="width: 80px; display: inline-block;">Search:</span> 
					<input type="submit" value="Search"  name="submit" style="width: 80px;"/>
			   </div>
			
		</form> <br> <hr>';
		
		if(!empty($_GET['title']) || !empty($_GET['author'])){
			
			$title = $_GET['title'];
			$author = $_GET['author'];
		
			$sql = "SELECT * FROM `thesis-information` WHERE Title like '%$title%' AND Author like '%$author%'";

			
			$run = $this->core->database->doSelectQuery($sql);

			echo '<div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
<hr>			<table style="valign: top;" width="" height="" border="0" cellpadding="3" cellspacing="0">'.
			'<tr class="heading">' .
			'<td width="200px"><b>Title</b></td>' .
			'<td width=""><b>Author</b></td>' .
			'<td width=""><b>Description</b></td>' .
			'<td width=""><b>Supervisor</b></td>' .
			'<td width=""><b>School</b></td>' .
			'<td width=""><b>Action</b></td>' .
			'</tr>';

			while ($fetch = $run->fetch_assoc()) {
										
				
				$title= $fetch['Title'];
				$supervisor= $fetch['Supervisor'];
				$school= $fetch['SchoolID'];
				$description= substr($fetch['Description'],0, 250);
				$author= $fetch['Author'];
				$url = $fetch['URL'];
				$fullUrl = '/datastore/journals/'.$url;
				echo '<tr style="border-bottom: 2px solid #CCC;">
					<td valign="top"><b>' . $title . '</b></td>
					<td valign="top">' . $author . '</td>
					<td valign="top">' . $description. '....</td> 
					<td valign="top">' . $supervisor . '</td>
					<td valign="top">' . $school . '</td>';
				echo '<td><center><a href="'.$fullUrl.'" > <span class="glyphicon glyphicon-file" aria-hidden="true" style="font-size: 20pt;"></span><br>View Document</a><center></td>';
				echo'</td></tr>';

			}

			echo '</table>';
		}
	}
	public function savethesisJournal($item) {
		$Title = $this->core->cleanPost['Title'];
		$StudentID = $this->core->cleanPost['StudentID'];
		$SchoolID = $this->core->cleanPost['SchoolID'];
		$Author = $this->core->cleanPost['Author'];
		$Supervisor = $this->core->cleanPost['Supervisor'];
		$Description = $this->core->cleanPost['Description'];
		$uid = $this->core->userID;
		
		if (isset($_FILES["file"])) {

			$file = $_FILES["file"];
		
			$home = getcwd();
			$path = $this->core->conf['conf']["dataStorePath"] . 'journals/' . $course;

	
		
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $file["error"]["file"] . "<br>";
			} else {
		
				$fname = $_FILES["file"]["name"];
				$destination = $path."/".$fname;
		
				if (file_exists($destination)) {
					$fname = rand(1,999) . '-' .$fname;
					$destination = $path."/".$fname;
				}

				move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
				
				if(file_exists($destination)){
					echo'<div class="successpopup">File uploaded as '.$fname.'</div>';
				}
			}
		}
		$base = $this->core->conf['conf']['path'] . '/datastore/journals/'. $fname;
		
		
		
		$sql = "INSERT INTO `thesis-information` (`Title`, `StudentID`, `CreaterID`, `URL`, `SchoolID`, `Author`, `Supervisor`, `Description`,`DateTime`) VALUES ( '$Title', '$StudentID', '$uid ', '$base ', '$SchoolID', '$Author', '$Supervisor', '$Description', NOW());";
		
		$run = $this->core->database->doInsertQuery($sql);
		
		//$this->core->redirect("information/show", $item);
		if($run){
			echo '<b>Add Success</b> </br><div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/journal/thesis/" >Back To thesis search</a></div>';
		}
	}
}
?>
