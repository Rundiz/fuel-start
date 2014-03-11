<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower(Config::get('encoding')); ?>">
		<title><?php echo $page_title; ?></title>
		<style type="text/css">
			html, body {
				background: #fff;
				color: #555;
				font-family: Tahoma, Arial, sans-serif;
				font-size: 14px;
				margin: 0;
				padding: 0;
			}
			
			h1 {
				font-size: 20px;
				font-weight: 500;
				margin: 0 0 10px;
				padding: 0;
			}
			
			p {
				margin: 0 0 10px;
				padding: 0;
			}
			
			.page-error-container {
				border: 1px solid #EAD0D0;
				box-shadow: 0 1px 3px #ddd;
				margin: 50px auto 0;
				padding: 0;
				width: 90%;
			}
			.page-error-wrapper {
				margin: 0;
				padding: 20px;
			}
		</style>
	</head>
	<body>
		<article class="page-error-container">
			<div class="page-error-wrapper">
				<header>
					<h1><?php echo $error_head; ?></h1>
				</header>
				<div class="error-content">
					<p><?php echo $error_content; ?></p>
				</div>
			</div>
		</article>
	</body>
</html>