jQuery(window).on('elementor/frontend/init', function() {
    var DynamicGalleryHandler = elementor.modules.controls.BaseData.extend({
        onReady: function() {
            this.updateCategoryOptions();
            this.listenTo(this.container.settings, 'change:gallery_categories', this.updateCategoryOptions);
        },

        updateCategoryOptions: function() {
            var categories = this.container.settings.get('gallery_categories');
            var options = {
                'all': 'All'
            };
            
            if (categories && categories.models) {
                categories.models.forEach(function(category) {
                    var categoryName = category.get('category_name');
                    if (categoryName && categoryName !== 'All') {
                        var key = categoryName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                        options[key] = categoryName;
                    }
                });
            }

            // Update all category dropdowns in gallery items
            var repeaterFields = this.container.el.querySelectorAll('.elementor-repeater-fields');
            repeaterFields.forEach(function(field) {
                var categorySelect = field.querySelector('select[data-setting="category"]');
                if (categorySelect) {
                    // Store current value
                    var currentValue = categorySelect.value || 'all';
                    
                    // Clear existing options
                    categorySelect.innerHTML = '';
                    
                    // Add new options
                    Object.keys(options).forEach(function(key) {
                        var option = document.createElement('option');
                        option.value = key;
                        option.textContent = options[key];
                        categorySelect.appendChild(option);
                    });
                    
                    // Set value (default to 'all' if current value doesn't exist)
                    categorySelect.value = options[currentValue] ? currentValue : 'all';

                    // Trigger change event
                    jQuery(categorySelect).trigger('change');
                }
            });

            // Force Elementor to re-render the widget
            this.container.render();
        }
    });

    // Register the custom control handler
    elementor.addControlView('gallery_items', DynamicGalleryHandler);
});

(function($){
    if (!window.elementor) return;
    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        // Only run for our widget
        if (model.get('widgetType') !== 'dynamic_gallery') return;
        // Helper to get categories from the repeater
        function getCategories() {
            var cats = ['all'];
            var controls = panel.model.get('settings').get('gallery_categories') || [];
            controls.forEach(function(cat) {
                if (cat.category_name && cat.category_name.toLowerCase() !== 'all') {
                    cats.push(cat.category_name);
                }
            });
            return cats;
        }
        // Update all item category selects
        function updateItemCategorySelects() {
            var cats = getCategories();
            var $repeater = panel.$el.find('[data-setting="gallery_items"]');
            $repeater.find('.elementor-control-field select').each(function(){
                var $select = $(this);
                if ($select.closest('.elementor-control').find('.elementor-control-title').text().trim() === 'Category') {
                    var val = $select.val();
                    $select.empty();
                    cats.forEach(function(cat) {
                        var slug = cat === 'all' ? 'all' : cat.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                        $select.append('<option value="'+slug+'">'+cat+'</option>');
                    });
                    if (val && $select.find('option[value="'+val+'"]').length) {
                        $select.val(val);
                    }
                }
            });
        }
        // Watch for changes in the categories repeater
        var observer = new MutationObserver(function(){
            updateItemCategorySelects();
        });
        var $catRepeater = panel.$el.find('[data-setting="gallery_categories"]');
        if ($catRepeater.length) {
            observer.observe($catRepeater[0], {childList:true, subtree:true, characterData:true});
        }
        // Initial update
        setTimeout(updateItemCategorySelects, 500);
    });
})(jQuery); 