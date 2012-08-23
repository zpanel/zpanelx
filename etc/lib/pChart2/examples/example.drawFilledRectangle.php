<?php   
 /* CAT:Drawing */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));


 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawFilledRectangle() - Transparency & colors",array("R"=>255,"G"=>255,"B"=>255));

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>150,"G"=>200,"B"=>170,"Dash"=>TRUE,"DashR"=>170,"DashG"=>220,"DashB"=>190,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255);
 $myPicture->drawFilledRectangle(20,60,400,170,$RectangleSettings);

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>134,"B"=>27,"Alpha"=>30);
 $myPicture->drawFilledRectangle(30,30,200,200,$RectangleSettings);

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>31,"B"=>27,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledRectangle(480,50,650,80,$RectangleSettings);

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>125,"B"=>27,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledRectangle(480,90,650,120,$RectangleSettings);

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>198,"B"=>27,"Alpha"=>100,"Surrounding"=>30,"Ticks"=>2);
 $myPicture->drawFilledRectangle(480,130,650,160,$RectangleSettings);

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>134,"G"=>209,"B"=>27,"Alpha"=>100,"Surrounding"=>30,"Ticks"=>2);
 $myPicture->drawFilledRectangle(480,170,650,200,$RectangleSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawFilledRectangle.png");
?>