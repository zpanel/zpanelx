function opensearch(browser, from) {

if(!from)
from = '';

//if(browser == "ns")	{
//    var wdh = 750; hgt = 380;
//} else	{
var wdh = 640; hgt = 380;
//}

var helpWin;
var helpName = "search.php?func=searchframe&DefaultFrom=" + from;
helpWin = open('' + helpName + '', '', 'width=' + wdh + ',height=' + hgt +',left=100,top=100,scrollbars=yes,resizable=yes,status=yes');
}
