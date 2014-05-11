/*vuspellb.js [optional]
* this file is part of the VIETUNI typing tool 
* by Tran Anh Tuan [tuan@physik.hu-berlin.de]
* Copyright (c) 2001, 2002 AVYS e.V.. All Rights Reserved.
*/                              

// interactive spell checking:
// simple implementation for use with very small dictionary


if (typeof(CVietString) != 'undefined') {
  if (!theTyper) theTyper = new CVietString("");
  theTyper.theSChecker = new CSpellChecker();

// interactive user interface:
//
vuspellb =
"<div id='divspell' onselectstart='return false;' "+
"style='background-color:#ACC0D2; position: absolute; width:380; display:none;"+
"border-width:3; border-style:solid;"+
"border-top-color:#CCE0F2; border-bottom-color:#7C90A2;"+
"border-left-color:#CCE0F2; border-right-color:#7C90A2;"+
"font-family: Verdana,Arial; font-size:12px;'>" +
"<form name='formspell' method='POST' action='none'>" +
"<table width='380' border='0'cellpadding='6' cellspacing='0'>"+
"<tr><td colspan='2' style='background-color:#007090; color:#FFFFFF; cursor:move;'"+
"onmousedown='return onMouseD();' onmouseleave='return onMouseU();'"+
"onmousemove='return onMouseM();' onmouseup='return onMouseU();'>"+
"<b>Soát l&#7895;i chính t&#7843; - T́m và thay th&#7871;</b></td></tr>"+
"<tr><td align=right><b>T́m th&#7845;y:&nbsp;</b></td>" +
"<td><input type='text' name='wrong' size='30' value=''></td></tr>" +
"<tr><td align=right><b>S&#7917;a thành:&nbsp;</b></td>" +
"<td><input type='text' name='right' size='30' value=''></td></tr>" +
"<tr><td align=right><b>G&#7907;i ư khác:&nbsp;</b></td>" +
"<td><select name='slist'></select></td></tr>" +
"<tr><td>&nbsp;</td>" +
"<td><input type='checkbox' name='ask'>"+
" &nbsp;Luôn h&#7887;i tr&#432;&#7899;c khi s&#7917;a</td></tr>" +
"<tr align=center><td><input type='reset' name='B0'"+
"value='K&#7871;t thúc' onclick='iCSpell(this.form,0); return true;'></td>"+
"<td><input type='button' name='correct'"+
"value='Thay th&#7871;' onclick='iCSpell(this.form,1);'>&nbsp;&nbsp;"+
"<input type='button' name='next'" +
"value='T́m ti&#7871;p' onclick='iCSpell(this.form,2);'></td></tr>"+
"</table></form></div>"+
"<script defer>"+
"var dspell=document.all.divspell, x=0, y=0, md=0;"+
"dspell.style.top=document.body.scrollTop+20;"+
"dspell.style.left=20; "+
"function onMouseD() { md=1; x=event.x; y=event.y; return false; }" +
"function onMouseU() { md =0; return false; }"+
"function onMouseM() { if (!md) return true; "+
"dspell.style.left = parseInt(dspell.style.left)+event.x-x;"+
"dspell.style.top = parseInt(dspell.style.top)+event.y-y;"+
"x=event.x; y=event.y; return false;}"+
"function iCSpell(form,cmd) { theTyper.theSChecker.stepCS(form,cmd); }</script>";

}


function CSpellChecker() {
  this.value = "";
  this.dic = new CDictionary();
  this.curindex = 0;
  this. regexp = //;
  this.found = null;

  this.startCS = startCS;
  this.stepCS = stepCS;
  this.checkThis = checkThis;
  return this;
}

function startCS(search) {
  this.value = " "+theTyper.txtarea.value+" ";
  this.curindex = 0;
  this.start = 1;
  this.counter1 = 0;
  this.counter2 = 0;
  this.search = search;
  form = document.forms.formspell;
  form.reset();   
  dspell.style.top = document.body.scrollTop;// + 60;
//  dspell.style.left = document.body.offsetWidth/2-200;
  form.ask.disabled= false;
  if (!search) this.stepCS(form, -1);
  else {                    
    form.slist.disabled= true;
    form.ask.disabled= true;
    dspell.style.display="block";
  }
}

function stepCS(form, cmd) {
  // make text fields of form VN-typeable:
  if (form && !form.ready) {
    initTyper(form.wrong);
    initTyper(form.right);
    form.ready = 1;
  }
  if (cmd == 0) {
    this.curindex = 0;
    dspell.style.display="none";
    return;
  }
  else if (cmd == 1) {
    var separator = "([,!\\/'\"\\(\\)\\-\\+\\?\\.\\s])";
    var sstr= form.wrong.value.replace(/([\.\?\+\-\$\*\/\(\)\[\]\^\\])/g,"\\$1");
    if (!this.search) sstr= separator+ sstr+ separator;
    this.regexp = new RegExp(sstr,'ig'); 
    this.found = this.regexp.exec(this.value);
    if (this.found) {
      var right = form.right.value;
      if (!this.search) right = this.found[1]+form.right.value+this.found[2];
      this.value = this.value.replace(this.regexp, right);
      this.counter2++;
      updateArea(theTyper.txtarea, this.value.substring(1,this.value.length-1));
    }
    if (this.search) return;
    this.curindex++;
  }
  else if (cmd == 2) {
    this.curindex++;
  }
  var stop = 0;
  while (!stop && this.curindex< this.dic.data.length) {
    stop = this.checkThis(form);
    if (!stop) this.curindex++;
  }
  if (stop) {
    dspell.style.display="block";
  }
  if (this.curindex >= this.dic.data.length) {
    var msg = "Sua chinh ta dda~ xong!\nTha^'y ";
    if (!this.counter1) msg = "Kho^ng tha^'y lo^~i chi'nh ta? na`o.";
    else msg+=this.counter1+" tu+` bi. lo^~i, su+?a "+this.counter2+" tu+`.";
    updateArea(theTyper.txtarea, this.value.substring(1,this.value.length-1));
    alert(msg);
    dspell.style.display="none";
  }
  this.start = 0;
}


function checkThis(form) {
  var rec = this.dic.getRecord(this.curindex);
  if (!rec) return 0;
  var separator = "([,!\\/'\"\\(\\)\\-\\+\\?\\.\\s])";
  this.regexp = new RegExp(separator+rec[0]+separator,'ig'); 
  this.found = this.regexp.exec(this.value);
  if (!this.found) return 0;
  this.counter1++;
  if (form.ask.checked || this.start) rec[2] = 1;
  if (!rec[2]) {
    var right = this.found[1]+rec[1]+this.found[2];
    this.value = this.value.replace(this.regexp, right);
    this.counter2++;
    return 0;
  }
  form.wrong.value = rec[0];
  while(form.slist.length>0) form.slist.options.remove(0);
  if (typeof(rec[1])=='object') {
    form.slist.onclick = function() { 
       this.form.right.value= this.options[this.options.selectedIndex].text;
    }
    for(var i= 0; i < rec[1].length; i++) {
      form.slist.options[i] = new Option(rec[1][i], i, false, false);
    }
    form.slist.options[0].selected = true;
    form.right.value = rec[1][0];
    form.slist.options.disabled = false;
  } 
  else { 
    form.slist.options.disabled = true;
    form.right.value = rec[1];
  }

  return 1;
}





/*************************************************************/
// RULES: (if you know some, please write down!)
// not yet implemented:
//
// 'GI' hay 'D':
//    + co' da^'u ho?i hoa(.c sa('c: GI (gia'o gio+'i gia?m gia?ng)
//    + nga~ hoa(.c na(.ng: D  (da. da^~n die^~m die^.n...)






/*************************************************************/
// spell dictionary (please help to build a usefull one!)
// structure: list of array: ["wrong", "right" or array of suggestions, 0 or 1]
// arr[0]: frequently occured misspelled word
// arr[1]: correct word or list of suggestions
// arr[2]: 0: can change without confirmation (word is definitely wrong)
//         1: not sure, ask for confirmataion!
//
//  example: ["da?", ["da~", "gia?", "gia~", "dda?"], 1]
//        or ["kho~i", "kho?i", 0]
//

function CDictionary() { return this; }

// no use as long as the dictionary is very small
CDictionary.prototype.indexOf = function (word) {};

CDictionary.prototype.getRecord = function(ind) {
  if(!theTyper) return null;
  var rec = this.data[ind];
  var oldkeymode = theTyper.keymode;
  theTyper.keymode = new CViqrKeys();
  rec[0] = theTyper.doConvertIt(rec[0]);
  if (typeof(rec[1])=='object') {
    rec[2] = 1;
    for(var i= 0; i < rec[1].length; i++)
       { rec[1][i]= theTyper.doConvertIt(rec[1][i]); }
  } 
  else { rec[1]= theTyper.doConvertIt(rec[1]); }
  theTyper.keymode = oldkeymode;
  return rec;
}


CDictionary.prototype.data = [

["ca~", "ca?", 1],
["cu+~a", "cu+?a", 0],
["dde^~", "dde^?", 0],
["ho~i", "ho?i", 0],
["ho+~", "ho+?", 0],
["kho~i", "kho?i", 0],
["khu~ng", "khu?ng", 0],
["lu+~a", "lu+?a", 0],
["sa'ch vo+~", "sa'ch vo+?", 0],
["su+~a chu+~a", "su+?a chu+~a", 0],
["tho+~", "tho+?", 0],
["tie^~u", "tie^?u", 1],
["ti~nh ta'o", "ti?nh ta'o", 0],
["tu+o+~ng", "tu+o+?ng", 0],
["vie^~n vo^ng", "vie^?n vo^ng", 0],
["vo+' va^~n", "vo+' va^?n", 0],
["vui ve~", "vui ve?", 0],

["bi. vo+?", "bi. vo+~", 0],
["co+?", "co+~", 0],
["da?", ["da~", "gia?", "gia~", "dda?"], 1],
["de^?", "de^~", 0],
["ddo+?", "ddo+~", 0],
["la(.ng le?", "la(.ng le~", 0],
["lu+?", "lu+~", 0],
["ma?n", "ma~n", 0],
["ma.nh me?", "ma.nh me~", 0],
["nga^?u", "nga^~u", 0],
["nghi?a", "nghi~a", 0],
["ngho+?", "ngo+~", 0],
["nha? nha(.n", "nha~ nha(.n", 0],
["nu+?", "nu+~", 0],
["tie^?n", "tie^~n", 0],
["vie^?n xu+'", "vie^~n xu+'", 0],
["vi?nh", "vi~nh", 0],
["vu?", "vu~", 0],
["vu+?ng va`ng", "vu+~ng va`ng", 0],

["ca^`ng", "ca^`n", 0],
["nga^ng nga", "nga^n nga", 0],
["ngu'c nga`ng", "ngu't nga`n", 0],
["ngu't nga`ng", "ngu't nga`n", 0],

["si'ch", "xi'ch", 0],
["soa", "xoa", 1],
["sua^n", "xua^n", 0],
["xai", "sai", 1],
["xan", "san", 1],
["xang", "sang", 1],
["xa'ng", "sa'ng", 0],

["ddu+o+.t", "ddu+o+.c", 1],
["ngu+o+.t", "ngu+o+.c", 0],
["vu+o+.c", "vu+o+.t", 1],

["__END_HERE__", "", 0]
];

