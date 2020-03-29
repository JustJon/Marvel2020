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
	<title>Marvel API Display</title>
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
				dataType: 'json',
        			success: function (data, status, xhr) {// success callback function
					console.log(data);
					var markers = data;
					var htmltable = '<table class="htmltable" style="width:700px">';
					for(i=0; i<data.data.length; i++) {
						if (parentId == 'characters') {
							htmltable+='<tr><td style="width:100px; border: 1px solid black;">'+data.data[i].id+'</td><td style="width:200px; border: 1px solid black;">'+data.data[i].name+'</td><td style="width:300px; border: 1px solid black;">'+data.data[i].thumbnail+'</td></tr>';
						} else if (parentId == 'series') {
							htmltable+='<tr><td style="width:100px; border: 1px solid black;">'+data.data[i].id+'</td><td style="width:200px; border: 1px solid black;">'+data.data[i].title+'</td><td style="width:300px; border: 1px solid black;">'+data.data[i].thumbnail+'</td></tr>';
						} else {
							htmltable += '<tr><td colspan=3>Data Error</td></tr>';
						}
					}
					htmltable+'</html>';
            				$('#player').html(htmltable);
					setTimeout(function() {
                                        	$('#loadingIcon').hide();
                                        	$('#player').show();
                                	}, 1000);
    				},
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
