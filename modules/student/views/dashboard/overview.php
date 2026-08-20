<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$CI = &get_instance();

$CI->load->model('student/Student_model');

$total_students = $CI->Student_model->get_total_students();
$active_students = $CI->Student_model->get_active_students();
$inactive_students = $CI->Student_model->get_inactive_students();
$this_month_students = $CI->Student_model->get_this_month_students();

$students_by_course = $CI->Student_model->get_students_by_course();
$students_by_department = $CI->Student_model->get_students_by_department();
?>

    
    
<div
    id="widget-student-overview"
    class="widget"
    data-name="Student Overview">

    <style>


            .student-dashboard-overview {
    width: 100% !important;
    max-width: 100% !important;
    display: block !important;
    float: none !important;
    clear: both !important;
    margin: 0 !important;
    padding: 0 !important;
            }

/*====
   HEADER
  ==== */

            .student-dashboard-overview .student-overview-header {
    width: 100% !important;
    display: block !important;
    float: none !important;
    clear: both !important;
    margin: 0 0 20px !important;
    padding: 0 2px !important;
            }

            .student-dashboard-overview .student-overview-title {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    font-size: 21px !important;
    line-height: 30px !important;
    font-weight: 600 !important;
    color: #1f2937 !important;
            }

            .student-dashboard-overview .student-overview-title i {
                margin-right: 8px;
                color: #2563eb;
            }

            .student-dashboard-overview .student-overview-subtitle {
    display: block !important;
    width: 100% !important;
    margin: 4px 0 0 !important;
    padding: 0 !important;
    font-size: 13px !important;
    color: #9ca3af !important;
            }

            .student-dashboard-overview .student-overview-divider {
                width: 100%;
                height: 1px;
                background: #e5e7eb;
                margin-top: 16px;
            }

/*====
   STATISTICS
  ==== */

            .student-dashboard-overview .student-stats-grid {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
            }

/*====
   STAT CARD
  ==== */

            .student-dashboard-overview .student-stat-card {
                position: relative;
                min-width: 0;
    background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 20px;
                min-height: 165px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
                transition: all .2s ease;
            }

            .student-dashboard-overview .student-stat-card:hover {
                transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.09);
            }

            .student-dashboard-overview .student-stat-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .student-dashboard-overview .student-stat-label {
                margin: 0;
                font-size: 13px;
                font-weight: 500;
                color: #64748b;
            }

            .student-dashboard-overview .student-stat-number {
                margin: 8px 0 0;
                font-size: 30px;
                line-height: 1;
                font-weight: 700;
                color: #111827;
            }

/*====
   ICON
  ==== */

            .student-dashboard-overview .student-stat-icon {
                width: 48px;
                height: 48px;
                min-width: 48px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }

.student-dashboard-overview .student-stat-icon-blue {
                background: #eff6ff;
                color: #0284c7;
            }

.student-dashboard-overview .student-stat-icon-green {
                background: #ecfdf5;
                color: #059669;
            }

.student-dashboard-overview .student-stat-icon-red {
                background: #fef2f2;
                color: #dc2626;
            }

.student-dashboard-overview .student-stat-icon-yellow {
                background: #fffbeb;
                color: #d97706;
            }

/*====
   TEXT COLORS
  ==== */

.student-dashboard-overview .student-blue-text {
                color: #0284c7 !important;
            }

.student-dashboard-overview .student-green-text {
                color: #059669 !important;
            }

.student-dashboard-overview .student-red-text {
                color: #dc2626 !important;
            }

.student-dashboard-overview .student-yellow-text {
                color: #d97706 !important;
            }

/*====
   PROGRESS
  ==== */

            .student-dashboard-overview .student-stat-progress {
                margin-top: 28px;
            }

            .student-dashboard-overview .student-progress-bg {
                width: 100%;
                height: 6px;
                overflow: hidden;
                background: #f1f5f9;
                border-radius: 50px;
            }

            .student-dashboard-overview .student-progress-bar {
                height: 100%;
                border-radius: 50px;
            }

.student-dashboard-overview .student-progress-blue {
                background: #0ea5e9;
            }

.student-dashboard-overview .student-progress-green {
                background: #10b981;
            }

.student-dashboard-overview .student-progress-red {
                background: #ef4444;
            }

.student-dashboard-overview .student-progress-yellow {
                background: #f59e0b;
            }

            .student-dashboard-overview .student-progress-info {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 8px;
                font-size: 11px;
            }

.student-dashboard-overview .student-progress-label {
                color: #94a3b8;
            }

.student-dashboard-overview .student-progress-percent {
                font-weight: 600;
            }

/*====
   DISTRIBUTION AREA
  ==== */

            .student-dashboard-overview .student-distribution-grid {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
                margin-top: 18px;
            }

            .student-dashboard-overview .student-distribution-card {
                min-width: 0;
    background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            }

/*====
   DISTRIBUTION HEADER
  ==== */

            .student-dashboard-overview .student-distribution-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                padding: 16px 18px;
                border-bottom: 1px solid #f1f5f9;
            }

            .student-dashboard-overview .student-distribution-title-wrap {
                display: flex;
                align-items: center;
                min-width: 0;
            }

            .student-dashboard-overview .student-distribution-icon {
                width: 40px;
                height: 40px;
                min-width: 40px;
                border-radius: 9px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 11px;
            }

.student-dashboard-overview .student-course-icon {
                background: #eff6ff;
                color: #0284c7;
            }

.student-dashboard-overview .student-department-icon {
                background: #eef2ff;
                color: #4f46e5;
            }

            .student-dashboard-overview .student-distribution-title {
                margin: 0;
                font-size: 14px;
                line-height: 20px;
                font-weight: 600;
                color: #1e293b;
            }

            .student-dashboard-overview .student-distribution-subtitle {
                margin: 2px 0 0;
                font-size: 11px;
                color: #94a3b8;
            }

/*====
   COUNT BADGE
  ==== */

.student-dashboard-overview .student-count-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 30px;
                height: 30px;
                padding: 0 9px;
                border-radius: 50px;
                font-size: 11px;
                font-weight: 600;
            }

.student-dashboard-overview .student-course-badge {
                background: #eff6ff;
                color: #0284c7;
            }

.student-dashboard-overview .student-department-badge {
                background: #eef2ff;
                color: #4f46e5;
            }

/*====
   DISTRIBUTION BODY
  ==== */

.student-dashboard-overview .student-distribution-body {
                padding: 18px;
            }

.student-dashboard-overview .student-distribution-item {
                margin-bottom: 18px;
            }

.student-dashboard-overview .student-distribution-item:last-child {
                margin-bottom: 0;
            }

.student-dashboard-overview .student-distribution-item-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                margin-bottom: 7px;
            }

.student-dashboard-overview .student-distribution-name {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 13px;
                font-weight: 500;
                color: #334155;
            }

.student-dashboard-overview .student-distribution-total {
                flex-shrink: 0;
                font-size: 13px;
                font-weight: 600;
                color: #475569;
            }

.student-dashboard-overview .student-distribution-total small {
                color: #94a3b8;
                font-size: 11px;
                font-weight: 400;
            }

.student-dashboard-overview .student-distribution-progress {
                width: 100%;
                height: 7px;
                overflow: hidden;
                background: #f1f5f9;
                border-radius: 50px;
            }

.student-dashboard-overview .student-course-progress {
                height: 100%;
                background: #0ea5e9;
                border-radius: 50px;
                transition: width .3s ease;
            }

.student-dashboard-overview .student-department-progress {
                height: 100%;
                background: #6366f1;
                border-radius: 50px;
                transition: width .3s ease;
            }

/*====
   EMPTY STATE
  ==== */

.student-dashboard-overview .student-empty {
                padding: 35px 10px;
                text-align: center;
            }

.student-dashboard-overview .student-empty i {
                font-size: 28px;
                color: #cbd5e1;
            }

.student-dashboard-overview .student-empty p {
                margin: 10px 0 0;
                font-size: 12px;
                color: #94a3b8;
            }

/*====
   RESPONSIVE
  ==== */

            @media (max-width: 1199px) {

                .student-dashboard-overview .student-stats-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

            }

            @media (max-width: 767px) {

                .student-dashboard-overview .student-stats-grid,
                .student-dashboard-overview .student-distribution-grid {
                    grid-template-columns: 1fr;
                }

                .student-dashboard-overview .student-stat-card {
                    min-height: 155px;
                }

                .student-dashboard-overview .student-overview-title {
        font-size: 18px !important;
                }

            }

            @media (max-width: 480px) {

                .student-dashboard-overview .student-stat-card {
                    padding: 16px;
                }

    .student-dashboard-overview .student-distribution-header {
        padding: 14px;
    }

                .student-dashboard-overview .student-distribution-body {
                    padding: 14px;
                }

            }







            /* =========================================
   STUDENT WIDGET DRAG HANDLE
========================================= */

#widget-student-overview {
    position: relative !important;
    overflow: visible !important;
}

#widget-student-overview .widget-dragger {
    position: absolute !important;
    left: -18px !important;
    top: 18px !important;

    width: 10px !important;
    height: 22px !important;

    z-index: 100 !important;
    cursor: move !important;

    opacity: 0.65 !important;
}
        </style>

<div
    id="student-overview-content"
    class="panel_s dashboard-widget student-dashboard-overview"
>

    <div class="widget-dragger"></div>

    <!--
         HEADER
   = -->

        <div class="student-overview-header">

            <h4 class="student-overview-title">
                <i class="fa fa-graduation-cap"></i>
                <?= _l('student_overview'); ?>
            </h4>

            <p class="student-overview-subtitle">
                <?= _l('student_dashboard_summary'); ?>
            </p>

            <div class="student-overview-divider"></div>

        </div>


        <?php
    /*
       SAFE VALUES
   = */

    $total_students = (int) ($total_students ?? 0);
    $active_students = (int) ($active_students ?? 0);
    $inactive_students = (int) ($inactive_students ?? 0);
    $this_month_students = (int) ($this_month_students ?? 0);

        $active_percentage = $total_students > 0
            ? round(($active_students / $total_students) * 100)
            : 0;

        $inactive_percentage = $total_students > 0
            ? round(($inactive_students / $total_students) * 100)
            : 0;

        $month_percentage = $total_students > 0
            ? round(($this_month_students / $total_students) * 100)
            : 0;

    $active_percentage = min($active_percentage, 100);
        $inactive_percentage = min($inactive_percentage, 100);
    $month_percentage = min($month_percentage, 100);

    $students_by_course = $students_by_course ?? [];
        $students_by_department = $students_by_department ?? [];
        ?>


    <!--
         STATISTICS CARDS
   = -->

        <div class="student-stats-grid">


        <!-- TOTAL STUDENTS -->

            <div class="student-stat-card">

                <div class="student-stat-top">

                    <div>

                        <p class="student-stat-label">
                            <?= _l('student_total_students'); ?>
                        </p>

                        <h2 class="student-stat-number">
                            <?= $total_students; ?>
                        </h2>

                    </div>

                    <div class="student-stat-icon student-stat-icon-blue">
                        <i class="fa fa-graduation-cap"></i>
                    </div>

                </div>

                <div class="student-stat-progress">

                    <div class="student-progress-bg">

                        <div
                            class="student-progress-bar student-progress-blue"
                        style="width:100%;">
                    </div>

                    </div>

                    <div class="student-progress-info">

                        <span class="student-progress-label">
                            <?= _l('student_total'); ?>
                        </span>

                        <span class="student-progress-percent student-blue-text">
                            100%
                        </span>

                    </div>

                </div>

            </div>


        <!-- ACTIVE STUDENTS -->

            <div class="student-stat-card">

                <div class="student-stat-top">

                    <div>

                        <p class="student-stat-label">
                            <?= _l('student_active_students'); ?>
                        </p>

                        <h2 class="student-stat-number student-green-text">
                            <?= $active_students; ?>
                        </h2>

                    </div>

                    <div class="student-stat-icon student-stat-icon-green">
                        <i class="fa fa-check-circle"></i>
                    </div>

                </div>

                <div class="student-stat-progress">

                    <div class="student-progress-bg">

                        <div
                            class="student-progress-bar student-progress-green"
                        style="width:<?= $active_percentage; ?>%;">
                    </div>

                    </div>

                    <div class="student-progress-info">

                        <span class="student-progress-label">
                            <?= _l('student_active'); ?>
                        </span>

                        <span class="student-progress-percent student-green-text">
                            <?= $active_percentage; ?>%
                        </span>

                    </div>

                </div>

            </div>


        <!-- INACTIVE STUDENTS -->

            <div class="student-stat-card">

                <div class="student-stat-top">

                    <div>

                        <p class="student-stat-label">
                            <?= _l('student_inactive_students'); ?>
                        </p>

                        <h2 class="student-stat-number student-red-text">
                            <?= $inactive_students; ?>
                        </h2>

                    </div>

                    <div class="student-stat-icon student-stat-icon-red">
                        <i class="fa fa-user-times"></i>
                    </div>

                </div>

                <div class="student-stat-progress">

                    <div class="student-progress-bg">

                        <div
                            class="student-progress-bar student-progress-red"
                        style="width:<?= $inactive_percentage; ?>%;">
                    </div>

                    </div>

                    <div class="student-progress-info">

                        <span class="student-progress-label">
                            <?= _l('student_inactive'); ?>
                        </span>

                        <span class="student-progress-percent student-red-text">
                            <?= $inactive_percentage; ?>%
                        </span>

                    </div>

                </div>

            </div>


            <!-- THIS MONTH -->

            <div class="student-stat-card">

                <div class="student-stat-top">

                    <div>

                        <p class="student-stat-label">
                            <?= _l('student_this_month'); ?>
                        </p>

                        <h2 class="student-stat-number student-yellow-text">
                            <?= $this_month_students; ?>
                        </h2>

                    </div>

                    <div class="student-stat-icon student-stat-icon-yellow">
                        <i class="fa fa-calendar"></i>
                    </div>

                </div>

                <div class="student-stat-progress">

                    <div class="student-progress-bg">

                        <div
                            class="student-progress-bar student-progress-yellow"
                        style="width:<?= $month_percentage; ?>%;">
                    </div>

                    </div>

                    <div class="student-progress-info">

                        <span class="student-progress-label">
                            <?= _l('student_new_this_month'); ?>
                        </span>

                        <span class="student-progress-percent student-yellow-text">
                            <?= $month_percentage; ?>%
                        </span>

                    </div>

                </div>

            </div>

        </div>


    <!--
         COURSE + DEPARTMENT
   = -->

        <div class="student-distribution-grid">


        <!-- COURSE WISE -->

            <div class="student-distribution-card">

                <div class="student-distribution-header">

                    <div class="student-distribution-title-wrap">

                        <div class="student-distribution-icon student-course-icon">
                            <i class="fa fa-book"></i>
                        </div>

                        <div>

                            <h4 class="student-distribution-title">
                                <?= _l('student_course_wise_students'); ?>
                            </h4>

                            <p class="student-distribution-subtitle">
                                <?= _l('student_course_distribution'); ?>
                            </p>

                        </div>

                    </div>

                    <span class="student-count-badge student-course-badge">
                        <?= count($students_by_course); ?>
                    </span>

                </div>


                <div class="student-distribution-body">

                    <?php if (!empty($students_by_course)) : ?>

                        <?php foreach ($students_by_course as $course) : ?>

                            <?php

                            $course_total = (int) ($course['total'] ?? 0);

                            $course_percentage = $total_students > 0
                                ? round(($course_total / $total_students) * 100)
                                : 0;

                            $course_percentage = min($course_percentage, 100);

                            ?>

                            <div class="student-distribution-item">

                                <div class="student-distribution-item-top">

                                    <span class="student-distribution-name">
                                        <?= e($course['course_name'] ?? 'Unknown'); ?>
                                    </span>

                                    <span class="student-distribution-total">
                                        <?= $course_total; ?>

                                        <small>
                                            (<?= $course_percentage; ?>%)
                                        </small>
                                    </span>

                                </div>

                                <div class="student-distribution-progress">

                                    <div
                                        class="student-course-progress"
                                    style="width:<?= $course_percentage; ?>%;">
                                </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="student-empty">

                            <i class="fa fa-book"></i>

                            <p>
                                <?= _l('student_no_course_data'); ?>
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


        <!-- DEPARTMENT WISE -->

            <div class="student-distribution-card">

                <div class="student-distribution-header">

                    <div class="student-distribution-title-wrap">

                        <div class="student-distribution-icon student-department-icon">
                            <i class="fa fa-building"></i>
                        </div>

                        <div>

                            <h4 class="student-distribution-title">
                                <?= _l('student_department_wise_students'); ?>
                            </h4>

                            <p class="student-distribution-subtitle">
                                <?= _l('student_department_distribution'); ?>
                            </p>

                        </div>

                    </div>

                    <span class="student-count-badge student-department-badge">
                        <?= count($students_by_department); ?>
                    </span>

                </div>


                <div class="student-distribution-body">

                    <?php if (!empty($students_by_department)) : ?>

                        <?php foreach ($students_by_department as $department) : ?>

                            <?php

                            $department_total =
                                (int) ($department['total'] ?? 0);

                            $department_percentage =
                                $total_students > 0
                                    ? round(
                                        ($department_total / $total_students) * 100
                                    )
                                    : 0;

                            $department_percentage =
                                min($department_percentage, 100);

                            ?>

                            <div class="student-distribution-item">

                                <div class="student-distribution-item-top">

                                    <span class="student-distribution-name">

                                        <?= e(
                                            $department['department_name']
                                                ?? 'Unknown'
                                        ); ?>

                                    </span>

                                    <span class="student-distribution-total">

                                        <?= $department_total; ?>

                                        <small>
                                            (<?= $department_percentage; ?>%)
                                        </small>

                                    </span>

                                </div>

                                <div class="student-distribution-progress">

                                    <div
                                        class="student-department-progress"
                                    style="width:<?= $department_percentage; ?>%;">
                                </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="student-empty">

                            <i class="fa fa-building"></i>

                            <p>
                                <?= _l('student_no_department_data'); ?>
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>
</div>