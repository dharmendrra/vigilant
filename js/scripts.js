$(function() {
	$('#menu').find('.toggle').on('click touchstart', function(event) {
		event.preventDefault();
        event.stopPropagation();
		$('#menu').toggleClass('active');

	});
	//to stop link propagation in menu bar
    /*
    $('#menu').singlePageNav({
		updateHash: false,
		threshold:10,
        filter: ':not(.external)'
    });
    */
});	