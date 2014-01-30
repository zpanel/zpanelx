<?php
$reponame    = "My Repository - my.repo.com";
$packagelist = "packages.txt";
$log         = "C:/ZPanel/logs/domains/zadmin/my-apache-access.log";
$ignoreip    = "200.200.200.200"; //I use this to ignore how many times I have downloaded anything from my own ip

/* *******************************
 * RepoStats for calculating the number of downloads for custom repo modules. Also works with Repo Browser Module for ZPanel 10.X
 * Version : 100
 * Author :  RusTus (RusTus @ ZPanel Forums)
 * Email : rustus@zpanelcp.com
 * *******************************
 * 
 * THREE WAYS TO USE REPOSTATS:
 * http://my.repository.com/repostats.php <-Returns HTML
 * http://my.repository.com/repostats.php?json=true <-Returns JSON array of packages and doenloads
 * http://my.repository.com/repostats.php?cron=true <-Use with CRON or scheduler to create repolist file for Repo Browser module
 * ********************************
 * 
 * INSTALL:
 * Place repostats.php in your root repository web directory and change variables at top match your server setup.
 * RepoStats must know the path to your apache access log so it can parse the log and count
 * the number of times your modules have been downloaded.
 * ********************************
 * 
 * NOTES:
 * RepoStats will work with RepoBrowser to calculate downloads in ZPanel for your modules.  If you want this feature then
 * Run RepoStats as a CRON (whatever intravel you desire).  This will create a repostatslist file that RepoBrowser will
 * look for when getting your download stats for your repository.  If this file does not exist then RepoStats will still
 * work with RepoBrowser by using the JSON setting automatically, however this can be harder on your server depending on
 * the ammount of custom modules you have and the size of your apache access log.
 * To run as a cron use: http://my.repository.com/repostats.php?cron=true
 *
 */

//IF LOG EXISTS THEN OUTPUT, IF NOT THEN RETUN NOTHING.
if (file_exists($log)){

	//Get list of packages available
	$file_handle = fopen($packagelist, "r");
	while (!feof($file_handle)) {
	   $line = fgets($file_handle);
	   $packages[] = trim($line);
	}
	fclose($file_handle);

	//Get accesslog information
	$file_handle = fopen($log, "r");
	while (!feof($file_handle)) {
	   	$line = fgets($file_handle);
	   	if (strstr($line, "ZppyClient") && !strstr($line, $ignoreip)){
	   		$downloads[] = $line;
		}
	}
	fclose($file_handle);

	asort($packages);
	$totalpackages =  count($packages);
	if (!isset($_GET['json']) && !isset($_GET['cron'])){
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $reponame; ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="data:image/icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAD8/PwA5ubmF9DQ0C3Ozs4vzs7OMM7Ozi/g4OAc+vr6A/r6+gPi4uIa5ubmFfz8/AD///8A////AP///wD///8A8/LyCpyViHWKgXKKioFyi4qBcouLg3SKvLu4RPT09Ann5uQZlYx+fsvLyjL6+voD////AP///wD///8A////AN7c2CSCd2SZg3hml4h9bJKKgG6QioBukLq1rk/29vYGzMjCOoJ2ZJnGxcQ4+vr6A////wD///8A////AP///wDu7ewRh3xqk46FeIbDwsA85+bkGero5hXz8/IK/Pz8AMrGwDyCdmSYxMPCOvT09Af6+voD/Pz8AP///wD///8A/Pz8AKCYinSEeWiUt7a0SPDw8Az///8A////AP///wDKxsA8g3hmmLSzskrW1tYm2traIurq6hL6+voB////AP///wDNyMI6gndkmZyWjm7g4OAd/Pz8AP///wD///8AysbAPIJ2ZJmLg3aGkIl8gZmTinG8u7pD6OjoFfz8/AD///8A8O/uD4yBcI2KgXKMyMjINfj4+AX8/PwA////AMrGwDyCdmSahXpoloV6aJaCd2SZjoV4hM7Ozi/6+voD////APz8/ACtpppig3hmmKyqplfq6uoS/Pz8AP///wDKxsA8gnZkmbSxrFDZ1tIqq6SYZYN4Zpi5t7RK9vb2Bv///wD///8A3NrWJoR5ZpiUjYJ82NjYJPr6+gH///8AysbAPIR4ZpjGxcQ4+vr6A+Dd2iKEeWaYramkV/b29gf///8A////APf29gaXjX5+hntqlL++vj709PQJ////AMrGwDyEeGaYxsXEOPr6+gPm5OIag3hml6yoolz09PQH////AP///wD8/PwAw762RoJ3ZJqinphk5ubmF/z8/ADKxsA8gnZkmMbFxDj6+voD5uTiGoN4ZpesqKJa9vb2B////wD8/PwA+vr6Aezr6hOIfmySjoV4hdLS0iv6+voDysbAPIJ2ZJnAv74/9PT0CeLg3h+DeGaYrqqkWfb29gf8/PwA7u7uDuDg4B3c3Nwhn5iMcYN4Zpi7ubhE9PT0CcrGwDyCdmSZqaekWdTU1CnBvrpCgndkmbq3skz4+PgD9vb2B7OuplecloxxmpSKcpGJfIGCd2SarKiiWvLy8grMyMI6gndkmol/cI2Ykoh1i4Fyi4d8apPV1NIq/Pz8AODe2iOCd2SagXVim4F1YpuBdWKbgXVim8C8tkb4+PgFzcjCOoR5ZpiCd2SagXVim4J3ZJmzraRZ9PT0B/z8/ADz8/IK0c3IM9HNyDXRzcg10c3INdTRzDH09PQJ/Pz8AO3s6hPd29gk2tjUKc/LxjXc2dYm9vb2Bvz8/AD///8A//8AAMP/AACDvwAAn78AAN+/AADfjwAAz4MAAO+7AADvuwAA97sAAPe7AADzuwAA+7sAAPOTAACDhwAA//8AAA==">
	<style type="text/css">
	<!--
	html {
		height: 80%;
	}
	body {
		text-align:left;
	    height:100%;
	    background: #F3F3F3;
	    font-size: 62.5%;
	    font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
	    margin-top:10px;
	    margin-bottom:10px;
	    margin-right:10px;
	    margin-left:10px;
	    padding:0px;
	}
	body,td,th {
	    font-family: Verdana, Arial, Helvetica, sans-serif;
	    font-size: 9pt;
	    color: #333333;
	}
	h1,h2,h3,h4,h5,h6 {
	    font-family: Geneva, Arial, Helvetica, sans-serif;
	}
	h1 {
	    font-size: 28px;
	    font-weight:bold;
	    color: #039ACA;
	    text-shadow:3px 3px 5px #BBBBBB;
	}
	h2{
		font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif;
		font-size: 14pt;
		color: #006699;
		font-weight: lighter;
		line-height:50px;
	}
	a:link,a:visited,a:hover,a:active {
	    color: #006699;
	    text-decoration:none;
	}
	ol{
	    color:#039ACA;
	    font-size: 24px;
	    font-weight:bold;
	    text-shadow:3px 3px 5px #BBBBBB;
	}
	ol p{
	    color:#CCCCCC;
	    font: normal 12pt Verdana, Arial, Helvetica, sans-serif;
	    color: #333333;
	}
	.content{
		background:#F1F4F6;
		background: #F1F4F6 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAA6CAYAAAB4Q5OdAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAABKklEQVR42mL8DwQMaAAggJgYsACAAMIqCBBAWAUBAgirIEAAYRUECCAWLJYzAAQQy38sKgECCKt2gADCKggQQFgFAQIIq0UAAYRVJUAAYRUECCCsggABhFUQIIBYsNjDABBAWFUCBBALAwOmUoAAwqoSIICwCgIEEFaLAAIIq0qAAMIqCBBAWAMEIICwqgQIIKyCAAGEVRAggLAKAgQQVkGAAMIqCBBAWJ0EEEBYVQIEEFZBgADCmmwAAgirSoAAwioIEEBYbQcIIKwqAQIIqyBAAGEVBAggrIIAAYQ14gACCKtKgADC6iSAAMKqEiCAsAoCBBBWQYAAwioIEEBYkyJAAGFVCRBAWAUBAgirjwACCGt0AAQQVu0AAYRVECCAsAoCBBDWAAEIMAAoCSZuy+v+UQAAAABJRU5ErkJggg==') repeat-x top;
		border:solid 1px #DFDFDF;
		margin-bottom:20px;
		margin-top:20px;
		padding-top:0px;
		padding-bottom:20px;
		padding-right:0px;
		padding-left:20px;
		-moz-border-radius: 10px;
		border-radius: 10px;
		height:90%;
	}
	.poweredbox {
	    font-family: Geneva, Arial, Helvetica, sans-serif;
	    color:#333333;
		padding-left: 15px;
	}
	.zgrid{
		text-align:left;
		min-width:75%;
		border-spacing:0px;
		padding-right:20px;
	}
	.zgrid td{
		padding:5px;
		border-bottom:1px solid #DFDFDF;
		border-spacing:0px;
	}
	.zgrid th{
		padding:5px;
		border-bottom:1px solid #DADEE2;
		color: #333;
		font-weight:bold;
		text-align: left;
		border-spacing:0px;
	}
	-->
	</style>
	</head>
	<body>
	<a class="header_logo" href="http://www.zpanelcp.com/" target="_blank">
						 <img src="data:image/png;base64,
                         iVBORw0KGgoAAAANSUhEUgAAAMYAAAAyCAYAAAAHmKRSAAAACXBIWXMAAAsSAAALEgHS3X78AAAABGdBTUEAALGOfPtRkwAAACBj
                         SFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAxzklEQVR42mL8//8/A6ng9tvfDOQCJkYGhvp97xl81bkZ
                         nn3+x6AiyOY9+cxnBj0ZxlgJXha/h19/Mbz++Zvh2y+guxghekBO5GRhZBDjZGEQYWdhEGRlfrXx6q9sK2mOL5fffD1YaiHIsOXm
                         F4ZnH38zGEmyMvzD4iVGJiaGL+9fM9T5qjOMglGADtDzAUAAsQyEI0DpnZmBMebpl79xT3+/d1VX+c7wm+s3w2uufwxi0n8ZZDn+
                         MTAy/0coBjL//WNg+PWDieHbNyaG25+Z5LU0WLe8/foT6CHWJf//M74FZriC0egdBdQCAAFEVsZgZmIkO0NwsjKqsTIxOW17+H46
                         r/BXBi6evwzCPEAzgTUCsFAHYhYGVkZmBmZGBniNAaoB/gPZzIx/GTg4/jMIsP1n+PfnF8PX7z+B+pljFlz/ycD9j4ufkZExcTRK
                         RwE1AEAAkZUxnn38RVYTCtQ8uvD8V+Vf1s8JwkI/GHg5mRhYgRmBEdgyYwI5ho2R4e/PvwzXr78A1gR/gc0fRnDe+AusLf78+ceg
                         ribOwMLKAmQDMwZQDxuwKmFi/8/AIvyF4cnLXwmsDDx/mRgZU/4Bq0VGLJmSzLxMdG3MAPEGOC8j6jrkeg/F3P947ILJMUHNGgXk
                         AWZo+JHcXwAIILIyxqsvf0jPgcBo//jjf+j+B+9ClGV+MbADawWGv8CED6wamIHVAQvQ6cxAviArK0OogT4DKwMjAyM0rfyHdjT2
                         /HzH8O7XL6BaRnC/4+9fIAHMJIxAfaK8vxle/vqS/PmHwDV2VoY+jFQH1PCPUH+KCZgTpVQtGP6zKoDrqf9EZgxGRmAb7+Nthg9P
                         rwEd9gsaGX9RErqEuhUw5ysw/Pz6nOH90wsMzKzcDIJSKkBaAqiHEZE5/n9h+PzmIcOPT08Yfv/8CI3cv9Bg+D+a1vEAUUU9Bg5e
                         HYY/v94xvL53FkjDwu8PqeEHEEBkZQwZPtK1MQPbSUcfvV/Az/mTi/EvM8Pf38DEDY5yJkQGASYndkYWBlkBXqwpkvPlJ6Cavwz/
                         QckElCmAVQmI/e8PCANrH+afDDdef41QE+Vc+vPPv5fone9v3/8SciYbAyuHH5CSYoDYQnzNws7jwsDCsoPh9cM9QL0/gGI/oREC
                         AazsNgws7LpA8/8ycPFZAz0sCMxQImgZCGYWMFP+fcTw/tlOho8vLkPV/MKidhSgJDJ2E2D4OgHD+T+DlKYxw7Pri4CZ4wNU9g8p
                         GQMggMjKGLzszOQ0pRpvvvrMLsL5j+Hjg3cMzEAjWFgZGViBVQkzC6jQZWBQ0ZEEJiBGcK3ABupwMCIaFuDsDsxAoOT27NYrhu/A
                         RP4PWKj//f2f4Tew1vj18x8Du4ggw9Nv30yd+HhtFIRY1/7++x+pMmBi+MDHSagxBITMXyBdfQbSht7+/2Fk4BFxZHj7+BrDv78v
                         obXGP3iz6v//f5Da5P8fBkYWJSAN4v/A2SpjZFJgEJKNB3LWAjPHWWiz6ge5TYORAf5Bwvg/sKHNwq7OwC1kDAy7E0jNW6KbpQAB
                         RFbGWHXxLTmNdSlgCmYWFuZk6AzTwapm+b3nDN9+/GXY8vwVuIPPxMwI9S4wAwAT/x9gecnPws7Q7GXKwM7MhKG/cfd9hvuvvzB8
                         /fl79sdvTGv//v+HUmN8/kmowP3/G9iM2c/AL6EADGA2SK3BiLtL8f8/ciIFZipGViAtAMTAzMXwHZq5/mH2Vf7/RmqGMUISPSOs
                         BPgHbjf+//8TKMTKICDly/D980eGX1/vQc0arTnwlL+IIAYWQMysokAWP7S2+EmKQQABRFbG2H/zA8l6QL0BfjZQAseT33+DMgGo
                         v8EEbu4zQpMlaIwZxP73G1ggAMVxjYr9BRbawNTMcP7Jl583X35H7e2CmlIfPhFwJNDit48uMfz4PBtY2hgBdXEDSx8WrJ1wUAeI
                         g0+CgYmFC1z6g0T+/QFmhv8cQDY7WtgyYna4QRkCyP31/RPD7x8fgUb8AZYEzAxsXCLA0o4HyP8LiVwWPgYBCUuGV/feQJt3f0dr
                         DSKT3P9/oIKKC1pQMeMY9MAKAAKIrIzx+cdPcjIGAy8LC8MnYBNoz433DBxsTAwswBoBhJmZGcEZ4TuwofAPmF5Ao1AgbzDBh2sZ
                         wR3tf8Cm1A9gJjn3/DOwxcUErEH+g8X//IXUJm8//wEXGS8+/oB33JHaUgw/P34jxpl/GL6+vw3Er4FsPiDmgIYTsoF/GTj5pBi4
                         hOTgAQ1K1F/ePAPm7D84RrcYUfnAav/t4xsMn18/hNYukD4JCxsHg4iiFdB8WUjmABYXnHxKwHanFLC9/BNaY/wZzRhEjSKCMgMr
                         UqYgGgAEEFkZg/E/eXECKurefvvNMOPIMwZONmYGXk5WBg5gn4KTnYmBHZhR+DiBBSZo+ArojX/MkJriL7AZxcrCBE74f4H4F7BP
                         sePte4YfP/8zfP/1j+EHkP/1OzAt//wDnuwAlcOP3n5n+AnMXYxoYcbEwssgaBHO8P7ESvwZA5JIP0JpNmgVzYgcAgyC0jZAy9jB
                         CRdk67+/vxm+vHuOlHD/4ky8oHHoL29fADPFfWhpBrLrG7jp9QfoqQ/P/jBwcAcDFbKDm2vMLDzgzjoDw3NoRP8cHcYltjjGmmEI
                         AoAA7JixCsIwEIavqTS1VYuDdHF28xFcHX1gX8LJh1DQiGlJY6j49wxahKJ0NpDxwkHuu//u7wVGZV2vFB0Kv4Y6JIAiwo6glKZ5
                         nlGI8UlADW7m+e11s5jjlkXFTTOdjBiKRhkcSi5wAjF3HrmuF40mOwBQggx2CAtQZmnIDupnAqGUlK02tNtvyWrVlWbdcpSqFhTB
                         y93I8iXF4wXD8LZrjxjBDj7WfO3qtjh5GM7+lj5G4J2CnF1TNIzZl25sY5lMyRnpwRD/ov9ZQYLucbb7PAQQWRlDVZSdnIzB8eHH
                         P2DChfQRQE0nKxkOhqP3XjEIiQgxCPGyAxP/PyCGNK1AQImPlUGUh4fh0MNvwKYXC8Pvf5BmE2i06f2XXwxvX79nMJbjZbj9lZnh
                         E7AZ9QuYWT7/+M1gKc/DKczDCq5t0F3BpevL8GbfTIY7Zw/hy8OwdvwftBLnP7D9D6zaxCyhfY9fcPGvbx9DM8Q3aIb6jadUB9Uw
                         sAwEqjE+QzPGX6hdrGgR+B9Y3XFAay9mpMz6n3C6ICodMCO5FXVSEtwV+k9KQvxPeVr+TwNzSQMAAURWxggzEiNnuPZM/74nUcxM
                         TGzMv/+BawA3UyUGJ+3vDNN332F4+IWdQVKUj+E3sKnOCswYoOUeoXoSDDzA2uXgvc8Mn38ygken/gDx4xcfGQSYfjFUeCsxcAEz
                         TsO2Bww/gW0tUGvq07dfDK6aQod1pHgZfv39h6UVw8Rgt3Ahg4WOIjGVHGZkcAtqMLByaAET9h94bfHr2wdgn+Q5NKF/JWrkiJER
                         NiQMm/P4hWQfts41I1qmwBGjwLzDyikJLHmkGARlpIAZmBGRsP8zYgzi/P31ieHl7VPQQuAPkt3/gZ1+DQZ+SXWGrx9eM3x8fg3Y
                         pOMENunEgTWmMHiCEjJgwQiUewEMj3fAcHjM8PfPX5LnDdi5eYAZX5qBT1QCGLZ8YDNBTchPL98x/P39iuH3j2fAJuYvcoZdyQUA
                         AURWxnj87hsZGYNxqgw/S+OjT3+EmZhYgQn8L8P5h58ZIkzEGLpjjRjWnHrKcOr5b4bfLP/BoSnLzcggJ8QJrD2YGaR5GBmuvIOk
                         wx/ATOWsxs8QZCLJIMDDzrD96jtgZ/snuKP+8ccfBg1xdmAzjaHswduv2FfZAtPx528UjHYKSDoBEwErNCEzgodbP758CIzIL9BM
                         8R0tkRNTM/3D1Tgm0IlHBRy8Qgyiip7AxGUA5IkDM8V/AuYyMfxjB2ZMxtvgGXfEEPN/cEAJSocCWZoMvCI/GTh5bwIzBhcDE6ss
                         w/+/LChuFlcFafnA8PPbTYbX93cCM8gzaCb+jXcEjQmoRFDGiIFH2BOY0eSB7uWEjvBBgJgyEzBc3wCbkPcZ3j7axfD900Okmpym
                         I3MAAURWxvjwjZy1UowM8gKsiQ/efdn07RcTuDl17QUkg7GzMjG8+Paf4SOwj8AJaioBm0RScgIM778D+xHM/xnkgf2QE8/egJtf
                         P/78Y3j0GdhhZ4VMMl5/+Y3hG7C/Cpow/PXrN4OJrOACYE1x++P33zgKakZwc05CVoHhxeMHpHlCSNoQmDgMgFXXL4RhwNri24en
                         SM2i7wzIM95E9g4pGHmBAEEZBWBJng6sEqWhfZ/vxJn1H+RWEai5f5Dczgz0J7CzxvQN3NFjYVMHT4D++/MbqQkJ9QW4oOFmYOMy
                         Z5BUVwFmjmXAMLkNlcWeOUCrRsXV/Rg4efyBzQcmqLnfsJjLC6wBjRkkVFUZ3jxaxfD59SWo/b9omTkAAoisjMHNSk7f7z+DIBf7
                         KXn+b6duvv9lxsPBynDv7XeGNedfM4jxsgF9/4vh3acfDILAvgYoE2289A6YCUB6WBjmHX/JIMTNAp7k+wDskBsJcjBceP4NmCm+
                         M9wEZgzQGijQcg9ZXoavBnL8s7//+vOHEVfBCiyQRMQlGGJzqxm6y1JJCCl2NgZuIUdgCcaCaCYB7QANt/778xmaKb4RLCWp15FE
                         booIAJshCcCMKgH033f4PAlk8hBdPWh8H7k/wQgdlv4G7eswItpZKHM8vxAzOExMKOaCJzr//wVnICZmAQZhuSBg82cpED+H1qK/
                         UcIDNDAiqmjBwMEdCIzU3wywVQbgERNGhH/BmySg5jIycgFrMB+GH5/fQ81lgDZDaQIAAoisjGGoKEz6iBm4hvz3Ul2Me1XHzvtm
                         X36zMNx//Z2ha88jBkVhToaJQYoMFx5fZ3jy7Q8DBzDjgTrO/6Gt/N/AvsIXYG3yHdiMEmL8zRBopsJQs/0xw/UXX4HNJkaGX6Al
                         IT++MWR6qZxTEuE6BuqHMDGzQOPsP3qVwcDBzs7w5cVd0jzNJaAJLBG1gRH5C27Qn5+gBX9PkGqLH3SfYwA1R0QULIAlsDKwhP8G
                         T2CgCcOfwED5+/sr2rovJgZOfgn0RhiW8X5GrNUtKBP8/PoF2Df5Ch62ZmblAjbdeMH7BSDLXn4D+VLA5pcpw7sn+5Caiog+Gzu3
                         IAOnYACwZkMsmWEEmvX751dgXwJYQ/39Be7DsHJwAv3HAV5qDZrsZGETZ+AVtQSauwvqpz8MpC7dIRIABBBZGYPl8zPiizlgWDKD
                         Ju3YgWHHzsPAyv6/V1mIWeHQg885DLw84I72LWCpP+v4K4ZMJzmGvFW3Gf6Ah2eBiR3Yj/sFrGW//vgLDtHPHz4zVAcoMWy68ZHh
                         ytMv4NGrLz//MXz++p3BVo79rJIojy8rCwtojTrDp1ePITU2MzPGFMKr9+8Zpvd1kOZpQSk38NQ6fEIPmCI/vnkGTICfoSXtNwbq
                         Ldf4j2NV+n+MJhgjE7BTxaEPzBSIUvc3MNG+e3yD4Rt4QOAHkrtAA78cDPLGIVhGpdA79aj9GUbwSNofYKIETUo+QulLcfKLMAjJ
                         mAELDiFw6Q6a5OQSVGP48PwytFn3E2VeR0BSBciUgjedgNmN4QOwpPr4/C5Q/Td4M4mDB1j7yFswsHEKgzMHyFweIXVg5F4FFkq/
                         kPxF9c44QADWrZ6lYSAMP82niZFLOuhQ0MGigghuzha6uvkLRPD3uLn4B9z8DbrqXAQFTUgV05JL2l5LLL4J1xh10dI1HOHueJ+v
                         e+/mAkbW8/9BaArlNQPjKARb34GuWzg93Dq3bx7PrjtDzXPsIkdc3b3hYGMTx3suLu5jLDtLEPmrPdpKTgrAE4GT/TplRQuXtw/4
                         ICWYTPLj2QwNczxoeCutrKbxNHzGe+hDIwKakmMwTeMXUMVoBMYY4jj+Kyh2iZG3v4pPyU+iOHjRoBtKuyAWxl6255KvNkq5K7JM
                         ksrC+lkEFn1drQCSNqv7RKCYddSTSu6Z0tydOaWphkH0KpuS+Xq5/C9JeRwgtRTU7XZpD1Sd0byZ7NHo33KJqjfL+2LFXZ1+gL7f
                         kao7s6QZRBogekmw1jyicVahEqrhUlHlmahbIaOFA+NTAJE3UQSsNUnFoJr9/f1LDJ+f3WFg+/3pdrCZXICm0D+GTz9+gZtIP4CJ
                         uO3AEwZfC3kGHXEWhlfANPEDGBc/gfg5MGNI8fxj8DCWZmje/4Thw6+/4P7Hp5//GURYvjO0herNDzYU/fT64U2GF4/uMvz5/Qvc
                         lMKGGYEFvbiEJEN2bi5xfmVmZQf2LTyAyY0ZpTT99OYBsJnyARo53zBKReIbmv8xhoY5+ZSBNRIvXAxUWv79+wvrEDIjIwfQU0Lw
                         JgmoVP/59R00o4ImEV8B8Uso/RqaUMmpxIAR8uklNDO8g5oFM/stw+/v14GR/BM+58DEBCyRGEHLmdkZ0JdkMDJKocyXgAYwIGH4
                         AWrmCyh+C7QTWDv8+ASfsQXRID9jX6pDNQAQQPSbQWVkBCfMH5/eMbx6cJPh94tbWy2lWDz4mb59+M7MxvCHnZXh+qdfDN2nXzMU
                         OMozfPn+jeHN77/ATABsFgGbypXOCgzzr39iOP/+B8N/bnaGHxzsDBz/v/0ONxBq//32Ye6re9cZvr5+Cm7hIPqcuMG3r1+Jc7ew
                         rDa4b/EfWlvA+hZfXj+BJj7kEvk/SQHCyMQKdDA7uB0NxiwcwNpJg4FHJARYO0Em+UA5+ff3D8AO/jdoxkNfRIht8us3Umf6E5SG
                         lcZfyUpM4Lrq7w+kWugTitk/vjwDZt4/WFokLEhNNeyjcX9//wT6E9hxZwKVaD+B+DcQ/4Hin9BVzOhNP5jZNEnDAAFEl8MQ/v79
                         G/379x92JuiqwP/AZuef3+8ZrISYfvAq/j096TaD6395eQZ2xn8MS+5/ZXBUE2Josgb2D5l+M4izMzNUGgsxfOHiZph79wkDqwCw
                         /8HIwvDr2XOGCNlvH/TZv1/99u5vAiOoJGFiBjefQC0Q6MDLMmwjF79+/WJQV9dgEBISYnj3Dk8BygZsHnAJugETBKKkZgLa8/7V
                         Q6DYR6SERtraJZDj+MVVGfhEJRkYmYGJgekPMJ8AExWwmcPEpADd0fcH3owCTbD9+/OFAbEO6x+B4VvYxibkyUMGpAkycku3f1jM
                         /Q+tqb6hzEEg3Iat74JcEf1j4BNXBHaqRYF+/w0OD9DkJyy7g1qToJEuUN8Fc7KTkVZpFiCAaJ0xmIGeK1FWUuwAjQT9Aw1a/INM
                         4IHZQIauDjDbCzxn6Lr+ioFRWQlY7/5haLn+mWG9gzyDAjfEec46EgyOu18y/AH2R5iZWRl+PHzGEC71kyHCUkEU2KJa8g9qJnhQ
                         BFoesYD6c6/fRAF53v9BexuQwPfvPxhsbGwYlJSU8GcMfnE9YGSpwlfMgtrDv39+Bra1n2CZt/hHUvnLyiEEnT+AdIlhLv//H1Hq
                         gkZ6fn3/zPDp5V1oQvxBpF3/GWizPP0fjklJXNtGGQknXmAqYOMALZAUgnbDGeFGMcIrxD9oNSTNWzoAAURWxmAi8pQQYC1hoKSk
                         0CEtJcXw9esXhl+/f4NLdNCaKFCw/gWyvwLZPjqCDJ8/P2KY+vQhA6OmGsNdoOS5T3/hGeMdsAd+7R8LA78oN8PvWw8Y3P89YHCV
                         52O4D6w1QIckAGsksFn/gIXKHyANmgeRlZNl4BEVdf7x7m07OwdHEfqwLUgtSB9OwM7DDJ23YGZAXjMFGp79A+5bwGoL8kaiIM0D
                         3GupQG3C3z8/Mby+fw7Y1HiH1ClFn1VnZKAvICWjMaJh7JkFs6mEyzpGyLzG/98MNF4WAhBAZOU8FhZmghg0TMrJydEgJCjE8PXb
                         V4afP38BE/EfYGb5zfD7Dwj/AWPQbPWPP38Zgk2kGFJ43zEw3bzO8JuHk2ENsOX6EbRrD5igp7/+y8Aswsvw++ZtBudPtxjCDPgY
                         foPmN/6AMsQfcKYA7daDZA5gH/HnT4Z79x8w/AENEzMw+HFysstxgPokKJgDaZ4Ka6dbCdjmV4VGAqRP8Pf3d6ShStjyDzIjCTwB
                         x4SBQbNfoAWGn9/cY3hx8zjDzy9Pkdrz5NROgx8w4ggLTMzA8P7ZNYbfP94woK7tojoACCCyagwBfgFi+hXa375/9+bl4WZ4//Ej
                         pN3/HzJpB1rB848BygelrH+Qsw1CreUYOM88YZh59gzDJhtThuP3/jFwAFU8+s7IwH3hEkPQnycM3qbCDL9A6v/8hdbfwNqX8T90
                         5J8RPOcE6q/+BPYjPn/9Bip4lYEhGiAkJDjpL9KEr5iYOHiiD3enW84FmEC5GGBbIsHzFq8fQkeivkJL8J9kZ4ovb58CO9Wg0RZo
                         04QR0jn6/R3Ykf38CWj3V6Sh4PfQjPGD4oTw/9//wZYrgGHxDNhk/AgNi7/gSMRWc3z/9I7h59dX0LD4ykDcmjSyAEAAkZUxXrx8
                         Sbgx+u9fpbaWJiOo9AZ3iEH7toH0LyD9EzQ/AdqN94+J4Suwn/mNEZiQQTUAUE7ZVJkh6vpzhrUHDzM8szBmYODlYhDaf4LBnfcn
                         g66BPMO9P//BE3hMwDBk+vuHgRmYQVjAA0Z/wWtHwZmNEZKOX795zSAtLs7w6s3bjk+fv0z6D5t4ZWNnOHDwEMPt27exO55XRIGB
                         hdWAAdY3AU+agUai4LPcn0lo72MvIb8DO9Rf3z9C6rz/QRu6hfUpYBkEscCPEsAtxA2dwf4/SPIFE8P3j68Yvrx7xIBYsv8Hz9D2
                         T6SCiVAzlmw/AgQQWRmDk52N4cu3Hwy4RkWBzSiB7z9+8AsKCjL8+PETWkv8Z4AeAwXOAKBaAuJDUOYAhggjZNvqb6A3pTRlGGKE
                         PzAcOX2c4TuwD2GuKcYgJiXO8Po36BC2fwwswAYH6CwqNqB6VqZ/kFYJFEJnxMCZ4/OXrwx/JZkZ3n/6/EtKhE0J2Le5B3IzCwsr
                         w+lTpxhev36NbUYSmDFEHSG782CddqCuT68eAjPHW7SRKPJnuUHDk4ja4AtS6QebxIMt1vvNQM0Fc5x8QgOwvQF/YmUCn8f6HTqP
                         8QlHPwo5XH5CMXpBgbZQkY0dSx+HKAAQQGRljN0nzjMEu9oxfP3xA/PEP2AB8PHjR2MZKWkfUD8D1MH9B23/g/zFzABpHrEAWw5s
                         QDEuYCb5DpT6AVqCA8o0oMwB7GyL8LAyqNvLg5fT/ASq+/PtG7Cx8ReMQbUEE4j99y90FA/Yt2CEYUYwBrnj7/8/DG/evWPg5ufn
                         B7qjSlJSMgXkHjbQoW6hoQw7d+5kePDgAXqnW4SBg1cDaPFfeOkOmrf49PIBUm1B6YwrI7TZAJsH+Qhl/0fLHLiWpZM3cQi2mVmL
                         AXVj0gBkjf/fEQkVtIWNT4ThE3jV7BcG5Bl13CNjf7CGy///n1CGxNm5xZDmOkjKHAABRFbne8v+4+Bl46zATjYTdDceMgampZmC
                         AgLgjvY/8JwCpD8BHlb9Bzof5h94IIIJNGr5/y8DD9CfgsDmkNi/XwwSf34ySAKxxO9vDMLfgfjnNwbxH9+A/O8Mon9+MAj/+cXA
                         C1TLAVpTxvAPOm73H9HfQEoZoNHOt2/fMTACO9rv3r8P4+TkMGJjYwWvl1JRUQHPY2A2o0R1gY6TRFTnoJwOzBT//30FMn8AO+Sg
                         BW7AnM3GDMSsqBi87JiJyDH2/0ilH6yp9B2aQX7gmLMgoTPLxAR0KyvQrSC3sYE3AIkoWDNw8VuhzQnQH/z5/RAaRpARKW4BGQYB
                         KS2gOzmBbgYtTAQ2C9j+omHYpCWu1cvAJseXGygZiI1LkEFIzgBYc/ADzeWEryyFxBHeDV8AAURWjXH12m2G1LpuhmQfB4Yfv1Br
                         M9DIkzQIyEgxADvf4LVS/2Dp4C+iqQM+P+A/5FQQGA0+XBDEB6r6C5ozYgR1yhnhtQAoG4Dk/oAPX4KogzSbmCBzT4zgzgXEDkbo
                         ubfAgv8r+FQTRt69e/dxgtILNxcXw5YtWxjOnTuH3rxhBjY1DMHrV2CBDVpiwcUvwsDJawKZjGP5CZ6IYmLCtg3qL8PrezuAnUTY
                         qAmhBW7oNQSl7Zv/4AwhKA3M3AygUbUfwBLsN+SYR2YJhv9MMqDp6wEf1fr04howTEHNJm7I8CuQJShtwcAnpgjMNM+Abv0LXm2L
                         Grb/gZ30J8B+3klgR/0LA+oqAEhcfXr5hEFA4gkw0UmD54NApTC/uAEDt6Akw78/L4El83eGbx/uM3x+fRrI/4FkBka4AwQQeRN8
                         nBzARHWN4f/vPwwxnrag+Qr4ubCgJtOXz1//HD52HJhJ/jKwsrKAS2Z2dnYGbh4eBn4+fnD7/xdQD6gWYQbKg4ZymUGnH4DWyoFO
                         IQQ2DWHDubAO8z9oMoKcDQCb0ANmEmCt8hvYzPoJ7OSDOvoQ+je4bwNifwc2xR4B5YWA/aJ7N2/+B6UbTk5OhmfPsK4QZgWaKgid
                         4YUmNKCjQMu0GcEVHLQ6/s+INYmDj2rnuAzMGLAa4B+eTiCtGvmg865kECXif0ZoYP2BnNxFs+H8/1jUY5/0+/7pGcO3d8cZuIW9
                         gPn0O7TpAzpDSwyYIKSwhi9In6AUKC6UGF7cXM/w9zd68xNkxheG98/2MojIxwHzG+RAbPAmK1YJBgY2aXAtxcVnw8DBLc7w8s52
                         aK2MPPABBwABRF7GAKVOLk6G89fugle5BtoYMPz8jVjB8ODRI4aLFy+CM8Xtm7cY7ty9wyAjIws+JvMRUA4UXdJS0sBOMAuQ/5BB
                         U1OT4cmTpwy/fv1kUFZWYbh27SqDuLg4g6SkFNZJOMROmv8Mz/nFGN5y84P7HuiqWIG1tMrL+wxs7BwMvHx8DFpaWl/5+HjBbuTm
                         5gbb8+LFC5RKHui3pwxMjPLQdjCsvfoXXIkRar78/QPanwHKDLxIzSRYln4PdBILQXPIzQyoQ7J/8M+fwDYBobQ80RPxX3D/h5FR
                         BryIEVZq462tQH0ERl7oTDUucyHibx/vAzZxxBg4eAyBpeof6OmLf/GeGQxa9s/ObQxsVl0GZozrWPpioLO9LgD7iPIMPEIOkJNW
                         wUss/sDd9PcfaD+KLdCMKwx/fr3AVWsABBBlS0I42BguXb8HTjcOusoMiHkCyD4I0JIfSWlphucvnzMIiwiDM8abt6/BcSIIrEVY
                         gc3yV69fMoiIijF8+vwZWMoD+xFiYgxcD+8x8AsKMogBMweoaYYLsP75zXDbzIXhmpENZDKEgRE6UsYI3iTG9+Ujg92aiQz/QGeh
                         ff7KYGpqMkVFRdkWtFZKRESEYcOG9agZA9Rhe/toH4OoggKwGSIOr+aJS5aMDJ9fXQEfPoDoYyBOF3n3+CCDmIoasJoXhs4sIje1
                         CNvCzIqoMlELgf/gs0z/M7JAEhfeEu0vMDF8B88ZcPCKAktoNqDtf5Ha7f+QCr/f4H3WoopSQPP5oJU0upv/oxQqn19vYeCXjAGq
                         54T6HXnwAPmgh39Ad3xleHF7PYOA5BtgIjYBhjc3OFwYkTqKWOswcLsZVPBwMSDWbSEy87+/vxje3N/J8Pv7V2B/0QboR274ytz/
                         8PEtFmD/Qxx64DNsXwkKAAjAyRWsNAwE0YmtJtZUGkgr5JZiPQvquR8gSG/56Vz0IgpeRIzVIqZr1Rbb+GZmQ1PRIgYCIZlsNpvs
                         7rw3s8/5D53txMerJ96ndNo/pG6nJZjDdT0DcOuH7VAoUQ8jdg1Yowbsswks6DiqR6vZyRuSIqJpJo7EPNhWgHqxWIthFdDPhbmS
                         6LdEwjXVo7ozCZDBdRoMzkwURbvc2Xg9RpIklKbpyu9HLFHjNWP4wCd4AMtv/kHHSZirHD4wQHrBjc0Y47nCrqginud3qdE6EulP
                         M7rBx7uztuO1warOPuHn0Rw97hTZNawnJdXZBHDt42KIuz0YbP1a38+Zobd8hNH2A+8XSOfgKPtLdmHr8URLfSuuswvM1VPchXbI
                         h1fwzcv09ddvda7btuuhbNjjOL+/xAca2rINLderlATFtsyurPreCGK4oXtCk1Px85JcxYELyjEAzWe3to0Nrcoc1UlT3X0Z3HaC
                         A8wObT3P5Uqwa0rjx3O8y4MtY1JUc9SwfQnA2tXrIAgD4UaFEiThFfSB3Nx9U30DFxxh0RgHo4IFRZHgXa+NBW10cGApUC7N/X13
                         X8t/SITcYctky2bTiQTS6SkNomjFkjiGNIbLXwD4gEvQKBBbmLRwdERDzyN6SOfe96S2kUZB/KsaxBiwO8yD+EKOIUUEDQPGxiOI
                         rkHgIu5Ag+JYnXqn6JA3K8UOrrlcXPJMv1SZNPM0N5SrfRwOUrPLPCPQKZXDXvZ1POy+02f98BUl+hwbkIwdCl0cuEE0WjA6TDo0
                         5LVhGko9Lhnk+Zn2+g8lS/2WXl3Pa9lxJiXuGR3nbqTTa7eR+7Jpv4R+vjIUmynqc6O8PaV1xVGod/S5v5/Kq7qSV1mae03LSBBw
                         i71QsrvGvDrNtfaHngKIOhkDmPCfv3jH8PUfM4OjmSHDm3fvl8vJynD8hS4B5+RgZ7h+7/Hfj1++MNqaaTJ9+fYdPvnKzcXBsPfE
                         hT+q8nIs0mLCwM43eZvgeIB9nsPnr/6RExJgVpWTYvz+8xc8k4FIUMdfVFT0Mw83F7iZt/PwKYYPf7AWrH+gJT1s0gn9gOY/OMIN
                         efIJfZYaFqGwiEY+xhMzcsSUQTPUDPDDzpCbTqD+Mz+wlfcRWNj9+fkfKTF/g5rzhQHvEe0oozlMSP2JH2g1wH+0ScefSGLY1ofB
                         /PgFyTyYeogfWYBBKWfAAOw8A137ATlD/oaGMysD4bNm/yHFEWqhws7NAL4b49v739CztGDuQD7BkRGpEPuBq6YGCCBym1Kgkkkf
                         ahkjOND+/D3Lycz0bVZ3DYOemiKwX/EKXDDwcHEx3HvynCGvcQLnx09ffvbU5PyzNtRm+PDpK4MgPw/Dut1HGLr65whLK8u/nVKX
                         z8DFwY64+Qi0IhlYy7CxQtLhN1BNAJcDFgOc7OAaA5Tx9p08z1DdPp1fWFTo48S6PAZxYQFwcwxkHmwkVgbY3zl6/grDgiXrGHbv
                         PwrshAArBGBzG1yKfQe2Zj6/YkApqfjFVIEWghYOvgFHFvisJRkloLr7DL9Bx7sh3+CB0hFEtMMRJ/nBzIVs2hGWUwMmjsdAez+i
                         lOiiikB3iTHgORZeBejeV8BOJmjlLcjtHAwiCjLAWuMxsImEbw4FtY2PaHEzYskwyL0n5LkZBuz9BvhMOiMDj7AssLb7z/D+6Uuo
                         vr/wcBFR5Aa2+4HV3f+H4Mzx/RMDWtMKmcaXMZDD+S90Rp+BQUIN1BcTATZTWRhe3n6BZB4zkj8YkOIKfsbwf7SMABBA5GYM0DEh
                         oCFB0CwqaJnEfWACuAdsC/1lBp0yAyy9QU0ZUN8jwNuRYefRMwyvX74xBLap+FjYWA8mBnowLN+4iyEqwI1h1pINwEqO1QLY3nnM
                         wcv9lAX58AKgficHcwY3SyNws6h30VqGd+8+QJZtAGuWrEg/BmtTPYa7wIxX1DoVVHOBFv49Y+PivMbOwsLAzc3JUJYYBs5YR85f
                         Y9ix5zDDZ9CarQ/AtMjLg1gCAj58HHRqNLDwePMQWI58glRnEmpW4AB8cesEPLKUzKyAkXoD2Ax5zYB7VxqwrQY0nw/YtOUBpoMP
                         zxiACQV1eYOCsQ8wMV8HtnURx5WIKEBqg794j6VyB+InDEysVxk+PQc2VD5wMUhrezE8PLeH4fePDwT6Q+jLJ9DOvQZq5eT/D26q
                         vbqDnKGxzIABS2ZWdogaUA0HKlQ+AN0joW4E7qw/vXoARS9IvbQ2aB+4C7DtvwWo/ic4Y0NWG4BqP9BOPmT7GInyBwcv6IBtiBmQ
                         OyMUgZlDm+H94y0M755iG7XDth4GPV8wAAQg7WxaGgaCMLwE/KBqFcGiiBSkKAjSo3fxIP5e8eahR3+AggiCeBArVuO2a0wk6zw7
                         adpAxIKHEMhmN5nMx868w0z+G3xT/A5j7ytQLsc4VsA1wuJHUTf4wd73DA0eaFLAeXFhEvj6GqgOGDgrvBDuna4FsSOze9AxfVGW
                         oXX8z+zYUCvs/U35LiT3IJE8yfxcGSv/Ci+RHP3oq6uyuXcUAl73flWOI+wujoSRee381wd1g+jOF9ot5ap89HCmZDoZ0uxAxjun
                         4ovfmdSpYlAbj0D+nZQ+Mdr1/DoIMvXPPj8zo7fLAttXAadhBYK70qoioHyTweN0pl940dDrxDVLRZBPGXaW1EP1VjbQrX29fwwI
                         hF03hrauyT7XZH4v0G1flOb1HeH+dlNcQd7/QsaSsA7IclOMQSr8S6zyhud+SWiyvKGGPX7WXZT/NroB31+MSFttwGpLnz+R47as
                         cWi+03PzdKsKN1MGoqoHPwKQdgU7CQNBdBsBsRbbWBLwJDFcIMYYEEzg4MXfNlEs9iKJEWM8EC4aD0RPctBoW9+bHcQYT3rYNCnM
                         dmb2zXRmdrv73xzD+ZHosV7aRDvQWHMdBjBU41mCsrhKui6uN5qcHaJda1mtpyEaw7VHgPkC7V157Yjgi105/FI8nd7PZRtCe8IS
                         wbqHfhr6rAhZ/2Ip8JbSMxGjZx2Y5W59dbSWaJdnl3nhKRLeJ4CVGt81fpWJ7ZmEjGnSNW4wQt/86qytvG5Y5+CMQPchyzDSpA/a
                         qlRN+K20X4kwSG8yuJW6HYliiedgABUmFi+WJtuWD0lCGQoMfyklUkbqtwxQRvYEJujGC49VR89Wbv9Vyp+5Ape4NHSsbnFvLEsu
                         6K0JqrDGEumRnCCVZT7kn4usrg8dB6SBBZh9pacDvIQBJ9BNE/8lbxPhN0tboIkBdO7ksQOdeeBjAEN5gfe2Bvh9fpFgLpWtQ0yT
                         jim4NckRjHOFvu7k91y+B/lmMNYJeA7QX9t4myfyZsmvcSKwj7frila9zr/mjQhy5jMMrR7GfwL2pwCsXU1Lw0AQDQi5WOghNDWK
                         tigWBEEiIhb6z6W0KT20gif14g/wJOQsZLd9b+YFS6k3AwNlu9/zZmY/Z//7iiABUoLeBKRaE599E5gKjG35VA2nGvMVYsBc4SPF
                         OVcarq/yvPiJdQCtwO+zYwQGteYSRLU42VmGHZugeb6pANjW+dE0cJJMQc9gzLcBeBPpO5X3u1/taIjf/S4QntlyVwwFiExcgC4B
                         igFAwTyv5TeJQzDa8z7mJDHpFq5p/YUyDNfih9ITKBSGJ7WNdcw0VD3EM7qaed/pTwoifTJVoGOA5Q5DLWrxHL/pgHqpOt4AVD0D
                         ZP/KtXhs+MxVjv9q1KfSmPvBNsdiwEQt3IPWoJkJbgxn6puOeNbydYg0OrC3+UK8F+T7Y4owGx620t7uC1N2LDuGFepTWt6MHxo6
                         gRhZeVb3xq30UQphasYI+xTPiLHbvU1Ot5ice3QyJ35c6eOeEAWH7f/j2wrA2rWtIAzD0DjY6MAH/0BU/P8fcjD2IHhhoIgIc52m
                         OWnDKD65x93anuaetP33mu8K0lyLkB4zpmDJuxXCiZEO61BpeO8AJjlCwhMGf8azEkDkwnlcoHaCpF0bxlCC4vBdS7reOlWa1sCj
                         Nzy8wBiuRkN6U/5wN9rwIpI+fOfAmC2y4DtJ1Pu5ddun9gJ2Hn0cgh8h93LXDb6dMsoLuHBfO9G2sR8jpQrTIuDIBMkmk5htBbBq
                         or9ItDGY67w4jLvGvzsImz3eH0yo9Ak8EpH+SBNTOqCnpHQKkrbx1VbTCoIRJu2kO4+MmLc3QuD5HJAyJW/A6Ja8q6RMZeXYp8qm
                         jz4CiNoZAzY6A3M0+gQNDzRjiEKbM7g2pLBA5RixjJUjJ2ZsehlxqPmH1OxDNvc9tNTRhzYbXkFru18MhJcqI7uVAYdbmRhwHxSA
                         3nn/BzXvN961RtgLBOSVo/+Q3MAKTbyM0Iz6Cl5ao+pnxqIfVgAoQfW/gGZYWKYWgDazBMBNYYR6QmGGHi9c0NYAyA23oIUXA7SQ
                         fAet3RmhBR5yOpNDGvq+gdEyAfd1kYKfjRMysMHCBltiAskcWABAANHqlBBYIuRBilxmaLMClOhA6729oLn9K551P8jsX1B9ID3S
                         UPofAb2MaBkG5l8utEQJKt32QMXDwc00SGn1GypGzBolZPZvaG3FCS3p2HCsRWJH4yMPi3Kj1bjI8yjseNzBhCYOCqN9DPg3VTHi
                         YTNB2+/oPXHYiOROaHiB+omnofHEhidT8ECbr2+Q0sUPaOGETf0j6CDPeTQ3sEH7YNhWg/JDw+gnSrAjTQNgiKEBgACkmz0PAUEQ
                         hueuUqDRamgVfgJaJ/6z1lEoNELiqo2LiGg4iY9s8mxMxqpcebcf73zszDs7uX8PRhoxRBNu3yLSbIzhBQpSmSilI1WiHCyMcTjK
                         lHcxifQelXHUgtR/JfXPVCrvcQBQpoTegq91MvabM1c3iX5h3UOfJqwZayLtuGhoQD1vGHmAHG2czj6eMg05dAvTkxAw1lVUT8Bx
                         BOPaBKOXafbdlRzBThnZ4sn8C+PPfHfMqeHIfW7PcqVLQcYTV85LaGNJjTeWz3/tK5U1S+zuTHDwwWyErlP87MD6DzDnUNoO2Auw
                         bdFLl1rti328BSDlbFoQBIIwvCsEWhl4qCwhMIJ+f9CvKaJABL1F3dqK0J7Jca95URGdr3fni3X+bdfOUORVgSznLKMr7zwPCbmy
                         a2+JsR7k+w7FpMqIMcDSOf4YUDSt2b3nVSf9OqGNLKV6t/HiCd8rVPTYqJSnMP0xlou2eP4ukhvXMk8qxRgvZLOkZrLYR9Briuqd
                         t1gtuhpScIujWCNj5YFBHyvoHQDS3HR/AYZ4TEk7EqJWoADkR6Kp6QYvDLgXTxzDp0SzE/aMoFMhSwZNBy4ygOgP7orQt4OXt2q+
                         2F/3rNOVxUnUXodOYy2Aj1JFjBw+L8j/RJ9bFm8Aj8c2bpx7e+bMRwBRZxEhfQAb1CMs0DYziL+VgUbHwFMIOKBuZYLWRqAEu4th
                         9KbVQQvQMwZAALEMIbfDOu6s0LbtjUGaKRiQRt+YoJ3V66OZYmgBgAADAD7SFWTaJnnBAAAAAElFTkSuQmCC" 
                         width="198" height="50" alt=" ZPanel - Taking Hosting to the Next level..." border="0" /></img></a>
	<div class="content">
	<h2><?php echo $reponame; ?></h2>
	<p>Total Packages: <b><?php echo $totalpackages; ?></b></p>
	<table cellpadding="0" cellspacing="0" class="zgrid">
	<tr>
	<th>Package</th><th>Downloads</th>
	</tr>
<?php
	foreach ($packages as $package){
		$count = 0;
		if (isset($downloads)){
			$founddownloads = TRUE;
			foreach ($downloads as $download){
				if (strstr($download, $package)){
					$count++;
				}
			}
		} else {
			$founddownloads = FALSE;
		}
		echo "<tr>";
		echo "<td>" . $package . "</td><td>" . $count . "</td>";
		echo "</tr>";
	}
?>
	</table>
	</div>
<?php
	if ($founddownloads == FALSE){
?>

	<h2>ERRORS:</h2>
	<p>RepoStats has opened your log file: <strong><?php echo $log; ?></strong> but there are no download entries for any modules.</p>
	<p>When a module is downloaded it Apache will log the download as comming from: "ZppyClient".  You either have the wrong log file, or there are no downloads for your modules yet.</p>

<?php	
	}
?>
	<div class="poweredbox">
	<p><strong>Powered by <a href="http://www.zpanelcp.com/" target="_blank" title="ZPanel - Taking hosting to the next level!">ZPanel</a></strong> - Taking hosting to the next level.</p>
	</div>
	</body>
	</html>

<?php
	}
	//repostats.php?cron=true -SCRIPT RUNS AS CRON
	if (isset($_GET['cron'])){
		$file = "./repostatslist";
		$line = "";
		foreach ($packages as $package){
			$count = 0;
			foreach ($downloads as $download){
				if (strstr($download, $package)){
					$count++;
				}
			}
			$line .= "" .$package . " " . $count . "\r\n";
		}
		$body = $line;
		$fp = fopen($file,'w');
		fwrite($fp,$body);
		fclose($fp);
	}

	//repostats.php?json=true -SCRIPT RETURNS JSON ARRAY
	if (isset($_GET['json']) && strtoupper($_GET['json']) == 'TRUE'){
		$lastpackage = $totalpackages;
		echo "[";
	    foreach ($packages as $package){
			$count = 0;
			foreach ($downloads as $download){
				if (strstr($download, $package)){
					$count++;
				}
			}
			$lastpackage--;
			echo "{";
			echo "\"package\": \"".$package."\", ";
			echo "\"downloads\": \"".$count."\"";
			if ($lastpackage <> 0){
				echo "},";
			} else {
				echo "}";
			}
		}
		echo "]";
	}


} else {
	echo "Cannot find or open log file: " . $log . "<br><br>Edit repostats.php and change the log variable to match the path to your apache log file.";
}
?>