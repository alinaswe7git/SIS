<?php

class uploadProfileImage
{
	public $core;
	public $view;

	public function configView()
	{
		$this->view->open = true;
		$this->view->header = true;
		$this->view->footer = true;
		$this->view->menu = false;
		$this->view->internalMenu = true;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core)
	{
		$this->core = $core;
		//change the back ground color from defualt to transparent
		echo '<style>
			.bodycon {
				background-color: #1C00ff00;
			}
			.contentwrapper {
				padding: 20px;
			}
		</style>';
	}


	public function uploadUploadProfileImage($item)
	{

		include $this->core->conf['conf']['formPath'] . "UploadProfileImage.form.php";
	}


	public function saveUploadProfileImage($item)
	{
		$profileImageFilename = $_FILES["idImage"]["name"];

		$profileImageFile = $_FILES['idImage']['tmp_name'];

		$profileImageFileDistination = 'datastore/identities/pictures/' . $profileImageFilename;

		//echo "depos " . $profileImageFilename;

		$profileImageExtension = pathinfo($profileImageFileDistination, PATHINFO_EXTENSION);

		if (!in_array($profileImageExtension, ['png', 'jpeg', 'jpg'])) {
			echo "You file extension must be .png, .jpeg or .jpg";
		} elseif ($_FILES['idImage']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
			echo "File too large!";
		} else {
			// move the uploaded (temporary) file to the specified destination
			if (move_uploaded_file($profileImageFile, $profileImageFileDistination)) {
				echo "<script>
                            alert('Profile Image File uploaded successfully');
                        </script>";
				include $this->core->conf['conf']['formPath'] . "UploadProfileImage.form.php";
			} else {
				echo "<script>
				alert('Failed to upload Profile Image! contact IT help desk');
			</script>";
			}
		}
	}
}
