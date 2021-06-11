<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>Nkrumah Take Picture</title>
<script  type="text/javascript" src="../../lib/jquery/jquery.js"></script>
    <style type="text/css">
	body {
		background-color: #ccc;
	}
        .container {
            width: 320px;
            height: 240px;
            position: relative;
            border: 1px solid #d3d3d3;
            float: left;
        }
 
        .container video {
            width: 100%;
            height: 100%;
            position: absolute;
        }
 
        .container .photoArea {
            border: 2px dashed white;
            width: 140px;
            height: 190px;
            position: relative;
            margin: 0 auto;
            top: 40px;
        }
 
        #canvas {
            margin-left: 20px;
        }
 
        .controls {
            clear: both;
        }
    </style>
</head>
<body>
<div style="border: 10px solid #FFF; width: 580px; height: 30px; padding: 20px; ">
<b>ENTER STUDENT NUMBER: </b> <input type="text" name="number" id="number"> 
<button id="startbutton" onlick="takepicture()">Take photo</button>
 </div>


<p>
<div class="s">
<div class="photoArea"></div>
<video id="video"></video>
<canvas id="canvas"></canvas>
</div>
</p>
<script id="jsbin-javascript">
(function() {

  var streaming = false,
      video        = document.querySelector('#video'),
      canvas       = document.querySelector('#canvas'),
      photo        = document.querySelector('#photo'),
      startbutton  = document.querySelector('#startbutton'),
      width = 640,
      height = 480;

  navigator.getMedia = ( navigator.getUserMedia ||
                         navigator.webkitGetUserMedia ||
                         navigator.mozGetUserMedia ||
                         navigator.msGetUserMedia);

  navigator.getMedia(
    {
      video: true,
      audio: false
    },
    function(stream) {
      if (navigator.mozGetUserMedia) {
        video.mozSrcObject = stream;
      } else {
        var vendorURL = window.URL || window.webkitURL;
        video.src = vendorURL.createObjectURL(stream);
      }
      video.play();
    },
    function(err) {
      console.log("An error occured! " + err);
    }
  );

  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width);
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      streaming = true;
    }
  }, false);
 
  function takepicture() {
    canvas.width = 400;
    canvas.height = height; 
    canvas.getContext('2d').drawImage(video, 120, 0, 400, 480, 0, 0, 400, 480);
    var data = canvas.toDataURL('image/png');



	image = data.replace('data:image/png;base64,', '');
	var number = $("#number").val();
	$("#number").val("");

	jQuery.ajax({
	type: 'POST',
	url: 'index.php',
	dataType: 'json',
	data: ({
        number: number,
        data : image
    	}),
	success: function (msg) {
		alert("Uploaded successfully");
	}
	});

  }

  startbutton.addEventListener('click', function(ev){
      takepicture();
    ev.preventDefault();
  }, false);

})();
</script>
</body>
</html> 
<?php
$data = base64_decode($_POST['data']);
	echo $_POST['number'];
file_put_contents('pictures/'.$_POST['number'] . '.png', $data);
?>