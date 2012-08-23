<?php
/////////////////////////////////////////////////////////
//	
//	source/edit_contact.php
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/edit_contact.php
	PURPOSE:
		Provide an interface for viewing/adding/updating contact info.
	PRE-CONDITIONS:
		$user - Session ID
		[$edit] - $id of item to modify or update (-1 means "new")
	POST-CONDITIONS:
		POST's data to contacts.php, which makes the requested changes.
	COMMENTS:

********************************************************/
include("../include/super2global.inc");
include("../include/header_main.inc");
include("../lang/".$my_prefs["lang"]."/contacts.inc");
include("../lang/".$my_prefs["lang"]."/edit_contact.inc");
include("../include/contacts_commons.inc");
include("../include/data_manager.inc");

//authenticate
include_once("../include/icl.inc");
$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
if ($conn){
	iil_Close($conn);
}else{
	echo "Authentication failed.";
	echo "</html>\n";
	exit;
}

//get data source name
$source_name = $DB_CONTACTS_TABLE;
if (empty($source_name)) $source_name = "contacts";

//open data manager connection
$dm = new DataManager_obj;
if ($dm->initialize($loginID, $host, $source_name, $backend)){
}else{
	echo "Data Manager initialization failed:<br>\n";
	$dm->showError();
}

//get groups
if (!isset($groups)){
	$contacts = $dm->read();
    $groups = GetGroups($contacts);
}

//if edit mode, fill in default values
if (isset($edit)){
	if (!isset($contacts)){
		$contacts = $dm->read();
	}
	if (is_array($contacts)){
		reset($contacts);
		while ( list($k, $foobar) = each($contacts)){
			if ($contacts[$k]["id"]==$edit){
				$name=$contacts[$k]["name"];
				$email=$contacts[$k]["email"];
				$email2=$contacts[$k]["email2"];
				$group=$contacts[$k]["grp"];
				$aim=$contacts[$k]["aim"];
				$icq=$contacts[$k]["icq"];
				$yahoo=$contacts[$k]["yahoo"];
				$msn=$contacts[$k]["msn"];
				$jabber=$contacts[$k]["jabber"];
				$phone=$contacts[$k]["phone"];
				$work=$contacts[$k]["work"];
				$cell=$contacts[$k]["cell"];
				$address=$contacts[$k]["address"];
				$url=$contacts[$k]["url"];
				$comments=$contacts[$k]["comments"];
			}
		}
	}
}else{
	$edit=-1;
}

?>

<FORM ACTION="contacts.php" METHOD=POST>
	<input type="hidden" name="user" value="<?php echo $user; ?>">
	<input type="hidden" name="delete_item" value="<?php echo $edit; ?>">	
	<input type="hidden" name="edit" value="<?php echo $edit; ?>">
	
	<table width="100%" cellpadding=2 cellspacing=0>
           <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
	      <td align=left valign=bottom>
	         <span class="bigTitle"><?php echo $cStrings[1]; ?></span>
	      </td>
           </tr>
        </table>

<table cellpadding="2" cellspacing="2" border="0"><!-- Global form width -->
    <tr>
      <td valign="top"><!-- Left Colum -->
         <table width="100%">
            <tr>
	      <td valign="top">
                 <table width="100%" border="0">
                    <tr>
                       <td class=mainLight>
                          <?php echo $ecStrings[3]; ?>:
                       </td>
                       <td align=right>
                          <input type="text" name="name" value="<?php echo $name?>">
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
                          <?php echo $ecStrings[4];?>:
                       </td>   
                       <td align=right>
                          <input type="text" name="email" value="<?php echo $email; ?>">
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
		 	   <?php echo $ecStrings[12];?>:
                       </td>
                       <td align=right>
                           <input type="text" name="email2" value="<?php echo $email2; ?>">
                       </td>
                    <tr>
                    </tr>
                       <td class=mainLight>
                          <?php echo $ecStrings[5];?>:
                       </td>
                       <td align=right>
                          <input type="text" name="url" value="<?php echo $url; ?>">
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
                          <?php echo $ecStrings[8];?>:</b>
                       </td>
                       <td align=right>
                          <input type="text" name="phone" value="<?php echo $phone; ?>"  size=20>
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
		         <?php echo $ecStrings[9];?>:
                       </td>
                       <td align=right>
                          <input type="text" name="work" value="<?php echo $work; ?>" size=20>
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
                          <?php echo $ecStrings[10];?>:
                       </td>
                       <td align=right>
                          <input type="text" name="cell" value="<?php echo $cell; ?>" size=20>
                       </td>
                    </tr>
                    <tr>
                       <td colspan=2 class=mainLight>
		          <br><?php echo $ecStrings[7];?>:
                          <br><textarea name="comments" rows=4 cols=30><?php echo $comments;?></textarea>
                       </td>
                    </tr>
                 </table>
	      </td>

            </tr>
         </table>
      </td>

      <td valign="top"><!-- Right colum -->
         <table width="100%">
            <tr>
	      <td valign="top">
                 <table border="0" width="100%">
                    <tr>
	               <td class=mainLight valign="top"><?php echo $ecStrings[6]; ?>:
	                  <select name="group">
		 	  <option value="_otr_"><?php echo $ecStrings[14]?>
		 	  <?php
				$groups=base64_decode($groups);
				$groups_a=explode(",", $groups);
				
				if (is_array($groups_a)){
					while (list($key,$val)=each($groups_a)){
						if (!empty($val)) echo "<option ".(strcmp($val,$group)==0?"SELECTED":"").">$val\n";
					}
				}
		 	  ?>
		 	  </select>
                       </td>
                       <td class=mainLight align=right>
		 	  <input type="text" name="other_group" value="<?php echo $other_group; ?>">
	      	       </td>
	   	    </tr>
                    <tr>
                       <td class=mainLight>
                          <br>AIM:<input type="text" name="aim" value="<?php echo $aim; ?>" size=12>
                       </td>
                       <td class=mainLight align=right>
                          <br>Yahoo:<input type="text" name="yahoo" value="<?php echo $yahoo; ?>" size=12>
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight>
		          ICQ:<input type="text" name="icq" value="<?php echo $icq; ?>" size=12>
                       </td>
                       <td class=mainLight align=right>
                          MSN:<input type="text" name="msn" value="<?php echo $msn; ?>" size=12>
                       </td>
                    </tr>
                    <tr>
                       <td class=mainLight colspan=2>
                          Jabber:
                          <input type="text" name="jabber" value="<?php echo $jabber; ?>" size=20>
                       </td>
                    </tr>
                    <tr>
	      	       <td colspan=2 class=mainLight valign="top">
	                  <br><br><?php echo $ecStrings[11];?>:
                          <br><textarea name="address" rows=7 cols=30><?php echo $address;?></textarea>
	      	       </td>
    		    </tr>
                 </table>
              </td>
            </tr>
         </table>
      </td>

    </tr>
    <tr>
       <td align=left><input type="submit" name="add" value="<?php echo $cStrings[8]; ?>"></td>
       <td align=right><input type="submit" name="remove" value="<?php echo $ecStrings[13]; ?>"></td>
    </tr>
</table>

     </FORM>
   </body>
</html>