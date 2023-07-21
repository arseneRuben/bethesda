/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var note;

$("td.notetrim").each(function () {

    note = this.innerText;
    if (note >= 0 && note < 11) {
        $(this).next().html("NA");
    }
    if (note >= 11 && note < 14) {
        $(this).next().html("EA/IA");
    }
    if (note >= 14 && note < 17) {
        $(this).next().html("A");
    }
    if (note >= 17 && note <= 20) {
        $(this).next().html("E");
    }
   
});

$("td.noteYear").each(function () {

    note = this.innerText;
    if (note >= 0 && note < 6) {
        $(this).next().next().next().html("NUL / WEAK");
    }
    if (note >= 0 && note < 11) {
        $(this).next().next().next().html("NA");
    }
    if (note >= 11 && note < 14) {
        $(this).next().next().next().html("EA/IA");
    }
    if (note >= 14 && note < 17) {
        $(this).next().next().next().html("A");
    }
    if (note >= 17 && note <= 20) {
        $(this).next().next().next().html("E");
    }
});



           