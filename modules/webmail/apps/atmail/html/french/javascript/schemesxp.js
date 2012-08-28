// to create a new color scheme copy and paste one of the old ones, 
// change the 'type name' and edit the rgb-codes of the colors. 
// To add the scheme you created to the dropdownbox edit html/english/schemes.html 
// and add an option with the value which is equal to your 'type name' (case sensitive!!)

// Change the default color

var type = document.settings.PrimaryColor.value;
var image;
var loginType = document.settings.LoginType.options[document.settings.LoginType.selectedIndex].value;
swapImageStyle(loginType);

//change scheme
function swapImage(colorScheme) {
	loginType = document.settings.LoginType.options[document.settings.LoginType.selectedIndex].value;
	document['startpage'].src = 'imgs/screenshots/' + loginType + '_' + colorScheme + '_startpage' + '.gif';
}


// change style
function swapImageStyle(loginType) {
colorScheme = 'gray';

document['startpage'].src = 'imgs/screenshots/' + loginType + '_' + colorScheme + '_startpage' + '.gif';
} 