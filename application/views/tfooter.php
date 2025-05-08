<?php
		if(isset($js_footer_script)){
			foreach($js_footer_script as $js){
				echo $js;
			}
		}
		
		if(in_array($judul,array("Login","Lupa Password"))){
			$class="class='footer_login'";
		}
		else{
			$class="";
		}
?>
			<?php
			echo "<div id='footer' $class>";
				echo "&bullet; ".$meta_footer["author"]." ".$meta_footer["year"]." &bullet;";
			echo "</div>";
			?>
		</div>
		<script>
			function showAlert(message) {
				// Create alert element
				const alert = document.createElement("div");
				alert.innerText = message;
				alert.style.position = "fixed";
				alert.style.top = "20px";
				alert.style.right = "20px";
				alert.style.backgroundColor = "white";
				alert.style.color = "black";
				alert.style.padding = "10px 20px";
				alert.style.borderRadius = "5px";
				alert.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
				alert.style.fontSize = "16px";
				alert.style.zIndex = "1000";
				alert.style.opacity = "0";
				alert.style.transition = "opacity 0.3s ease";

				// Append alert to body
				document.body.appendChild(alert);

				// Show alert with fade-in effect
				setTimeout(() => {
					alert.style.opacity = "1";
				}, 10);

				// Remove alert after 3 seconds with fade-out effect
				setTimeout(() => {
					alert.style.opacity = "0";
					setTimeout(() => {
						alert.remove();
					}, 300);
				}, 3000);
			}

			$.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
				console.log(message);
				showAlert("Data Tidak di Temukan.")
			};

			const popupCenter = ({url, title, w, h}) => {
				// Fixes dual-screen position                             Most browsers      Firefox
				const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
				const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

				const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
				const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

				const systemZoom = width / window.screen.availWidth;
				const left = (width - w) / 2 / systemZoom + dualScreenLeft
				const top = (height - h) / 2 / systemZoom + dualScreenTop
				const newWindow = window.open(url, title, 
				`
				scrollbars=yes,
				width=${w / systemZoom}, 
				height=${h / systemZoom}, 
				top=${top}, 
				left=${left}
				`
				)

				if (window.focus) newWindow.focus();
			}

			document.addEventListener('keydown', function(event) {
				// Check if Ctrl, Shift, and D keys are pressed
				if (event.ctrlKey && event.shiftKey && event.key === 'D') {
					event.preventDefault(); // Prevent default action (if any)
					
					// Get the current URL
					const currentUrl = window.location.href;

					// Check if the query string already exists
					const newUrl = currentUrl.includes('?') 
						? `${currentUrl}&debug_request_time=true`
						: `${currentUrl}?debug_request_time=true`;

					// Redirect to the new URL
					// window.location.href = newUrl;
					popupCenter({url: newUrl, title: currentUrl, w: 515, h: 160});  
				}
			});
		</script>
		<!-- /#wrapper -->
	</body>

</html>