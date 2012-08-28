/**
 * check_me.js
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This JavaScript app is the driving power of the SquirrelSpell's
 * main spellchecker window. Hope you have as much pain figuring
 * it out as it took to write. ;))
 *
 * $Id: check_me.js 14084 2011-01-06 02:44:03Z pdontthink $
 */

var CurrentError=0;
var CurrentLocation=0;

var CurrentLine;
var CurrentSymbol;
var ChangesMade=false;

/**
 * This function loads spellchecking errors into the form
 * displayed to the user.
 *
 * @return void
 */
function populateSqspellForm(){
  CurrentWord=Word=misses[CurrentError];
  WordLocations = locations[CurrentError].split(", ");
  CurrentLoc = WordLocations[CurrentLocation];
  if(CurrentLocation==WordLocations.length-1) {
    CurrentLocation=0;
  } else {
    CurrentLocation++;
  }

  tmp = CurrentLoc.split(":");
  CurrentLine=parseInt(tmp[0]);
  CurrentSymbol=parseInt(tmp[1]);
  document.forms[0].sqspell_error.value=Word;
  LineValue=sqspell_lines[CurrentLine];
  StartWith=0;
  NewLineValue="";
  if (CurrentSymbol > 40){
    StartWith=CurrentSymbol-40;
    NewLineValue = "...";
  }
  EndWith=LineValue.length;
  EndLine="";
  if (EndWith > CurrentSymbol + 40){
    EndWith=CurrentSymbol+40;
    EndLine="...";
  }
  NewLineValue+=LineValue.substring(StartWith, CurrentSymbol) + "*" + Word + "*" + LineValue.substring(CurrentSymbol + Word.length, EndWith) + EndLine;
  document.forms[0].sqspell_line_area.value=NewLineValue;

  if (suggestions[CurrentError]){
    WordSuggestions = suggestions[CurrentError].split(", ");
    for (i=0; i<WordSuggestions.length; i++){
      document.forms[0].sqspell_suggestion.options[i] = new Option(WordSuggestions[i], WordSuggestions[i]);
    }
  } else {
    document.forms[0].sqspell_suggestion.options[0] = new Option("No Suggestions", "_NONE");
    document.forms[0].sqspell_oruse.value=Word;
    document.forms[0].sqspell_oruse.focus();
    document.forms[0].sqspell_oruse.select();
  }

  document.forms[0].sqspell_suggestion.selectedIndex=0;
  if (!document.forms[0].sqspell_oruse.value)
    document.forms[0].sqspell_oruse.value=document.forms[0].sqspell_suggestion.options[document.forms[0].sqspell_suggestion.selectedIndex].value;
  occursTimes = WordLocations.length;
  if (CurrentLocation) occursTimes += CurrentLocation-1;
  document.forms[0].sqspell_likethis.value=occursTimes;
}



/**
 * This function updates a line from the message with a new value,
 * received from the user.
 *
 * @param  lLine    line number.
 * @param  lSymbol  symbol at which the misspelled word starts.
 * @param  lWord    misspelled word
 * @param  lNewWord corrected word
 * @return          void
 */
function updateLine(lLine, lSymbol, lWord, lNewWord){
  sqspell_lines[lLine] = sqspell_lines[lLine].substring(0, lSymbol) + lNewWord + sqspell_lines[lLine].substring(lSymbol+lWord.length, sqspell_lines[lLine].length);
  if (lWord.length != lNewWord.length)
    updateSymbol(lLine, lSymbol, lNewWord.length-lWord.length);
  if (!ChangesMade) ChangesMade=true;
}

/**
 * This function is used to add a word user wishes to place in his/her
 * user dictionary to the form field for later submission. Since there
 * is no sane way to pass arrays between javascript and PHP, all words
 * are concatenated into one strings and separated with a "%".
 *
 * @return void
 */
function sqspellRemember(){
  CurrentWord = misses[CurrentError] + "%";
  document.forms[0].words.value += CurrentWord;
  /**
   * Now ignore all occurances of this word.
   */
  sqspellIgnoreAll();
}

/**
 * This function is called when the "Change" button is pressed.
 *
 * @return void
 */
function sqspellChange(){
  CurrentWord = misses[CurrentError];
  NewWord=document.forms[0].sqspell_oruse.value;
  updateLine(CurrentLine, CurrentSymbol, CurrentWord, NewWord);
  proceed();
}

/**
 * This function is called when the "Change All" button is pressed.
 *
 * @return void
 */
function sqspellChangeAll(){
  // Called when pressed the "Change All" button
  allLoc = locations[CurrentError].split(", ");
  if (allLoc.length==1) {
    /**
     * There's no need to "change all", only one occurance of this
     * word in the whole text.
     */
    sqspellChange();
    return;
  }
  /**
   * Dark magic.
   */
  NewWord=document.forms[0].sqspell_oruse.value;
  CurrentWord = misses[CurrentError];
  for (z=CurrentLocation-1; z<allLoc.length; z++){
    tmp = allLoc[z].split(":");
    lLine = parseInt(tmp[0]);  lSymbol = parseInt(tmp[1]);
    updateLine(lLine, lSymbol, CurrentWord, NewWord);
    /**
     * Load it again to reflect the changes in symbol data
     */
    allLoc = locations[CurrentError].split(", ");
  }
  CurrentLocation=0;
  proceed();
}

/**
 * This function is only here for consistency. It is called when
 * "Ignore" is pressed.
 *
 * @return void
 */
function sqspellIgnore(){
  proceed();
}

/**
 * This function is called when the "Ignore All" button is pressed.
 *
 * @return void
 */
function sqspellIgnoreAll(){
  CurrentLocation=0;
  proceed();
}

/**
 * This function clears the options in a select box "sqspell_suggestions".
 *
 * @return void
 */
function clearSqspellForm(){
  for (i=0; i<document.forms[0].sqspell_suggestion.length; i++){
    document.forms[0].sqspell_suggestion.options[i]=null;
  }

  /**
   * Now, I've been instructed by the Netscape Developer docs to call
   * history.go(0) to refresh the page after I've changed the options.
   * However, that brings so many pains with it that I just decided not
   * to do it. It works like it is in Netscape 4.x. If there are problems
   * in earlier versions of Netscape, then oh well. I'm not THAT anxious
   * to have it working on all browsers... ;)
   */
  document.forms[0].sqspell_oruse.value="";
}

/**
 * This function goes on to the next error, or finishes nicely if
 * no more errors are available.
 *
 * @return void
 */
function proceed(){
  if (!CurrentLocation) CurrentError++;
  if (misses[CurrentError]){
    clearSqspellForm();
    populateSqspellForm();
  } else {
    if (ChangesMade || document.forms[0].words.value){
      if (confirm(ui_completed))
        sqspellCommitChanges();
      else self.close();
    } else {
      confirm (ui_nochange);
      self.close();
    }
  }
}

/**
 * This function updates the symbol locations after there have been
 * word length changes in the lines. Otherwise SquirrelSpell barfs all
 * over your message... ;)
 *
 * @param  lLine      line number on which the error occurs
 * @param  lSymbol    symbol number at which error occurs
 * @param  difference the difference in length between the old word
 *                    and the new word. Can be negative or positive.
 * @return            void
 */
function updateSymbol(lLine, lSymbol, difference){
  /**
   * Now, I will admit that this is not the best way to do stuff,
   * However that's the solution I've come up with.
   *
   * If you are wondering why I didn't use two-dimensional arrays instead,
   * well, sometimes there will be a long line with an error close to the
   * end of it, so the coordinates would be something like 2,98 and
   * some Javascript implementations will create 98 empty members of an
   * array just to have a filled number 98. This is too resource-wasteful
   * and I have decided to go with the below solution instead. It takes
   * a little more processing, but it saves a lot on memory.
   *
   * It just looks heinous. In real life it's really nice and sane. ;)
   */

  for (i=0; i<misses.length; i++){
    if(locations[i].indexOf(lLine + ":") >= 0){
      allLoc = locations[i].split(", ");
      for (j=0; j<allLoc.length; j++){
        if (allLoc[j].indexOf(lLine+":")==0){
          tmp = allLoc[j].split(":");
          tmp[0] = parseInt(tmp[0]); tmp[1] = parseInt(tmp[1]);
          if (tmp[1] > lSymbol){
            tmp[1] = tmp[1] + difference;
            allLoc[j] = tmp.join(":");
          }
        }
      }
      locations[i] = allLoc.join(", ");
    }
  }
}

/**
 * This function writes the changes back into the compose form.
 *
 * @return void
 */
function sqspellCommitChanges(){
  newSubject = sqspell_lines[0];
  newBody = "";
  for (i=1; i<sqspell_lines.length; i++){
    if (i!=1) newBody+="\r\n";
    newBody += sqspell_lines[i];
  }

  opener.document.compose.subject.value=newSubject;
  opener.document.compose.body.value=newBody;

  /**
   * See if any words were added to the dictionary.
   */
  if (document.forms[0].words.value){
    /**
     * Yeppers.
     */
    document.forms[0].sqspell_line_area.value=ui_wait;
    /**
     * pass focus to the parent so we can do background save.
     */
    window.opener.focus();
    document.forms[0].submit();
  } else {
    self.close();
  }
}