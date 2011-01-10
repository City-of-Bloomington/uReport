<div class="menuBar">
	<ul><li><a href="<?php echo BASE_URL; ?>">Home</a></li>
		<?php
			if (isset($_SESSION['USER'])) {
				echo "<li><a href=\"".BASE_URL."/login/logout.php\">Logout</a></li>";
			}
			else {
				echo "
				<li><a href=\"".BASE_URL."/login?return_url=$_SERVER[REQUEST_URI]\">
						Login
					</a>
				</li>
				";
			}
		?>
	</ul>
</div>

