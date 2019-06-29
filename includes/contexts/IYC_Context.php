<?php
namespace IYC\contexts;

class IYC_Context 
{
    public function destination_page_url($yacht_key) 
    {
        $this->destination_post_id = get_post_id_from_dest_key($yacht_key);
        return get_post_permalink($this->destination_post_id);
    }

    public function destination_page_thumbnail() 
    {
        $thumbnail = get_the_post_thumbnail_url($this->destination_post_id);
        return ($thumbnail == '') ? danzerpress_no_image() : $thumbnail;
    }

    public function destination_page_title() 
    {
        return get_the_title($this->destination_post_id);
    }
}