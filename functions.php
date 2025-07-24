<?php
function my_custom_theme_menus(){
// <!-- *Register navigation menus uses wp_nav_menu in five places. -->
register_nav_menus( array(
	'menu-1'=>__( 'Primary Menu' ,'my-custom-theme' ),
    'menu-2'=>__('Footer Menu','my-custom-theme'),
));
}
add_action('init', 'my_custom_theme_menus');
/*
* Register the side bar (they are like widgets)
*/
function my_custom_theme_sidebar(){
	register_sidebar(
		array(
			'name' => __('Primary-sidebar' , 'my-custom-theme'),
			'id' => 'sidebar-1',

	));
}

add_action('widgets_init', 'my_custom_theme_sidebar');
/*
* Register the featured image (we need to tell the theme support featured images)
*/
add_theme_support( 'post-thumbnails' );

// add image size
add_image_size( 'my-custom-image-size', 640, 999 );


/*
* adding the custom css
*/
function my_custom_theme_enqueue(){

	wp_enqueue_style( 'custom_style', get_template_directory_uri() . '/assets/css/style.css'); 
    wp_enqueue_style( 'bootstrap',get_template_directory_uri() . '/assets/css/bootstrap.min.CSS'); 
    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array(),"",true);
  
    wp_enqueue_style('my-custom-theme-style',get_stylesheet_uri());

}
add_action('wp_enqueue_scripts','my_custom_theme_enqueue');


/*
* support for the title tag it will show the title tag
*/
add_theme_support('title-tag');

/*
* support for custom logo
*/
$logo_width  = 120;
$logo_height = 90;
add_theme_support(
		'custom-logo',
		array(
			'height'      => $logo_height,
			'width'       => $logo_width,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

/*
* How to create custom post types for product and taxanomies
*/
function create_custom_post_type() {
register_post_type('product',
    array(
        'labels' => array(
            'name' => __('Products'),
            'singular_name' => __('Product')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'products'),
        // 'label'               => __( 'movies', 'twentytwentyone' ),
        // 'description'         => __( 'Movie news and reviews', 'twentytwentyone' ),
        // 'labels'              => $labels,
        // // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'comments', 'revisions', 'custom-fields', ),
        // // You can associate this CPT with a taxonomy or custom taxonomy. 
        // 'taxonomies'          => array( 'genre'),
        // /* A hierarchical CPT is like Pages and can have
        // * Parent and child items. A non-hierarchical CPT
        // * is like Posts.
        // */
        'hierarchical'        => true,
        // 'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        // 'show_in_nav_menus'   => true,
        // 'show_in_admin_bar'   => true,
        // 'menu_position'       => 5,
        // 'can_export'          => true,
        // 'has_archive'         => true,
        // 'exclude_from_search' => false,
        // 'publicly_queryable'  => true,
        'capability_type'     => 'post',
        // 'show_in_rest' => true,

    )
);
}
add_action('init', 'create_custom_post_type');

function create_custom_taxonomy() {
register_taxonomy(
    'genre',
    'product',
    array(
        'label' => __('Genre'),
        'rewrite' => array('slug' => 'genre'),
        'hierarchical' => true,

    )
);
}
add_action('init', 'create_custom_taxonomy');
/*
* Creating a function to create our CPT for movies
*/
  
function custom_post_type() {
  
// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Movies', 'Post Type General Name', 'twentytwentyone' ),
        'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'twentytwentyone' ),
        'menu_name'           => __( 'Movies', 'twentytwentyone' ),
        'parent_item_colon'   => __( 'Parent Movie', 'twentytwentyone' ),
        'all_items'           => __( 'All Movies', 'twentytwentyone' ),
        'view_item'           => __( 'View Movie', 'twentytwentyone' ),
        'add_new_item'        => __( 'Add New Movie', 'twentytwentyone' ),
        'add_new'             => __( 'Add New', 'twentytwentyone' ),
        'edit_item'           => __( 'Edit Movie', 'twentytwentyone' ),
        'update_item'         => __( 'Update Movie', 'twentytwentyone' ),
        'search_items'        => __( 'Search Movie', 'twentytwentyone' ),
        'not_found'           => __( 'Not Found', 'twentytwentyone' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwentyone' ),
    );
      
// Set other options for Custom Post Type
      
    $args = array(
        'label'               => __( 'movies', 'twentytwentyone' ),
        'description'         => __( 'Movie news and reviews', 'twentytwentyone' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'genre'),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
  
    );
      
    // Registering your Custom Post Type
    register_post_type( 'movies', $args );
  
}
  
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
  
add_action( 'init', 'custom_post_type', 0 );
function custom_taxonomy_movies() {
register_taxonomy(
    'genre',
    'Movies',
    array(
        'label' => __('Genre'),
        'rewrite' => array('slug' => 'genre'),
        'hierarchical' => true,
    )
);
}
add_action('init', 'create_custom_taxonomy');
/*
* crearted custom post type news with taxanomy
*/
// adding new post type
function create_custom_post_type_news() {
register_post_type('news',
    array(
        'labels' => array(
            'name' => __('News'),
            'singular_name' => __('News')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'news'),
    )
);
}
add_action('init', 'create_custom_post_type_news');

function create_custom_taxonomy_news() {
register_taxonomy(
    'local',
    'news',
    array(
        'label' => __('Local'),
        'rewrite' => array('slug' => 'local'),
        'hierarchical' => true,
    )
);
register_taxonomy(
    'national',
    'news',
    array(
        'label' => __('National'),
        'description'=> ('For all National Tags'),
        'rewrite' => array('slug' => 'national'),
        'hierarchical' => false,
        'show_ui'=> true,
        'hierarchical'        => false,
        // 'public'              => true,
        // 'show_ui'             => true,
        // 'show_in_menu'        => true,
        // 'show_in_nav_menus'   => false,
        // 'show_in_admin_bar'   => false,
        // // 'menu_position'       => 5,
        // 'can_export'          => true,
        // 'has_archive'         => true,
        // 'exclude_from_search' => false,
        // 'publicly_queryable'  => true,
        // 'capability_type'     => 'post',
        // 'show_in_rest' => true,
    )
);
}
add_action('init', 'create_custom_taxonomy_news');
/*
* function to render latest post
*/
function render_my_latest_post($attr){
   

return "current time".date("Y-m-d H:i:s");

}
add_shortcode('my-latest-post',"render_my_latest_post");
/*
* my practise function 
*/
function my_theme_messages() { 
  
// Things that you want to do.
$message = 'Hello world!'; 
  
// Output needs to be return
return $message;
}
// register shortcode

add_shortcode('greeting', 'my_theme_messages');


function my_form_code()
{
    ob_start();
    ?>
    <div class="custom-form-mydata">

        <form method="post" action="">
                <h2 class="text-center">Events</h2>
          <div class="form-group">
            <label for="event-title">Event Title</label>
            <input type="text" class="form-control" id="event-title" aria-describedby="event-title" placeholder="Event Title" value="">
          </div>
          <div class="form-group">
            <label for="start-date">Start Date</label>
            <input type="date" class="form-control" id=" event-start-date" placeholder="Start Date" value="">
          </div>
         <div class="form-group">
            <label for="end-date">End Date</label>
            <input type="date" class="form-control" id=" event-end-date" placeholder="End Date" value="">
          </div>
         <div class="fetch-link">
         <a href ="" class="" title ="Fetch">Fetch</a>
     </div>
        </form>
    </div>

<?php
return ob_get_clean();

}
add_shortcode('my-form', 'my_form_code');


// Reference function for wpdp
// function save_event_form_data(){
//     if(isset($_POST['action']) && $_POST['action']=='create-event'){
//         global $wpdb;
//         $wpdb->insert("wp_event",array("event_title"=>$_POST['event-title'],"startdate"=>$_POST['start-date']));

//         $wpdb->get_results("select * from wp_event where id=1 limit 1");

//         $wpdb->update("wp_event",array("event_title"=>$_POST['event-title'],"startdate"=>$_POST['start-date']),array("id"=>"1"));
//         $wpdb->query("insert table `");
//     }
// }
// add_action("init","save_event_form_data");


/*
* Save event Data 
*/
function save_event_data()
{
  
    if(isset($_POST['action']) && $_POST['action'] == 'create-event') {
        global $wpdb;
        $wpdb->insert('wp_events',array('event_title'=>$_POST['event-title'] ,'startdate'=>$_POST['start-date'],'enddate'=>$_POST['end-date']));
    }
}
add_action('init','save_event_data');


// function show_event_data()
// {
//     if(isset($_POST['action']) && $_POST['action'] == 'show-event' )
//     {
//         global $wpdb;
//         $wpdb->update('wp_events',array('event_title'=>$_POST['event-title'],'startdate'=>$_POST['start-date'],'enddate'=>$_POST['end-date']),array('id'=>''));

//     }
// }
// add_action('init','show_event_data');

function update_event_data()
{
    if(isset($_POST['action']) && $_POST['action'] == 'update-events' )
    {
        global $wpdb;
        $wpdb->update('wp_events',array('event_title'=>$_POST['event-title'],'startdate'=>$_POST['start-date'],'enddate'=>$_POST['end-date']),array('id'=>'1'));

    }
}
add_action('init','update_event_data');
function delete_event_data()
{
    global $wpdb;
    $wpdb->delete('wp_events',array('id'=>''));

}
add_action('init','delete_event_data');
// Shortcode For Calender
function render_my_custom_calendar($attr){
    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri() ;?>/assets/css/calendar/style_trial.css">



<?php // Get the current month and year or use the provided month and year from the URL
$rawmonth = $_GET['month'] ?? date('n');
$rawyear = $_GET['year'] ?? date('Y');

// Validate that both are numeric
// Initialize error
$invalid_error = null;

// Validate numeric input
if (!is_numeric($rawmonth) || !is_numeric($rawyear)) {
    $invalid_error = "Error: Month and year must be numeric.";
    $month = date('n');
    $year = date('Y');
} else

{
// Convert to integer only after confirming they are numeric
$month = (int)$rawmonth;
$year = (int)$rawyear;

    // logic for invalid month
    if ($month < 1 || $month > 12) {
        $invalid_error = "Error: Invalid month selected. Select range 1–12.";
         // fallback to current month to prevent fatal error so that there and current dates and year remain selected
        $month = date('n');
        $year = date('Y');
    }
    //  logic for invalid year range
    elseif ($year < 2005 || $year > 2045) {
        $invalid_error = "Error: Year must be between 2005 and 2045.";
        $month = date('n');
        $year = date('Y');
    }
}

// PHP section: Prepare dates and variables
// it calculate days in month 
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//it converts english output to number
$firstDayOfMonth = strtotime("$year-$month-01");

//its tell on that 2025-2-1 was which day of the week 
$firstDayOfWeek = date('w', $firstDayOfMonth);
// Create an array of days of the week

$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// function to get just weeks structure in an array 
function getWeeks($year, $month) {
    $weeks = [];
    $currentWeek = [];

    // it calculate days in month 
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    //it converts english output to number
    $firstDayOfMonth = strtotime("$year-$month-01");

    //its tell on that 2025-2-1 was which day of the week 
    $firstDayOfWeek = date('w', $firstDayOfMonth);

    // Add previous month dates to fill first week
    if ($firstDayOfWeek > 0) {
        for ($i = $firstDayOfWeek; $i > 0; $i--) {
            $prevDate = date('Y-m-d', strtotime("$year-$month-01 -$i days"));
            $currentWeek[] = $prevDate;
        }
    }

    // Fill current month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $currentWeek[] = $date;

        if (count($currentWeek) == 7) {
            $weeks[] = $currentWeek;
            $currentWeek = [];
        }
    }

    // Fill last week with next month days if needed
    if (!empty($currentWeek)) {
        $lastDate = end($currentWeek);
        $daysToAdd = 7 - count($currentWeek);
        for ($i = 1; $i <= $daysToAdd; $i++) {
            $nextDate = date('Y-m-d', strtotime("$lastDate +$i days"));
            $currentWeek[] = $nextDate;
        }
        $weeks[] = $currentWeek;
    }

    return $weeks;
}


// Determine navigation months
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

$daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

// Logic Get all events for the selected month
$monthStart = "$year-$month-01";
$monthEnd = date("Y-m-t", strtotime($monthStart)); 

global $wpdb;
$results=$wpdb->get_results("SELECT * from wp_events WHERE startdate BETWEEN '$monthStart' AND '$monthEnd'", ARRAY_A);
// print_r($results);


$eventsByDate = [];
if ($results) {
    foreach($results as $row) {
        // print_r($row);

        $start = new DateTime($row['startdate']);
        $end = isset($row['enddate']) && $row['enddate'] ? new DateTime($row['enddate']) : clone $start;

        // Loop over all dates the event spans
        for ($date = clone $start; $date <= $end; $date->modify('+1 day')) {
            $dateStr = $date->format('Y-m-d');
            if (!isset($eventsByDate[$dateStr])) {
                $eventsByDate[$dateStr] = [];
            }
            $eventsByDate[$dateStr][$row['id']] = $row;
        }

}
}

;?>

    <section class="cal-section">
        <div class="container">
            <div class="cal-outer">
                <h1 class="text-center calendar-full-name-wrap">Calendar for <?= date('F Y', $firstDayOfMonth) ?></h1>

                    <?php if ($invalid_error): ?>
                        <div class="error-message text-danger text-center my-2 fw-bold">
                            <?= htmlspecialchars($invalid_error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- to show the name of month and year -->
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col-5 month-name-wrap">
                            <h4><?= date('F Y', $firstDayOfMonth) ?></h4>
                        </div>


                   <!-- Form date selection auto like year and month by scrolling-->
                        <form method="GET" class=" col-5 d-flex gap-2 align-items-center"> 
                            <!-- month selection dropdown-->
                            <select name="c_month" id="c_month"class="form-select" style="width: auto;">
                                <!--loop for month-->
                                <option value=""> select month </option>
                                <?php

                                    for($m=1; $m<=12 ;$m++)
                                    {
                                        // we will check if selected month is equal to the loop in current month 
                                        // $selected = ($m == $month) ? "selected" : "";
                                        echo "<option value='$m'>" . date('M', mktime(0, 0, 0, $m, 10)) . "</option>";
                                        }
                                ?>
                            </select>
                            <select name="c_year" class="form-select" id="c_year" style="width: auto;" size="1">
                                <option value=""> select Year </option>
                                <?php
                                $currentYear = date('Y');
                                // we are making dropdown of year 20 month before and after
                                for ($y = $currentYear - 20; $y <= $currentYear + 20; $y++) {
                                   // $selected = ($y == $year) ? "selected" : "";
                                    echo "<option value='$y'>$y</option>";
                                }
                                ?>
                            </select>

                            <button type="submit" class="btn btn-primary btn-sm calender-btn-select">select</button>

                       </form>

                        <!-- it will put into the query previous month and year -->
                       <div class="col-2 text-end d-flex justify-content-end gap-2">
                            <a  class="previous-month-btn" href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">
                                <i class="bi bi-chevron-up" style="font-size: 40px; color: black;"></i>
                            </a>
                            <a class="next-month-btn" href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">
                                <i class="bi bi-chevron-down" style="font-size: 40px; color: black;"></i>
                            </a>
                        </div>

                    </div>
                    <div class="calender-wrap">
                        <table class="table table-bordered w-100 table-fixed head-table">
                                <thead>
                                    <tr>
                                        <!-- first show the days of week with loop -->
                                         <?php foreach ($daysOfWeek as $day): ?>
                                         <th style="background-color: antiquewhite;"><?= htmlspecialchars($day) ?></th>
                                         <?php endforeach; ?>
                                    </tr>
                                </thead>
                        </table>

                        <!-- calender body seperate table-->
                        <!-- Weeks container -->
                        <div class="calendar-container">

                        <!-- here we are actualy using the function -->
                          <?php 
                              $weeks = getWeeks($year, $month);
                              foreach ($weeks as $weekNumber => $weekDays): ?>

                                 <div class="calendar-row" id="week-<?= $weekNumber ?>" style="position: relative;">
                                      <!-- Week days table -->
                                      <table class="calendar-table table table-bordered table-fixed w-100 body-table">
                                        <tbody>
                                          <tr>
                                             <?php foreach ($weekDays as $fullDate):
                                              $day = date('j', strtotime($fullDate));
                                              $isToday = ($fullDate === date('Y-m-d'));
                                              $dayOfWeek = date('w', strtotime($fullDate));
                                              $style = "";
                                              $isCurrentMonth = (int)date('n', strtotime($fullDate)) === $month;
                                              if (!$isCurrentMonth) {
                                              $style .= "color: #ccc;";
                                              }
                                              elseif($dayOfWeek == 0 || $dayOfWeek == 6) {
                                                  $style = $isToday ? "background-color: #ffeb3b; color: #000;" : "background-color: #f2f2f2; color: #ff0000;";
                                              }
                                              $class = $isToday ? "today" : "";
                                            ?>

                                            <td id="cell-<?= $fullDate ?>"data-date="<?= $fullDate ?>" style="<?= $style ?>" class="<?= $class ?>" >
                                                <!-- it will show the event day and event-number -->
                                            <div class="day-number"><?= $day ?></div>

                                             </td>
                                            <?php endforeach; ?>
                                          </tr>
                                        </tbody>
                                      </table>

                                       <!-- Event overlay container for multi-day events -->
                                      <div class="event-overlay-container" id="overlay-week-<?= $weekNumber ?>" style="position: absolute; top: 30px; left: 0; width: 100%; pointer-events: none;">

                                       <?php

                                        // Step 1 : flatten all unique event
                                        $allEventsFlat = [];
                                        foreach ($eventsByDate as $events) {
                                            foreach ($events as $eventId => $event) {
                                                $allEventsFlat[$eventId] = $event; // overrides duplicates
                                            }
                                        }


                                        // $seenEventIds = []; // Prevent duplicate render

                                        $lanes = []; // NEW: lanes array to track occupied time slots
                                        foreach ($allEventsFlat as $eventId => $event) {
                                            // foreach ($events as $eventId => $event) {

                                                echo "<!-- DEBUG: Event ID: $eventId, Title: {$event['event_title']}, Start: {$event['startdate']}, End: {$event['enddate']} -->";
                                                // if (in_array($eventId, $seenEventIds)) continue;
                                                // $seenEventIds[] = $eventId;

                                                $startDate = $event['startdate'];
                                                $endDate = $event['enddate'] ?? $startDate;

                                                $startTimestamp = strtotime(date('Y-m-d', strtotime($startDate)));
                                                $endTimestamp = strtotime(date('Y-m-d', strtotime($endDate)));
                                                $weekStart = strtotime(date('Y-m-d', strtotime($weekDays[0])));
                                                $weekEnd = strtotime(date('Y-m-d', strtotime($weekDays[6])));

                                                if ($endTimestamp < $weekStart || $startTimestamp > $weekEnd) {
                                                    continue;
                                                }

                                                $eventStripStart = max($startTimestamp, $weekStart);
                                                $eventStripEnd = min($endTimestamp, $weekEnd);

                                                $daysFromWeekStart = ($eventStripStart - $weekStart) / 86400;
                                                $eventDurationDays = (($eventStripEnd - $eventStripStart) / 86400) + 1;

                                                $cellWidth = 185.5;
                                                $stripWidth = $eventDurationDays * $cellWidth;
                                                $leftPos = $daysFromWeekStart * $cellWidth;

                                                 //  logic to calculate top offset so that events should not overlap 
                                                $laneIndex = 0;
                                                while (true) {
                                                    if (!isset($lanes[$laneIndex])) {
                                                        $lanes[$laneIndex] = [];
                                                        break;
                                                    }

                                                    $conflict = false;
                                                    foreach ($lanes[$laneIndex] as [$existingStart, $existingEnd]) {
                                                        if (!($eventStripEnd < $existingStart || $eventStripStart > $existingEnd)) {
                                                            $conflict = true;
                                                            break;
                                                        }
                                                    }

                                                    if (!$conflict) break;
                                                    $laneIndex++;
                                                }

                                                $lanes[$laneIndex][] = [$eventStripStart, $eventStripEnd];
                                                $topOffset = $laneIndex * 28; // vertical space between events

                                                $fullDuration = (strtotime($endDate)-strtotime($startDate)) /86400 +1;
                                                // Debug info
                                                echo "<!-- event: {$event['event_title']} | start={$startDate}, end={$endDate}, left={$leftPos}, width={$stripWidth}, days={$fullDuration}, top={$topOffset} -->";

                                                // we are calcuting full duration of event not the strip width 
                                                echo "<div class='event-strip' id='event-{$eventId}'
                                               draggable='true'
                                                    data-duration='{$fullDuration}'
                                                 style='position: absolute; top: {$topOffset}px; left: {$leftPos}px; width: {$stripWidth}px;' title='" . htmlspecialchars($event['event_title']) . "'>";
                                                echo "<span class='event-text'  id='title-{$eventId}'>" . htmlspecialchars($event['event_title']) . "</span>";
                                                echo "<span class='event-actions'>";
                                                echo "<button class='edit-btn' onclick='event.stopPropagation(); promptEditEvent({$eventId})'><i class='fa fa-pencil'></i></button>";
                                                echo "<button class='dlt-btn' onclick='event.stopPropagation(); deleteEvent({$eventId})'><i class='fa fa-remove'></i></button>";
                                                echo "</span></div>";
                                            }

                                        ?>
                                        

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>    

        </div>
        </div>  
    <section>

 <?php
   wp_enqueue_script('mycustom-script',get_template_directory_uri().'/assets/js/mycustom-script.js', array(),"",true) ;
    wp_localize_script("mycustom-script","cal_vars",["ajax_url"=>admin_url("admin-ajax.php"),"c_month"=>$month,"c_year"=>$year]);

    return ob_get_clean();
}
add_shortcode('my-calendar',"render_my_custom_calendar");



function render_calendar_with_ajax(){
    $c_month=$_POST['c_month'];
    $c_year=$_POST['c_year'];
    $action=$_POST['method'];
    if($action=="prev"){
        // Determine navigation months
        $month = $c_month == 1 ? 12 : $c_month - 1;
        $year = $c_month == 1 ? $c_year - 1 : $c_year;
        
    }
    if($action=="next"){
        $month = $c_month == 12 ? 1 : $c_month + 1;
        $year = $c_month == 12 ? $c_year + 1 : $c_year;
        
    }
    if($action=="filter"){
        $month = $c_month ;
        $year = $c_year;
        
    }


// PHP section: Prepare dates and variables
// it calculate days in month 
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//it converts english output to number
$firstDayOfMonth = strtotime("$year-$month-01");

//its tell on that 2025-2-1 was which day of the week 
$firstDayOfWeek = date('w', $firstDayOfMonth);
// Create an array of days of the week

$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// function to get just weeks structure in an array 
function getWeeks($year, $month) {
    $weeks = [];
    $currentWeek = [];

    // it calculate days in month 
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    //it converts english output to number
    $firstDayOfMonth = strtotime("$year-$month-01");

    //its tell on that 2025-2-1 was which day of the week 
    $firstDayOfWeek = date('w', $firstDayOfMonth);

    // Add previous month dates to fill first week
    if ($firstDayOfWeek > 0) {
        for ($i = $firstDayOfWeek; $i > 0; $i--) {
            $prevDate = date('Y-m-d', strtotime("$year-$month-01 -$i days"));
            $currentWeek[] = $prevDate;
        }
    }

    // Fill current month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $currentWeek[] = $date;

        if (count($currentWeek) == 7) {
            $weeks[] = $currentWeek;
            $currentWeek = [];
        }
    }

    // Fill last week with next month days if needed
    if (!empty($currentWeek)) {
        $lastDate = end($currentWeek);
        $daysToAdd = 7 - count($currentWeek);
        for ($i = 1; $i <= $daysToAdd; $i++) {
            $nextDate = date('Y-m-d', strtotime("$lastDate +$i days"));
            $currentWeek[] = $nextDate;
        }
        $weeks[] = $currentWeek;
    }

    return $weeks;
}


// Determine navigation months
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

$daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

// Logic Get all events for the selected month
$monthStart = "$year-$month-01";
$monthEnd = date("Y-m-t", strtotime($monthStart)); 

global $wpdb;
$results=$wpdb->get_results("SELECT * from wp_events WHERE startdate BETWEEN '$monthStart' AND '$monthEnd'", ARRAY_A);
// print_r($results);


$eventsByDate = [];
if ($results) {
    foreach($results as $row) {
        // print_r($row);

        $start = new DateTime($row['startdate']);
        $end = isset($row['enddate']) && $row['enddate'] ? new DateTime($row['enddate']) : clone $start;

        // Loop over all dates the event spans
        for ($date = clone $start; $date <= $end; $date->modify('+1 day')) {
            $dateStr = $date->format('Y-m-d');
            if (!isset($eventsByDate[$dateStr])) {
                $eventsByDate[$dateStr] = [];
            }
            $eventsByDate[$dateStr][$row['id']] = $row;
        }

}
}
ob_start();
?>
<table class="table table-bordered w-100 table-fixed head-table">
                            <thead>
                                <tr>
                                    <!-- first show the days of week with loop -->
                                     <?php foreach ($daysOfWeek as $day): ?>
                                     <th style="background-color: antiquewhite;"><?= htmlspecialchars($day) ?></th>
                                     <?php endforeach; ?>
                                </tr>
                            </thead>
                    </table>

                    <!-- calender body seperate table-->
                    <!-- Weeks container -->
                    <div class="calendar-container">

                    <!-- here we are actualy using the function -->
                      <?php 
                      $weeks = getWeeks($year, $month);
                      foreach ($weeks as $weekNumber => $weekDays): ?>

                     <div class="calendar-row" id="week-<?= $weekNumber ?>" style="position: relative;">
                          <!-- Week days table -->
                          <table class="calendar-table table table-bordered table-fixed w-100 body-table">
                            <tbody>
                              <tr>
                                 <?php foreach ($weekDays as $fullDate):
                                  $day = date('j', strtotime($fullDate));
                                  $isToday = ($fullDate === date('Y-m-d'));
                                  $dayOfWeek = date('w', strtotime($fullDate));
                                  $style = "";
                                  $isCurrentMonth = (int)date('n', strtotime($fullDate)) === $month;
                                  if (!$isCurrentMonth) {
                                  $style .= "color: #ccc;";
                                  }
                                  elseif($dayOfWeek == 0 || $dayOfWeek == 6) {
                                      $style = $isToday ? "background-color: #ffeb3b; color: #000;" : "background-color: #f2f2f2; color: #ff0000;";
                                  }
                                  $class = $isToday ? "today" : "";
                                ?>

                                <td id="cell-<?= $fullDate ?>"data-date="<?= $fullDate ?>" style="<?= $style ?>" class="<?= $class ?>" >
                                    <!-- it will show the event day and event-number -->
                                <div class="day-number"><?= $day ?></div>

                                 </td>
                                <?php endforeach; ?>
                              </tr>
                            </tbody>
                          </table>

                           <!-- Event overlay container for multi-day events -->
                          <div class="event-overlay-container" id="overlay-week-<?= $weekNumber ?>" style="position: absolute; top: 30px; left: 0; width: 100%; pointer-events: none;">

                           <?php

                            // Step 1 : flatten all unique event
                            $allEventsFlat = [];
                            foreach ($eventsByDate as $events) {
                                foreach ($events as $eventId => $event) {
                                    $allEventsFlat[$eventId] = $event; // overrides duplicates
                                }
                            }


                            // $seenEventIds = []; // Prevent duplicate render

                            $lanes = []; // NEW: lanes array to track occupied time slots
                            foreach ($allEventsFlat as $eventId => $event) {
                                // foreach ($events as $eventId => $event) {

                                    echo "<!-- DEBUG: Event ID: $eventId, Title: {$event['event_title']}, Start: {$event['startdate']}, End: {$event['enddate']} -->";
                                    // if (in_array($eventId, $seenEventIds)) continue;
                                    // $seenEventIds[] = $eventId;

                                    $startDate = $event['startdate'];
                                    $endDate = $event['enddate'] ?? $startDate;

                                    $startTimestamp = strtotime(date('Y-m-d', strtotime($startDate)));
                                    $endTimestamp = strtotime(date('Y-m-d', strtotime($endDate)));
                                    $weekStart = strtotime(date('Y-m-d', strtotime($weekDays[0])));
                                    $weekEnd = strtotime(date('Y-m-d', strtotime($weekDays[6])));

                                    if ($endTimestamp < $weekStart || $startTimestamp > $weekEnd) {
                                        continue;
                                    }

                                    $eventStripStart = max($startTimestamp, $weekStart);
                                    $eventStripEnd = min($endTimestamp, $weekEnd);

                                    $daysFromWeekStart = ($eventStripStart - $weekStart) / 86400;
                                    $eventDurationDays = (($eventStripEnd - $eventStripStart) / 86400) + 1;

                                    $cellWidth = 185.5;
                                    $stripWidth = $eventDurationDays * $cellWidth;
                                    $leftPos = $daysFromWeekStart * $cellWidth;

                                     //  logic to calculate top offset so that events should not overlap 
                                    $laneIndex = 0;
                                    while (true) {
                                        if (!isset($lanes[$laneIndex])) {
                                            $lanes[$laneIndex] = [];
                                            break;
                                        }

                                        $conflict = false;
                                        foreach ($lanes[$laneIndex] as [$existingStart, $existingEnd]) {
                                            if (!($eventStripEnd < $existingStart || $eventStripStart > $existingEnd)) {
                                                $conflict = true;
                                                break;
                                            }
                                        }

                                        if (!$conflict) break;
                                        $laneIndex++;
                                    }

                                    $lanes[$laneIndex][] = [$eventStripStart, $eventStripEnd];
                                    $topOffset = $laneIndex * 28; // vertical space between events

                                    $fullDuration = (strtotime($endDate)-strtotime($startDate)) /86400 +1;
                                    // Debug info
                                    echo "<!-- event: {$event['event_title']} | start={$startDate}, end={$endDate}, left={$leftPos}, width={$stripWidth}, days={$fullDuration}, top={$topOffset} -->";

                                    // we are calcuting full duration of event not the strip width 
                                    echo "<div class='event-strip' id='event-{$eventId}'
                                   draggable='true'
                                        data-duration='{$fullDuration}'
                                     style='position: absolute; top: {$topOffset}px; left: {$leftPos}px; width: {$stripWidth}px;' title='" . htmlspecialchars($event['event_title']) . "'>";
                                    echo "<span class='event-text'  id='title-{$eventId}'>" . htmlspecialchars($event['event_title']) . "</span>";
                                    echo "<span class='event-actions'>";
                                    echo "<button class='edit-btn' onclick='event.stopPropagation(); promptEditEvent({$eventId})'><i class='fa fa-pencil'></i></button>";
                                    echo "<button class='dlt-btn' onclick='event.stopPropagation(); deleteEvent({$eventId})'><i class='fa fa-remove'></i></button>";
                                    echo "</span></div>";
                                }

                            ?>
                            

                        </div>
                    </div>
                     <?php endforeach;
                      

        wp_send_json(["success"=>true,"html"=>ob_get_clean(),"month"=>$month,"year"=>$year,"message"=>"done","name"=>date('F Y', $firstDayOfMonth),"mon"=>$m ,"yea"=>$y]);             


}

add_action("wp_ajax_calender__render","render_calendar_with_ajax");
add_action("wp_ajax_nopriv_calender__render","render_calendar_with_ajax");

function render_edit_event(){
 
     // if user is logged in we will now get the event id 
    $eventId=$_POST['event_id'] ?? '';
    $eventId=(int)$eventId;    //to make sure the event id is int
    $newTitle = ($_POST['new_title'] ?? '');

    if (!$eventId || !$newTitle) {
       
        wp_send_json(['success' => false, 'message' => 'Missing data']);
        //echo "Missing data ID or new Title.";
        exit;
    }

    // to protect the id 

    global $wpdb;
    $updateQuery= $wpdb->query( "UPDATE wp_events SET event_title = '$newTitle', editdate = NOW() 
                 WHERE id = $eventId");

       
    if ($updateQuery !== false) {
         wp_send_json(['success' => true, 'message' => 'Event updated']);
    } else {
        wp_send_json(['success' => false, 'message' => 'Database error: ']);
    }

}
add_action("wp_ajax_edit_event","render_edit_event");
add_action("wp_ajax_nopriv_edit_event","render_edit_event");

function render_add_event(){

$title = ($_POST['event_title'] ?? '');
$start = ($_POST['start_date'] ?? '');
$end = ($_POST['end_date'] ?? '');

if (!$title || !$start || !$end) {
    wp_send_json(['success' => false, 'message' => 'Missing data']);
    exit;
}

global $wpdb;
$sql = $wpdb->query("INSERT INTO wp_events (event_title, startdate, enddate, adddate )
        VALUES ('$title', '$start', '$end',NOW())");

if ($wpdb->insert_id > 0) {
    wp_send_json([
        'success' => true,
        'event_title' => $title,
        'start_date' => $start,
        'end_date' => $end,
        "event_id"=>$wpdb->insert_id
    ]);
} else {
   wp_send_json(['success' => false, 'message' =>'cannot add']);
}
}

add_action("wp_ajax_add_event","render_add_event");
add_action("wp_ajax_nopriv_add_event","render_add_event");

function cal_delete_events(){

// if user is logged in we will now get the event id 
$eventId=$_POST['event_id'] ?? '';
$eventId=(int)$eventId;    //to make sure the event id is int

// now check if event id exist or not
if(!$eventId)
{  
 wp_send_json(['success' => false, 'message' => 'Missing data']);
    //echo "invalid data or missing ID";
    exit;
}

global $wpdb;

//delete event only if it belongs to logged in user
$deleteEventQuery=$wpdb->query("DELETE FROM wp_events WHERE id=$eventId");

if ($deleteEventQuery !== false) {
   wp_send_json(['success' => true, 'message' => 'Event deleted']);
} else {
   
    wp_send_json(['success' => false, 'message' => 'Database error']);
 }
}
add_action("wp_ajax_delete_event","cal_delete_events");
add_action("wp_Ajax_nopriv_delete_event","cal_delete_events");

function cal_move_events(){

$eventId = (int)($_POST['event_id'] ?? 0);
$newStart = $_POST['new_start'] ?? '';
$newEnd = $_POST['new_end'] ?? '';

if (!$eventId || !$newStart || !$newEnd)
{
    wp_send_json(['success' => false ,'message' => 'MIssing input']);
    exit;
}

global $wpdb;

$move_query =$wpdb->query( "UPDATE wp_events SET startdate='$newStart' ,enddate='$newEnd' ,editdate= NOW()
    WHERE id=$eventId ");

if ($move_query !== false) {
   wp_send_json(['success' => true , 'message' => 'added on new date success']);
    
} else {
   wp_send_json(['success' => false, 'message' => 'Unable to add']);
}
}

add_action("wp_ajax_move_event","cal_move_events");
add_action("wp_ajax_nopriv_move_event",'cal_move_events');

?>