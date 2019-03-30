<?php
namespace IYC;

class WPCron
{
    public function __construct()
    {
        add_filter('cron_schedules', [$this, 'wpcron_intervals']);
        add_action('yacht_feed', [$this, 'wpcron_yacht_feed']);

        if (!wp_next_scheduled('yacht_feed')) {
            wp_schedule_event(time(), 'six_hours', 'yacht_feed');
        }
    }

    public function wpcron_intervals($schedules) 
    {
        $one_minute = array(
            'interval' => 60,
            'display' => 'One Minute'
        );
        $schedules['one_minute'] = $one_minute;

        $five_minutes = array(
            'interval' => 300,
            'display' => 'Five Minutes'
        );
        $schedules['five_minutes'] = $five_minutes;

        $six_hours = array(
            'interval' => 21600,
            'display' => 'Six Hours'
        );
        $schedules['six_hours'] = $six_hours;

        return $schedules;

    }

    public function wpcron_yacht_feed() 
    {
        check_for_xml_updates();
    }
}