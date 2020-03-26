<?php 
/**********************************************************************************
index.php
Index page for displaying Marvel API Data
Copyright Jonathan Lazar 2020
**********************************************************************************/
?>
<?php require_once 'includes/header.php'; ?>
<html>
<head>
	<title>Marvel Music Player</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="http://malsup.github.com/jquery.form.js"></script> 

	<script> 
        // wait for the DOM to be loaded 
	$(document).ready(function() { 

		$(".alpha").click(function(e) { 
			var parentId = $(this).parent().attr('id');
			var alphaId = e.target.id;
			url = '<?php echo BASEURL ?>getMarvel.php?type='+parentId+'&alpha='+alphaId;
			$.ajax(url,   // request url
    			{
				beforeSend: function() {
                                	$('#loadingIcon').show();
					$('#player').hide();
                        	},
        			success: function (data, status, xhr) {// success callback function
					console.log(data);
					var markers = JSON.stringify(data);
            				$('#player').text(markers);
					alert(markers);
					setTimeout(function() {
                                        	$('#loadingIcon').hide();
                                        	$('#player').show();
                                	}, 1000);
    				},
				dataType: 'json',
			});
		});
	});

    	</script> 
</head>
<body>
	<div id="charactertitles">Characters</div>
	<div id="characters">
		<?php foreach ($alphabet as $a) { echo '<div class="alpha" id="'.$a.'">'.$a.'</div>'; } ?>
	</div>

        <div id="titleseries">Series</div>
        <div id="series">
                <?php foreach ($alphabet as $a) { echo '<div class="alpha" id="'.$a.'">'.$a.'</div>'; } ?>
        </div>

	<div id="loadingIcon"><img src="images/logo-x-men2-34221.gif"></div>
	<div id="player"></div>
</body>
</html>
