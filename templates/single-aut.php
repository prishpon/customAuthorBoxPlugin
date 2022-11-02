<?php
// =============================================================================
//
// =============================================================================
get_header();
global $autorPosts;


?>
<style>
    .autor-box-page{
        display:flex;
        margin-top: 53px;
        border-bottom: 1px solid #D9D9D9;
        padding-bottom: 75px;
        margin-bottom: 75px;
    }
    .autorbox-image{
        transform: translateY(-30px);
    }
    .autorbox-image img{
        height:200px;
        border-radius:50%;
        margin-right: 30px;
        transform: translateY(10px);
    }
    .autor-box-title-page > h4{
        font-size:28px;
        font-weight:700;
        text-align:left;
        white-space: nowrap;
		font-family: 'Gabriela', serif;
    }
    .autor-related-posts{
        display:flex;
        align-items: center;
        flex-direction:row;
    }
    /*.featured-article{*/
    /*    max-height:150px;*/
    /*}*/
    .related-feature-img img{
        max-width:86px;
        margin-right: 20px;
    }
    .related-feature-img {
        float:left;
        height:64px;
        width:86px;
        display: flex;
        align-items: center;
        margin-right: 20px;
    }
    .social-aut-links-page{
        margin-top: 42px;
    }
    .social-aut-links-page a{
        color:#ff0700;
        padding-left:30px;
        font-family: 'Gabriela', serif;
        font-weight:400;
        font-size:16px;
        color: #FF0000;
        text-decoration:underline;
    }
    .follow a{
        /*display: flex;*/
        /*flex-direction: column;*/
        /*color:#ff0700;*/
    }
    .social-aut-links a{
        color:#ff0700;

    }
    .autor-subtitle4{
        font-weight: 600;
        font-size:20px;
        margin-bottom: 20px;
        color:#000;
    }

    .autor-article a{
        padding-top:20px;
        font-weight: 500;
        font-size:22px;
    }
    .autor-article a:hover{
        color:#ff0700;
    }

    @media screen and (max-width: 992px) {
        .social-aut-links-page > a:nth-child(4) {
            margin-left: 95px;
        }
        .social-aut-links-page > a:nth-child(5){
            margin-left: 20px;
        }
    }
    @media screen and (max-width: 768px) {
        .autor-box-page{
            flex-direction:column;
        }
        .autor-subtitle4{
            margin-top:20px;
        }
        .social-aut-links-page {
            margin-top: 38px;
        }
        .social-aut-links-page > a:nth-child(4) {
            margin-left: 0px;
        }
        .social-aut-links-page > a:nth-child(5){
            margin-left: 0px;
        }
        .autor-article a {
            font-size: 16px;
        }
    }
    @media screen and (max-width: 505px) {
        .autor-box-page{
            flex-direction:column;
        }
        .autor-subtitle4{
            margin-top:20px;
        }
        .social-aut-links-page {
            margin-top: 38px;
            line-height:35px;
        }
        .social-aut-links-page > a:nth-child(3)::after {
            content: '\A';
            white-space: pre;
        }
        .social-aut-links-page > a:nth-child(4) {
            margin-left: 93px;
        }
        .social-aut-links-page > a:nth-child(5){
            margin-left: 20px;
        }
        .autor-box-page{
            padding-bottom: 45px;
            margin-bottom: 45px;
        }
        .autor-related-posts {
            display: flex;
            align-items: center;
            flex-direction: column;
        }

    }
    @media screen and (max-width: 360px) {
        .autorbox-image img {
            margin-right: 0px;
        }
        .autorbox-image {
            text-align: center;
        }
    }
    @media screen and (max-width: 330px) {
        .social-aut-links-page a {
            padding-left: 15px;
        }
    }
</style>
<main id="main">
    <section class="section-aut-related-posts">
        <div class="container">
            <div class="row">
                <div class="main-box-wrap col-12">
                    <div class="autor-box-title-page"><h4>About author</h4> </div>
                    <div class="autor-box-page">
                        <div class="autorbox-image">
                                  <?php if (has_post_thumbnail( $post->ID) ): ?>
                                     <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
                                     <img src="<?php echo $image[0]; ?>" alt="" class="featured-article">
                                  <?php endif; ?>
                            </div>
                          <div class="autorbox-info autorbox-info-page">
                                    <div class="autor-subtitle4"><?php echo $post->post_title; ?></div>
                             <p><?php echo $post->post_content; ?></p>
                                <?php  if( have_rows('social_links_arr',$post->ID)) : ?>
                                   <div class="social-aut-links-page">
                                       <span style="font-weight:700; font-size:18px;">Follow on </span>
                                        <?php
                                        // loop through the rows of data
                                        while ( have_rows('social_links_arr',$post->ID) ) : the_row(); ?>
                                            <a class="sv-aut-follow-link" href="<?php  if(get_sub_field('social_name')['url']) { echo get_sub_field('social_name')['url'];} else { echo '#';} ?>">
                                                <?php echo get_sub_field('social_name')['title']; ?>
                                            </a>
                                        <?php endwhile; ?>
                                   </div>
                                <?php endif; ?>
                            </div>
                         </div>
                       </div>
                 </div>


                     <?php
                            foreach ($autorPosts as $ap) {
                                ?>
                <div class="autor-related-posts">
                                <div class="related-feature-img">
                                     <?php if (has_post_thumbnail( $ap->ID) ): ?>
                                        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $ap->ID ), 'single-post-thumbnail' ); ?>
                                            <img src="<?php echo $image[0]; ?>" alt="">
                                        <?php endif; ?>
                                </div>
                                <div class="autor-article">
                                    <a href="<?= get_permalink($ap->ID)?>">
                                        <?php echo $ap->post_title; ?>
                                    </a>
                                </div>
                </div>
                  <?php   }
                     ?>


            </div>
        </div>
        <!-- /.container -->
    </section>
    <!-- /.section-review-taxonomy -->
</main>
<!-- /#main -->
<?php get_footer(); ?>
