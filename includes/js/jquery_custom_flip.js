/* This jquery file deals solely with the card flipping functionality */
/* Flipping functionality modified from examples here:
http://forum.jquery.com/topic/jquery-flippy-plugin-reverse-issue
http://home.jejaju.com/play/flipCards/simple */

function doesCSS(p){
	var s = ( document.body || document.documentElement).style;
	return !!$.grep(['','-moz-', '-webkit-'],function(v){
		return  typeof s[v+p] === 'string';
	}).length;
}

$('html')
	.toggleClass('transform',doesCSS('transform'))
	.toggleClass('no-transform',!doesCSS('transform'));

$(function(){
	$('.flip').click(function(){
		$(this).parent().closest('.flipper').toggleClass('flipped');
	});
	
	$('.card').show('slide', {direction: "up"}, 700);
});
