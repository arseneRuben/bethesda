/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var note;

$("td.notetrim").each(function () {

    note = this.innerText;
    if (note >= 0 && note < 6) {
        $(this).next().html("NUL / WEAK");
    }
    if (note >= 6 && note < 10) {
        $(this).next().html("INSUF./BELOW. AVER.");
    }
    if (note >= 10 && note < 12) {
        $(this).next().html("PASS./ABOV. AVER.");
    }
    if (note >= 12 && note < 14) {
        $(this).next().html("A.B / FAIRLY GOOD");
    }
    if (note >= 14 && note < 16) {
        $(this).next().html(" BIEN / GOOD");
    }
    if (note >= 16 && note < 18) {
        $(this).next().html("T.B / VER. GOOD");
    }
    if (note >= 18 && note < 20) {
        $(this).next().html("EXCELLENT");
    }
    if (note === 20) {
        $(this).next().html("PARFAIT / PERFECT");
    }
});
$("td.noteYear").each(function () {

    note = this.innerText;
    if (note >= 0 && note < 6) {
        $(this).next().next().next().html("NUL / WEAK");
    }
    if (note >= 6 && note < 10) {
        $(this).next().next().next().html("INSUF./BELOW. AVER.");
    }
    if (note >= 10 && note < 12) {
        $(this).next().next().next().html("PASS./ABOV. AVER.");
    }
    if (note >= 12 && note < 14) {
        $(this).next().next().next().html("A.B / FAIRLY GOOD");
    }
    if (note >= 14 && note < 16) {
        $(this).next().next().next().html(" BIEN / GOOD");
    }
    if (note >= 16 && note < 18) {
        $(this).next().next().next().html("T.B / VER. GOOD");
    }
    if (note >= 18 && note < 20) {
        $(this).next().next().next().html("EXCELLENT");
    }
    if (note === 20) {
        $(this).next().next().next().html("PARFAIT / PERFECT");
    }
});
//note = parseInt(note);


           