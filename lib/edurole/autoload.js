<?php
$getd =  explode('&',$_SERVER['QUERY_STRING']);

$searchurl = explode('=', $getd[2]);
$urla = $searchurl[0];
$val = $searchurl[1];
$urld = ", \"$urla\": \"$val\"";

if(isset($getd[3])){
	$searchurl = explode('=', $getd[3]);
	$urla = $searchurl[0];
	$val = $searchurl[1];
	$urld = $urld. ", \"$urla\": \"$val\"";
}

if(isset($getd[4])){
	$searchurl = explode('=', $getd[4]);
	$urla = $searchurl[0];
	$val = $searchurl[1];
	$urld = $urld. ", \"$urla\": \"$val\"";
}


echo'<script>
	jQuery(document).ready(function() {
		var offset = 50;
		var pageload = '. $this->core->limit .';
		var loading  = false;
		var geturl = "../api/expandList/'. $this->core->page .'/'. $this->core->action .'/";

		jQuery(window).scroll(function() {

			if($(window).scrollTop() + $(window).height() == $(document).height()) {

				if(loading==false) {
					loading = true;

					$.get(geturl, {"offset": offset '.$urld.'}, function(data){
						$("#results").append(data);
						offset = offset + pageload;
						loading = false;
					}).fail(function(xhr, ajaxOptions, thrownError) {
						loading = false;
					});

				}
			}
		});

	});
</script>';
?>
