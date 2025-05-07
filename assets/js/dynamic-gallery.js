jQuery(document).ready(function($) {
    // Initialize Isotope for each gallery instance
    $('.dynamic-gallery .gallery-grid').each(function() {
        var $grid = $(this);
        
        // Initialize Isotope after all images are loaded
        $grid.imagesLoaded(function() {
            $grid.isotope({
                itemSelector: '.gallery-item',
                layoutMode: 'fitRows',
                transitionDuration: '0.4s'
            });

            // Show grid after initialization
            $grid.css('opacity', 1);
        });

        // Handle filter button clicks
        var $filterButtons = $grid.closest('.dynamic-gallery').find('.filter-button');
        
        $filterButtons.on('click', function() {
            var $this = $(this);
            var filterValue = $this.attr('data-filter');
            
            // Update active class
            $filterButtons.removeClass('active');
            $this.addClass('active');
            
            // Filter items
            $grid.isotope({ 
                filter: filterValue === '*' ? '*' : filterValue,
                transitionDuration: '0.4s'
            });
        });

        // Initial layout
        $grid.isotope('layout');
    });

    // Handle window resize
    $(window).on('resize', function() {
        $('.dynamic-gallery .gallery-grid').each(function() {
            $(this).isotope('layout');
        });
    });
}); 