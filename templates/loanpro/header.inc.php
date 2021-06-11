<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->core->getTitle(); ?></title>

<link rel="icon" type="image/png" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/apple-touch-icon-144x144-precomposed.png">
<link rel="apple-touch-startup-image" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/splash-screen-320x460.png" media="screen and (max-device-width: 320px)" />
<link rel="apple-touch-startup-image" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/splash-screen-640x920.png" />
<link rel="apple-touch-icon" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/apple-touch-icon-57x57-precomposed.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/apple-touch-icon-72x72-precomposed.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/apple-touch-icon-114x114-precomposed.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/apple-touch-icon-144x144-precomposed.png" />

<?php 
echo $this->cssFiles;
echo $this->jsFiles; 

if(isset($this->jsConflict)){
	echo'<script type="text/javascript">
		jQuery.noConflict();
	</script>';
}
?>

</head>
<body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]--> 

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo $this->core->conf['conf']['path']; ?>">
					<img src="<?php echo $this->core->fullTemplatePath; ?>/images/header.png" class="logo" /></a><font color="#fff" align="center">  Welcome to the National Institute of Public Administration Information System</font>
				</div>
			</div>
		</div>
		<br>
		<br>
		<br>
		<br>
 <!--                   
					<div class="nav-collapse collapse">
                        <ul class="nav pull-left">
<body>
<div class="bodywrapper">
<div class="bodycontainer">

 <div class="headercenter">
<div style="float: left; margin-top: 3px;">
<a href="<?php //echo $this->core->conf['conf']['path']; ?>">
<img src="<?php //echo $this->core->fullTemplatePath; ?>/images/header.png" class="logo" /></a>
</a></div>

<div style="float: left; font-size: 22pt; color: #333; margin-top: 30px; margin-left: 0px; ">AviPlat<div style="font-size: 13pt">INFORMATION SYSTEM</div></div>
</div>-->
