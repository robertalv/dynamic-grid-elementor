jQuery(function($) {
    function initDynamicGalleryRamble($scope) {
        $scope.find('.boxxy-large.filterbox-isotope').each(function() {
            var $grid = $(this).isotope({
                itemSelector: '.boxxy-large__item',
                layoutMode: 'fitRows'
            });
            $(this).data('isotope', $grid);
        });

        $scope.find('.filterbox__item').off('click.dynamicGallery').on('click.dynamicGallery', function() {
            var filter = $(this).data('filter');
            var $container = $(this).closest('.dynamic-gallery-ramble');
            var $grid = $container.find('.boxxy-large.filterbox-isotope').data('isotope');
            var filterValue = filter === 'cat-all' ? '*' : '.' + filter;
            $container.find('.filterbox__item').removeClass('filterbox__item--active');
            $(this).addClass('filterbox__item--active');
            $grid.isotope({ filter: filterValue });
        });

        // Trigger layout on load
        $scope.find('.filterbox__item.filterbox__item--active').trigger('click');
    }

    // Frontend
    initDynamicGalleryRamble($(document));

    // Elementor editor support
    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function($scope) {
            if ($scope.find('.dynamic-gallery-ramble').length) {
                initDynamicGalleryRamble($scope);
            }
        });
    }
}); 