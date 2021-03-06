<?php echo get_header(); ?>
<?php echo get_partial('content_top'); ?>
<?php if ($this->alert->get()) { ?>
    <div id="notification">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->alert->display(); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div id="page-content">
    <div class="container">
        <div class="row">
            <?php
            if (partial_exists('content_right')) {
                $class = "col-sm-9 col-md-9";
            } else {
                $class = "col-sm-12";
            }

            if (partial_exists('content_left')) {
                $menu_class = "col-sm-9 col-md-9";
            } else {
                $menu_class = "col-sm-9";
            }
            ?>

            <div class="<?php echo $class; ?>">

                <div class="row wrap-vertical">
                    <ul id="nav-tabs" class="nav nav-tabs nav-justified nav-tabs-line">
                        <li class="active"><a href="#local-menus" data-toggle="tab"><?php echo lang('text_tab_general'); ?></a></li>
                        <li><a href="#local-reviews" data-toggle="tab"><?php echo lang('text_tab_review'); ?></a></li>
                        <li><a href="#local-information" data-toggle="tab"><?php echo lang('text_tab_info'); ?></a></li>
                    </ul>
                </div>

                <div class="tab-content tab-content-line">
                    <div id="local-menus" class="tab-pane row wrap-all active">

                        <?php echo get_partial('content_left', 'wrap-none col-sm-3'); ?>

                        <div class="<?php echo $menu_class; ?>">
                            <?php echo load_partial('menu_list', $menu_list); ?>
                        </div>
                    </div>

                    <div id="local-reviews" class="tab-pane row wrap-all">
                        <div class="col-md-12">
                            <div class="heading-section">
                                <h4><?php echo sprintf(lang('text_review_heading'), $location_name); ?></h4>
                                <span class="under-heading"></span>
                            </div>
                        </div>

                        <?php echo load_partial('local_reviews', $local_reviews); ?>
                    </div>

                    <div id="local-information" class="tab-pane row wrap-all">
                        <?php echo load_partial('local_info', $local_info); ?>
                    </div>
                </div>
            </div>
            <?php echo get_partial('content_right', 'col-sm-3'); ?>
            <?php echo get_partial('content_bottom'); ?>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    $(document).ready(function() {
        $(function(){

            var layout = 'list', // Store the current layout as a variable
            $container = $('#Container'), // Cache the MixItUp container
            $changeLayout = $('#viewcontrols .btn'); // Cache the changeLayout button
            $listButton = $('#viewcontrols .listview'); // Cache the list button
            $gridButton = $('#viewcontrols .gridview'); // Cache the grid button

            // Instantiate MixItUp with some custom options:

            $container.mixItUp({
                animation: {
                    animateChangeLayout: true, // Animate the positions of targets as the layout changes
                    animateResizeTargets: true, // Animate the width/height of targets as the layout changes
                    effects: 'fade rotateX(-40deg) translateZ(-100px)'
                },
                layout: {
                    containerClass: 'list' // Add the class 'list' to the container on load
                },
                controls: { enable: true },
                callbacks: {
                    onMixFail: function(){
                        alert('<?php echo lang('text_no_match'); ?>');
                        $container.mixItUp('filter', 'all');
                    }
                }
            });

            // MixItUp does not provide a default "change layout" button, so we need to make our own and bind it with a click handler:

            $changeLayout.on('click', function() {

                // If the current layout is a list, change to grid:

                if(layout == 'list'){
                    layout = 'grid';

                    $listButton.removeClass('active'); // Update the list button as active
                    $gridButton.addClass('active'); // Update the grid button as active

                    $container.mixItUp('changeLayout', {
                        containerClass: layout // change the container class to "grid"
                    });

                    // Else if the current layout is a grid, change to list:

                } else {
                    layout = 'list';

                    $listButton.addClass('active'); // Update the list button as active
                    $gridButton.removeClass('active'); // Update the grid button as active
                    //$changeLayout.text('Grid'); // Update the button  as active

                    $container.mixItUp('changeLayout', {
                        containerClass: layout // Change the container class to 'list'
                    });
                }
            });

        });

    });
//--></script>
<?php echo get_footer(); ?>