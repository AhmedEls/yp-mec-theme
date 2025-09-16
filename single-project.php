<?php

/**
 * Template for displaying single Project posts
 * 
 * Place this file in your theme as single-project.php
 */

get_header(); ?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()) :
        the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class("project-single"); ?>>

            <!-- Project Title -->
            <header class="project-header">
                <h1 class="project-title"><span style="font-weight: 100; font-family: 'Courier New', Courier, monospace; display: inline-block; transform: translateY(-2px);">+</span> <?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?>
                    <p class="project-excerpt"><?php echo get_the_excerpt(); ?></p>
                <?php endif; ?>
            </header>

            <!-- Project Content -->
            <div class="project-content">
                <?php the_content(); ?>
            </div>

            <!-- Project Gallery -->
            <?php
            $gallery = get_field('proj_project_gallery');
            if ($gallery) : ?>
                <section class="project-gallery">
                    <h2 class="gallery-heading">Project Gallery</h2>
                    <div class="gallery-grid">
                        <?php foreach ($gallery as $image): ?>
                            <figure class="gallery-item reveal">
                                <a href="<?php echo esc_url($image['url']); ?>" target="_blank">
                                    <img src="<?php echo esc_url($image['sizes']['large']); ?>"
                                        alt="<?php echo esc_attr($image['alt']); ?>" />
                                </a>
                                <?php if ($image['caption']) : ?>
                                    <figcaption><?php echo esc_html($image['caption']); ?></figcaption>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </article>

    <?php endwhile; ?>

</main>

<style>
    .project-single {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .project-header {
        margin-top: 80px;
        margin-bottom: 1.5rem;
        /* text-align: center; */
    }

    .project-title {
        font-size: 2.5rem;
        margin: 0;
    }

    .project-content {
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .project-gallery {
        margin-top: 2rem;
    }

    .gallery-heading {
        font-size: 1.75rem;
        margin-bottom: 1rem;
        text-align: center;
        display: none;
    }

    .gallery-grid {
        column-count: 3;
        /* number of columns */
        column-gap: 1rem;
        /* gap between items */
    }

    .gallery-item {
        display: inline-block;
        width: 100%;
        margin: 0 0 1rem;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        background: #f7f7f7;
    }

    .gallery-item a {
        display: block;
        width: 100%;
        height: 100%;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* makes image cover its container */
        display: block;
        transition: transform .3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    /* Optional: captions overlay */
    .gallery-item figcaption {
        font-size: .85rem;
        color: #fff;
        background: rgba(0, 0, 0, 0.6);
        padding: .5rem;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
    }

    .reveal {
        opacity: 0;
        transform: translateY(40px) scale(0.95) rotateX(10deg);
        transform-origin: center;
        transition: all 0.9s cubic-bezier(0.25, 0.8, 0.25, 1);
        will-change: transform, opacity;
    }

    /* Active state */
    .reveal.reveal-visible {
        opacity: 1;
        transform: translateY(0) scale(1) rotateX(0);
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .gallery-grid {
            column-count: 2;
        }
    }

    @media (max-width: 600px) {
        .gallery-grid {
            column-count: 1;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reveals = document.querySelectorAll(".reveal");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add("reveal-visible");
                    }, index * 150); // stagger delay
                    observer.unobserve(entry.target); // animate once
                }
            });
        }, {
            threshold: 0.2
        });

        reveals.forEach(item => observer.observe(item));
    });
</script>

<?php get_footer(); ?>