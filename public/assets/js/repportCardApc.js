/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var note;
alert(3);

$("td.appreciation").each(function () {
  
    note = this.innerText;
    
    if (note >= 0 && note < 10) {
        //console.log(note);
        $(this).next().next().next().html("NA");
        
    }
    if (note >= 10 && note < 15) {
        $(this).next().next().next().html("ECA/IA");
    }
    if (note >= 15 && note < 18) {
        $(this).next().next().next().html("A");
    }
    if (note >= 18 && note <= 20) {
        $(this).next().next().next().html("A+");
    }
});
$("td.notetrim").each(function () {
  
    note = this.innerText;
    
    if (note >= 0 && note < 10) {
        //console.log(note);
        $(this).next().next().next().html("NA");
        
    }
    if (note >= 10 && note < 15) {
        $(this).next().next().next().html("ECA/IA");
    }
    if (note >= 15 && note < 18) {
        $(this).next().next().next().html("A");
    }
    if (note >= 18 && note <= 20) {
        $(this).next().next().next().html("A+");
    }
});
$("td.noteYear").each(function () {

    note = this.innerText;
    if (note >= 0 && note < 10) {
        $(this).next().html("NA");
    }
    if (note >= 10 && note < 15) {
        $(this).next().html("ECA/IA");
    }
    if (note >= 15 && note < 18) {
        $(this).next().html("A");
    }
    if (note >= 18 && note <= 20) {
        $(this).next().html("A+");
    }
});
//note = parseInt(note);


           