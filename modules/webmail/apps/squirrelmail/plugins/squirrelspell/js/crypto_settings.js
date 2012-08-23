/**
 * crypto_settings.js
 * -------------------
 * Some client-side checks. Nothing fancy.
 *
 * $Id: crypto_settings.js 2301 2002-01-31 03:45:53Z graf25 $
 *
 * @author Konstantin Riabitsev <icon@duke.edu> ($Author: graf25 $)
 * @version $Date: 2002-01-30 19:45:53 -0800 (Wed, 30 Jan 2002) $
 */

/**
 * This function is the only thing. It is called on form submit and
 * asks the user some questions.
 */
function checkMe(){
  if (!document.forms[0].action.checked){
    alert (ui_makesel);
    return false;
  }
  if (document.forms[0].action.value=="encrypt")
    cmsg=ui_encrypt;
  if (document.forms[0].action.value=="decrypt")
    cmsg=ui_decrypt;
  return confirm(cmsg);
}
