(function($){
    $(document).on('click', '.dfg-filter-btn', function(e) {
        e.preventDefault();
        var filter = $(this).data('filter');
        $(this).addClass('active').siblings().removeClass('active');
        if (filter === '*') {
            $('.dfg-gallery-item').show();
        } else {
            $('.dfg-gallery-item').hide();
            $('.dfg-gallery-item[data-category="' + filter + '"]').show();
        }
    });
    // Optionally, activate the first filter by default
    $(document).ready(function(){
        $('.dfg-filter-btn[data-filter="*"]').addClass('active');
    });
})(jQuery); 