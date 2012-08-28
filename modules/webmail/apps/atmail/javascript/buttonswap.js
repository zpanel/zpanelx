<script language="JavaScript" type="text/javascript">
	<!--
	function ButtonSwap(ButtonName,Position) {
		if (Position == "Flat") {
			eval ("document.getElementById('" + ButtonName + "Left').src = 'imgs/xp/shim.gif'");
			eval ("document.getElementById('" + ButtonName + "Middle').src = 'imgs/xp/toolbar_button_" + ButtonName + "_flat.gif'");
			eval ("document.getElementById('" + ButtonName + "Right').src = 'imgs/xp/shim.gif'");
		}
		if (Position == "Down") {
			eval ("document.getElementById('" + ButtonName + "Left').src = 'imgs/xp/toolbar_button_down_left.gif'");
			eval ("document.getElementById('" + ButtonName + "Middle').src = 'imgs/xp/toolbar_button_" + ButtonName + "_down.gif'");
			eval ("document.getElementById('" + ButtonName + "Right').src = 'imgs/xp/toolbar_button_down_right.gif'");
		}
	}

	function ButtonSwapLang(ButtonName,Position) {
		if (Position == "Flat") {
			eval ("document.getElementById('" + ButtonName + "Left').src = 'imgs/xp/shim.gif'");
			eval ("document.getElementById('" + ButtonName + "Middle').src = 'imgs/menubar-$this->Language/xp/toolbar_button_" + ButtonName + "_flat.gif'");
			eval ("document.getElementById('" + ButtonName + "Right').src = 'imgs/xp/shim.gif'");
		}
		if (Position == "Down") {
			eval ("document.getElementById('" + ButtonName + "Left').src = 'imgs/xp/toolbar_button_down_left.gif'");
			eval ("document.getElementById('" + ButtonName + "Middle').src = 'imgs/menubar-$this->Language/xp/toolbar_button_" + ButtonName + "_down.gif'");
			eval ("document.getElementById('" + ButtonName + "Right').src = 'imgs/xp/toolbar_button_down_right.gif'");
		}
	}
	-->
</script>