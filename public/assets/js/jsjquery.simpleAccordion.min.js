// ---------------------------------------------------- //
// SIMPLE ACCORDION v1.2
// Last update : December, 2017
// Author : BeliG
// Documentation : http://www.design-fluide.com/?p=1416
// ---------------------------------------------------- //  
!function(e){e.fn.simpleAccordion=function(i){var t=e.extend({item:".ui-accordion-item",trigger:".ui-accordion-trigger",content:".ui-accordion-content",active:"active",autoClose:!1,multiOpen:!1,speed:300},i),n=function(e){return!!e.hasClass(t.active)},c=function(e){e.find(t.content).eq(0).slideUp(t.speed,function(){e.removeClass(t.active)})},o=function(e){if(!t.multiOpen){var i=e.siblings("."+t.active);c(i)}var n=e.find(t.content).eq(0);n.hide(),e.addClass(t.active),n.slideDown(t.speed)};return this.each(function(){var i=e(this),a=!i.find(t.item).length;i.find(t.trigger).on("click.simpleAccordion",function(s){s.preventDefault();var r=a?i:e(this).closest(t.item);n(r)?c(r):o(r)}),t.autoClose&&e(document).on("click",function(n){0===e(n.target).closest(i).length&&(a&&i.hasClass(t.active)?c(i):i.find(t.item+"."+t.active).each(function(){c(e(this))}))})})}}(jQuery);