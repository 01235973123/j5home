// Listings
jQuery(".listing-grid").addClass("listing-active");

jQuery(".listing-full").click(function() {
	jQuery(".listing").addClass("listing-full");
	jQuery(this).addClass("listing-active");
	jQuery('.listing-grid').removeClass("listing-active");
});

jQuery(".listing-grid").click(function() {
	jQuery(".listing").removeClass("listing-full");
	jQuery(this).addClass("listing-active");
	jQuery('.listing-full').removeClass("listing-active");
});