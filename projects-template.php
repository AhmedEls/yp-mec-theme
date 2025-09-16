<?php
/*
Template Name: Projects Template
Template Post Type: project 
*/

get_header();

?>
<div style="min-height: 100vh;">
    <?php
    while ( have_posts() ) : the_post();
        the_content();
    endwhile;
    ?>
</div>
<?php
get_footer();
?>