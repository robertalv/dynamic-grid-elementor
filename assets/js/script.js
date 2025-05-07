jQuery(document).ready(function($) {
    var $grid = $('.gallery-grid');
    
    // Initialize Isotope after all images are loaded
    $grid.imagesLoaded(function() {
        $grid.isotope({
            itemSelector: '.gallery-item',
            layoutMode: 'fitRows'
        });
        
        // Show grid after initialization
        $grid.css('opacity', 1);
    });

    $('.filter-button').on('click', function() {
        var filterValue = $(this).attr('data-filter');
        
        $('.filter-button').removeClass('active');
        $(this).addClass('active');
        
        $grid.isotope({ filter: filterValue });
    });
});
