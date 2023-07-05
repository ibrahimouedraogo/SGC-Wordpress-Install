<?php
/**
 * Dynamically generate a stylesheet with custom colors
 * @var [type]
 */

$absolute_path  = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load        = $absolute_path[0] . 'wp-load.php';

require_once( $wp_load );

header( "Content-type: text/css; charset: UTF-8" );
header( 'Cache-control: must-revalidate' ); ?>

/* Custom styling dynamically generated by WordPress */
<?php if( psp_get_option('psp_progress_color') ): ?>
     #psp-projects .psp-task-list li em.status,
     #psp-projects .psp-enhanced-milestones .psp-milestone-dots .psp-milestone-dot.completed,
     #psp-projects .psp-enhanced-milestones .psp-milestone-dots .psp-milestone-dot.completed .psp-milestone-dot-count,
     #psp-projects .psp-milestones .completed span,
     #psp-projects .psp-task-list .task-item em.status,
     body:not(.archive-psp_projects) #psp-archive-content table.psp-my-tasks .psp-task-table-status em.status,
     #psp-archive-content .sub-task-item span.psp-sub-progress-bar em.status,
     #psp-projects .sub-task-item span.psp-sub-progress-bar em.status,
     #psp-projects .psp-progress span, #psp-projects .psp-progress span {
          background: <?php echo psp_get_option('psp_progress_color'); ?> !important;
     }
<?php endif; ?>


<?php if( psp_get_option('psp_accent_color_1') ): ?>

    body:not(.archive-psp_projects) #psp-archive-content table.psp-my-tasks th,
    #psp-projects .psp-projects-widget,
    #psp-projects #psp-phases .psp-phase.color-blue .psp-phase-title-wrap,
    #psp-projects #psp-phases .psp-phase .psp-phase-title-wrap {
        background: <?php echo psp_get_option('psp_accent_color_1'); ?> !important;
        color: <?php echo psp_get_option('psp_accent_color_1_txt'); ?> !important;
    }

    body:not(.archive-psp_projects) #psp-archive-content table.psp-my-tasks th,
    #psp-projects .psp-projects-widget,
    #psp-projects .psp-phase .psp-phase-title-wrap,
    #psp-projects #psp-phases .psp-phase.color-blue .psp-phase-title-wrap,
    #psp-projects .psp-new-project-btn::before,
    #psp-projects .psp-accent-color-1 {
        color: <?php echo psp_get_option('psp_accent_color_1_txt'); ?> !important;
    }
<?php endif; ?>


<?php if( psp_get_option('psp_accent_color_2') ): ?>
    #psp-projects #psp-phases .psp-phase.color-teal .psp-phase-title-wrap,
    #psp-projects .psp-phase.color-teal .psp-phase-title-wrap,
    #psp-projects .color-teal .psp-task-list li em.status,
    #psp-projects .psp-table-header,
    #psp-projects .psp-box.psp-with-accent .psp-box-title,
    #psp-projects .psp-section-nav a.active::after,
    #psp-projects #psp-login h2,
    #psp-projects .psp-table th {
        background: <?php echo psp_get_option('psp_accent_color_2'); ?> !important;
        color: <?php echo psp_get_option('psp_accent_color_2_txt'); ?> !important;
    }

    #psp-projects .psp-projects-overview .psp-dw-types {
        color: <?php echo psp_get_option('psp_accent_color_2'); ?> !important;
    }

    #psp-projects .psp-task-project-wrapper .psp-task-breakdown strong {
        color: <?php echo psp_get_option('psp_accent_color_2'); ?> !important;
    }

    #psp-projects .psp-ali-header  {
        box-shadow: inset 8px 0 <?php echo psp_get_option('psp_accent_color_2'); ?> !important;
    }
<?php endif; ?>

<?php if( psp_get_option('psp_accent_color_3') ): ?>
    #psp-projects #psp-phases .psp-phase.color-green .psp-phase-title-wrap,
    #psp-projects .psp-phase.color-green .psp-phase-title-wrap,
    #psp-projects .color-green .psp-task-list li em.status,
    #psp-projects #psp-login #wp-submit {
        background: <?php echo psp_get_option('psp_accent_color_3'); ?> !important;
        color: <?php echo psp_get_option('psp_accent_color_3_txt'); ?> !important;
    }
<?php endif; ?>

<?php if( psp_get_option('psp_accent_color_4') ): ?>
    #psp-projects #psp-phases .psp-phase.color-pink .psp-phase-title-wrap,
    #psp-projects .psp-phase.color-pink .psp-phase-title-wrap,
    #psp-projects .color-pink .psp-task-list li em.status {
        background: <?php echo psp_get_option('psp_accent_color_4'); ?> !important;
        color: <?php echo psp_get_option('psp_accent_color_4_txt'); ?> !important;
    }
<?php endif; ?>

<?php if( psp_get_option('psp_accent_color_5') ): ?>
    #psp-projects #psp-phases .psp-phase.color-maroon .psp-phase-title-wrap,
    #psp-projects .psp-phase.color-maroon .psp-phase-title-wrap,
    #psp-projects .color-maroon .psp-task-list li em.status {
        background: <?php echo psp_get_option('psp_accent_color_5'); ?> !important;
        color: <?php echo psp_get_option('psp_accent_color_5_txt'); ?> !important;
    }
<?php endif; ?>

<?php if(psp_get_option('psp_timeline_color')): ?>
	#psp-projects .psp-time-bar span,
	#psp-projects ol.psp-time-ticks li.active,
	#psp-projects .psp-date .cal .month {
		background: <?php echo psp_get_option('psp_timeline_color'); ?>
	}

	#psp-projects .psp-time-indicator span {
		border-bottom-color: <?php echo psp_get_option('psp_timeline_color'); ?>
	}
<?php endif; ?>


#psp-projects #psp-main-nav ul li#nav-menu:hover,
#psp-projects #psp-main-nav #nav-menu ul,
#psp-projects #psp-offcanvas-menu {
    background: <?php echo psp_get_option( 'psp_menu_background' ); ?>;
}

#psp-projects li#nav-menu.active {
    background: rgba(0,0,0.5) !important;
}

#psp-projects #psp-main-nav #nav-menu ul li a,
#psp-offcanvas-menu,
#psp-offcanvas-menu a,
#psp-offcanvas-menu a:hover {
    color: <?php echo psp_get_option( 'psp_menu_text' ); ?> !important;
}

#psp-projects #psp-title span {
    color: <?php echo psp_get_option( 'psp_header_accent' ); ?>;
}

<?php if( $body_background = psp_get_option('psp_body_background') ): ?>
    body.psp-standalone-page,
    #psp-projects,
    #psp-projects .psp-standard-template {
        background-color: <?php echo $body_background; ?>;
    }
    #psp-projects .psp-enhanced-milestones ul.psp-milestone-dots li strong.psp-single-milestone span.psp-marker-title {
        background: <?php echo $body_background; ?>;
        box-shadow: 0 0 20px 10px <?php echo $body_background; ?>;
    }
    <?php
    if( $body_background = '#ffffff' ): ?>
          #psp-projects .psp-tb-progress, #psp-projects .psp-progress, #psp-projects .psp-progress {
               background: #efefef;
          }
    <?php
    endif;
endif; ?>

#psp-projects.psp-part-project,
#psp-projects.psp-single-project {
	background: transparent;
}

#psp-projects #project-documents,
#psp-projects #psp-essentials div .psp-h4,
#psp-projects .psp-timing li,
#psp-projects .psp-time-start-end,
#psp-projects .psp-time-indicator {
    color: <?php echo psp_get_option('psp_body_text'); ?>;
}

#psp-projects #project-documents ul li a {
    color: <?php echo psp_get_option('psp_body_link'); ?>;
}

<?php
/*
 * #999999 is the default color, so skip if it's #999999
 */
if( psp_get_option('psp_body_heading') && psp_get_option('psp_body_heading') != '#999999' ): ?>
    #psp-projects #project-documents .psp-h3,
    #psp-projects .psp-section-heading .psp-h2.psp-section-title,
    #psp-projects .psp-section-heading .psp-h1.psp-section-title,
    #psp-projects .psp-section-heading .psp-section-data,
    #psp-projects #psp-project-calendar .psp-h2,
    #psp-projects .psp-projects-overview strong,
    #psp-projects .psp-team-list li a em,
    #psp-colophon p,
    #psp-projects .psp-projects-overview span,
    #psp-projects #psp-essentials .psp-h4,
    #psp-projects #psp-project-summary .psp-h5,
    #psp-projects #psp-time-overview .psp-archive-list-dates .psp-h5,
    #psp-projects #psp-time-overview .psp-archive-list-dates p {
        color: <?php echo psp_get_option('psp_body_heading'); ?>;
    }
    .psp-section-heading span.is-active svg,
    .psp-section-heading .psp-svg-color-primary svg {
         color: <?php echo psp_get_option('psp_body_heading'); ?>;
         fill: <?php echo psp_get_option('psp_body_heading'); ?>;
    }
<?php endif; ?>

#psp-projects .psp-fe-wizard__nav,
#psp-projects #psp-discussion,
#psp-projects #psp-comments {
    background-color: <?php echo psp_get_option('psp_footer_background'); ?> !important;
}

<?php echo psp_get_option('psp_open_css'); ?>
