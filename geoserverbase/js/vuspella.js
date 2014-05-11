/*vuspella.js [optional]
* this file is part of the VIETUNI typing tool 
* by Tran Anh Tuan [tuan@physik.hu-berlin.de]
* Copyright (c) 2001, 2002 AVYS e.V.. All Rights Reserved.
*/

// auto spell checking and correction for blunder, usefull in telex-mode
//

if (typeof(CVietString) != 'undefined') {
  if (!theTyper) theTyper = new CVietString("");
  theTyper.typing = scTyping;
  theTyper.checkSpell = checkSpell;
  reset = resetSChecker;
  vuspella = 1;
}

var spellerror = 0;
var buffer = "";
var restorewd = 1;
function resetSChecker(rflag){ spellerror=0; buffer=""; restorewd=rflag; }

function scTyping(ctrl) {
  this.changed = 0;
  this.ctrlchar = String.fromCharCode(ctrl);
  buffer += this.ctrlchar;
  if (spellerror) return 0;
  if (linebreak) {
    linebreak= 0;
    if ("fFzZwWjJ".indexOf(this.ctrlchar) >= 0) spellerror= 1;
  }
  else {
    this.keymode.getAction(this);
    this.Correct();
    if ((!this.changed || spellerror) && vuspella) this.checkSpell();
  }
  return this.changed;
}
//  this.checkSpell = null;



function checkSpell() {
  if(!this.keymode.istelex) return;
  var deletelast = spellerror;
  var caretpos = this.value.length-1;
  if (caretpos < 3) return;
  var c = this.ctrlchar.toLowerCase();
  var lvc = this.charmap.lastCharsOf(this.value);
  var lc = lvc[0].toLowerCase();
  var i = caretpos, c_at_i = '';
  do { c_at_i = this.value.charAt(i--); }
    while ( c_at_i != ' ' && c_at_i != '\n' && i >= 0);
  var wbegin = (i>=0)? i+1: -1;

  if ("fzwj".indexOf(c) >= 0) spellerror = 1;
  if (" \t\n([{:\"'<".indexOf(lc) >= 0) return;
  if (/\W/.test(c)) return;
  if ('bdklqsvx'.indexOf(c) >= 0) spellerror=1;
  else if ('cmntp'.indexOf(c)>=0 && !this.charmap.isVowel(lvc[0])) spellerror=1;
  else if ((c == 'r') && (lc != 't')) spellerror = 1;
  else if ((c == 'g') && (lc != 'n')) spellerror = 1;
  else if ((c == 'h') && ('cgknpt'.indexOf(lc) < 0)) spellerror = 1;
  else if ((lc == 'p') && (c != 'h')) spellerror = 1;
  else if ((lc == 'q') && (c != 'u')) spellerror = 1;
  else if ('ae:ea:ei:eu:ey:iy:ou:oy:yo:yu'.indexOf(lc + c)>=0) spellerror = 1;
  else if ((c == 'e') && (this.charmap.isVowel(lvc[0])>24)) spellerror = 1;
  else if (c == 'y') {
     var ind = this.charmap.isVowel(lvc[0]);
     if ((ind > 24) && ((ind-1)%24>1)) spellerror = 1;
  }
  else if ('aeiouy'.indexOf(c)>=0) {
     i = caretpos;
     var tmp = this.value;
     lvc = this.charmap.lastCharsOf(tmp);
     while ((i>=0) && !this.charmap.isVowel(lvc[0])) {
        i -= lvc[1];
        tmp = tmp.substring(0,i+1);
        lvc = this.charmap.lastCharsOf(tmp);
     }
     if (i >= 0 && i >= wbegin && i < caretpos) spellerror = 1;
  }

  if (spellerror && restorewd) {
    if (deletelast) buffer = buffer.substring(0,buffer.length-1);
    if (wbegin >= 0) this.value = this.value.substring(0, wbegin+1) + buffer;
    this.changed = 1;
  }
}

