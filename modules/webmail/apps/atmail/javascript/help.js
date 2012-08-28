function help(currFile, lang) {
        var wdh = 300; hgt = 410;

		var file = currFile || 'file';

        helpWin = open('html/' + lang + '/help/' + file + '.html' +  '', '', 'width=' 
        + wdh + ',height=' + hgt + ',left=100,top=100,scrollbars=yes');
        }


