<?php
class menuConstruct
{

	public $core;

	function __construct($core)
	{
		$this->core = $core;
	}

	public function buildMainMenu($menudata = FALSE)
	{

		if ($menudata == FALSE) {
			$menu = NULL;
		} else if (isset($this->core->role)) {
			$menu = $this->fillMainMenu();
		}
		
		$menu = $this->menuContainer($menu);

		return $menu;
	}

	public function menuContainer($menu)
	{

		//$container = '<div class="menu">
		//	<ul class="nav side-nav">
		//			<li class="userinfo glow-on-hover">Current user: <strong>' . $this->core->username . '</strong></li>';
		if ($this->core->role != NULL) {
			$container = '<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav" >
                <li>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/personal"><img src="' . $this->core->fullTemplatePath . '/designerpack/person-circle-outline.svg" alt="icon" width="40" height="40"><b>'. $this->core->username . '</b></a>
                </li>
				<li>
                    <a href="' . $this->core->conf['conf']['path'] . '/logout/do"><img src="' . $this->core->fullTemplatePath . '/designerpack/log-out-outline.svg" alt="icon" width="40" height="40">Logout</a>    
                </li>
				<li>
                    <a href="' . $this->core->conf['conf']['path'] . '/password/change"><img src="' . $this->core->fullTemplatePath . '/designerpack/pencil-outline.svg" alt="icon" width="32" height="32">Change Password</a>    
                </li>
				';
		}
		
		$container .= $menu;
		$container .= '</ul>';
		
		if ($this->core->role != 1000){
				$container .= '<div class="nav side-nav" style="margin-top: 70vh;">
                    <li style="margin-top: : 0px;">
                        <div style="display: flex;">
                    <h3 class="nipatitle" style="font-size: 100%; padding-left: 20px; padding-top: 10px;padding-bottom: 10px; font-family: "Blogger Sans", sans-serif;font-weight: bold;border-right:  solid ; padding-right: 8px;">National <br/> Institute Of Public <br/>Administration</h3>
                    <img src="' . $this->core->fullTemplatePath . '/designerpack/logo-large.png" alt="" border="0" style=" width:30%;height:30%; padding: 10px; margin-top: 15px;" />
                    
                    </div>
                    </li>
                    <!--<li style="padding-left: 20px;">copyright NIPA @2021</li>-->
                    <li style="padding-left: 20px;">Licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/">Creative Commons Attribution-NC-ND 3.0 Unported License</a>.</li>

                </div>
				<style>
				 @import url("http://fonts.cdnfonts.com/css/blogger-sans");
				</style>';
		}
		
		$container .= '</div></nav>';
		
		//$container .= '</br></br></br></br></br></br></br></br></br></br>';
		$container .= '<div id="page-wrapper">';
		//$container .= '<div id="wrapper">';
		$container .= '<div class="container-fluid">';
		
		return $container;
	}

	private function fillMainMenu()
	{

		$menu = NULL;

		if ($this->core->role != 1000) {
			$sql = "SELECT *
			FROM `functions-permissions`, `functions`, `roles`
			WHERE `functions`.`FunctionMenuVisible` > 0
			AND `functions-permissions`.RoleID = " . $this->core->role . "
			AND `functions-permissions`.FunctionID = `functions`.ID
			AND `roles`.ID = " . $this->core->role . "
			ORDER BY `functions`.`FunctionMenuVisible` ASC";
		} else {
			$sql = "SELECT *, `PermissionDescription` as RoleName  
			FROM `permissions`, `functions`
			WHERE `functions`.`FunctionRequiredPermissions` = `permissions`.`ID`
			AND `permissions`.`RequiredRoleMin` LIKE '%'
			AND `permissions`.`RequiredRoleMax` NOT IN (2,3,4,5,7,8,9,10)
			AND `functions`.`FunctionMenuVisible` > 0
			ORDER BY `permissions`.`RequiredRoleMin`, `functions`.`FunctionRequiredPermissions`,  `functions`.`FunctionMenuVisible`";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0) {
			return $menu;
		}


		$currentSegment = NULL;
		$i = 0;

		while ($fetch = $run->fetch_assoc()) {

			$segmentName = $fetch['RoleName'];
			$pageRoute = $fetch['Class'] . '/' . $fetch['Function'];
			$pageName = $fetch['FunctionTitle'];

			if (!isset($currentSegment)) {

				$menu .= $this->segmentHeader($segmentName, $i);
			} else if ($segmentName != $currentSegment) {

				$i++;
				//$menu .= '</ul></div>';
				$menu .= '</ul>';
				$menu .= $this->segmentHeader($segmentName, $i);
			}


			if ($pageName == "Message Inbox") {
				$uid = $this->core->userID;
				$sql = "SELECT `helpdesk`.ID as MID FROM `helpdesk`
				WHERE `RecipientID` LIKE '$uid' AND `Read` = 0
				OR `RecipientID` LIKE 'ALL'
				ORDER BY `MID` DESC";

				$runx = $this->core->database->doSelectQuery($sql);
				$countm = $runx->num_rows;

				//$menu .= '<li class="menu" ' . $style . '><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '<div class="mailcount"><b>' . $countm . '</b></div> </a></li>';
				
				$menu .= '<li ' . $style . '><img src="' . $this->core->fullTemplatePath . '/designerpack/mail.svg" alt="icon" width="10" height="10"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '"></i>' . $pageName . '<div class="mailcount"><b>' . $countm . '</b></div> </a></li>';
			
			}elseif ($pageName == 'Logout' || $pageName == 'Change Password'){
				$menu .= '<li></li>';
			}else {
				$menu .= $this->pageItem($pageRoute, $pageName);
			}

			$currentSegment = $segmentName;
		}

		//$menu .= '</div>';
		$menu .= '</ul>';
		$menu .= '</li>';
		//$menu .= '</div>';

		return $menu;
	}

	public function segmentHeader($segmentName, $count)
	{
		if (strlen($segmentName) > 25) {
			$segmentName = substr($segmentName, 0, 25) . "...";
		}
		$id =  rand(1000, 9999);

		if ($count == 0 || $count == 1) {
			//$expand = 'open';
			$expand = '';
		}else{
			$expand = 'class="collapse"';
		}

		if ($this->core->role != 1000) {
			
			$menu = '<li>
			<a href="javascript:;" data-toggle="collapse" data-target="#demo' . $id . '"><img src="' . $this->core->fullTemplatePath . '/designerpack/chevron-down-circle.svg" alt="icon" width="20" height="20"> <b>' . $segmentName . '</b></a>
			<ul id="demo' . $id . '">';
		}else {
			$menu = '<li>
			<a href="javascript:;" data-toggle="collapse" data-target="#demo' . $id . '"><img src="' . $this->core->fullTemplatePath . '/designerpack/chevron-down-circle.svg" alt="icon" width="20" height="20"> <b>' . $segmentName . '</b></a>
			<ul id="demo' . $id . '" ' . $expand . '>';
			
		}

		//$menu = '<div class="dropdown  ' . $expand . '" >
		//			<button class="btn btn-primary dropdown-toggle glow-on-hover" style="border-radius: 5px; margin-left: 10px; width: 100%; text-align: left;" type="button" id="dropdownMenu' . $id . '" data-toggle="dropdown" aria-haspopup="true" ><strong>' . $segmentName . '</strong> <span class="caret"></span></button>
		//			<ul class="dropdown-menu" aria-labelledby="dropdownMenu' . $id . '" style="margin-left: 10px; width: 100%; text-align: left; position: relative;">';
		
		//$menu = '<li>
		//<a href="javascript:;" data-toggle="collapse" aria-expanded="' . $expand . '" data-target="#demo' . $id . '"><img src="' . $this->core->fullTemplatePath . '/designerpack/chevron-down-circle.svg" alt="icon" width="20" height="20"> <b>' . $segmentName . '</b></a>
		//<ul id="demo' . $id . '" class="collapse">';
				
		//$menu = '<div class="dropdown  '.$expand.'" ><button class="btn btn-primary dropdown-toggle" style="border-radius: 5px; margin-left: 10px; width: 100%; text-align: left;" type="button" id="dropdownMenu'.$id.'" data-toggle="dropdown" aria-haspopup="true" ><strong>' . $segmentName . '</strong> <span class="caret"></span>			</button><ul class="dropdown-menu" aria-labelledby="dropdownMenu'.$id.'" style="margin-left: 10px; width: 100%; text-align: left; position: relative;">';

		return $menu;
	}

	public function pageItem($pageRoute, $pageName)
	{
		$menu = '';
		if ($pageName == 'Logout') {
			if ($this->core->role == 1000) {
				//$menu .= '<li class="menu" ' . $style . ' id="chatopen"><a href="#">Direct Chat</a></li>';
				$menu .= '<li ><img src="' . $this->core->fullTemplatePath . '/designerpack/paper-plane.svg" alt="icon" width="10" height="10"><a href="#">Direct Chat</a></li>';
			}
			$style = 'class="bold"';
			//$menu .= '<li role="separator" class="divider"></li>';
			$menu .= '<li ></li>';
		}
		
		//$menu .= '<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></li>';
		$menu .= '<li><img src="' . $this->core->fullTemplatePath . '/designerpack/caret-forward-outline.svg" alt="icon" width="10" height="10"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></li>';
		
		return $menu;
	}
}
?>
<!--
<style>
	.glow-on-hover {
		width: 220px;
		height: 50px;
		border: none;
		outline: none;
		color: #fff;
		background: #111;
		cursor: pointer;
		position: relative;
		z-index: 0;
		border-radius: 10px;
	}

	.glow-on-hover:before {
		content: '';
		background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8, #ff0000);
		position: absolute;
		top: -2px;
		left: -2px;
		background-size: 400%;
		z-index: -1;
		filter: blur(5px);
		width: calc(100% + 4px);
		height: calc(100% + 4px);
		animation: glowing 20s linear infinite;
		opacity: 0;
		transition: opacity .3s ease-in-out;
		border-radius: 10px;
	}

	.glow-on-hover:active {
		color: #000
	}

	.glow-on-hover:active:after {
		background: transparent;
	}

	.glow-on-hover:hover:before {
		opacity: 1;
	}

	.glow-on-hover:after {
		z-index: -1;
		content: '';
		position: absolute;
		width: 100%;
		height: 100%;
		background: #111;
		left: 0;
		top: 0;
		border-radius: 10px;
	}

	@keyframes glowing {
		0% {
			background-position: 0 0;
		}

		50% {
			background-position: 400% 0;
		}

		100% {
			background-position: 0 0;
		}
	}
</style>
-->
