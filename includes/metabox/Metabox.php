<?php
namespace IYC\metabox;

/**
 * Register a meta box using a class.
 */
class Metabox 
{
    protected $id;
    protected $title;
    protected $screen;
    protected $display;
    protected $priority;
    protected $callback_args;
    protected $context;
 
    /**
     * Constructor.
     */
    public function __construct($args, $context = []) 
    {   
        if ( is_admin() ) {
            $defaults = [
                'id' => 'test',
                'title' => 'Test Title',
                'screen' => null,
                'display' => 'advanced',
                'priority' => 'default',
                'callback_args' => null
            ];
    
            $args = wp_parse_args($args, $defaults);
            
            $this->id            = $args['id'];
            $this->title         = $args['title'];
            $this->screen        = $args['screen'];
            $this->display       = $args['display'];
            $this->priority      = $args['priority'];
            $this->callback_args = $args['callback_args'];
            $this->context       = $context;

            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
    }
 
    /**
     * Meta box initialization.
     */
    public function init_metabox() 
    {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }
 
    /**
     * Adds the meta box.
     * @param id
     * @param title
     * @param callback
     * @param screen
     * @param display
     * @param priority
     * @param callback_args
     */
    public function add_metabox() 
    {
        // 'my-meta-box',
        // __( 'My Meta Box', 'textdomain' ),
        // array( $this, 'render_metabox' ),
        // 'post',
        // 'advanced',
        // 'default'
        add_meta_box(
            $this->id,
            $this->title,
            [$this, 'render_metabox'],
            $this->screen,
            $this->display,
            $this->priority,
            $this->callback_args
        );
 
    }
 
    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) 
    {
        // Add nonce for security and authentication.
        $this->context = $this->context + [
            'meta_id' => $this->id,
            'nonce_field' => wp_nonce_field('custom_nonce_action', "custom_nonce"),
            'metabox_data' => get_post_meta($post->ID, "dp_metabox_{$this->id}", true)
        ];

        render("metabox/{$this->id}.twig", $this->context);
    }
 
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) 
    {
        // Add nonce for security and authentication.
        $data = apply_filters("sanatize_metabox_{$this->id}", isset($_POST[$this->id]) ? $_POST[$this->id] : '', $post_id, $post);
        $nonce_name   = isset($_POST['custom_nonce']) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        update_post_meta($post_id, "dp_metabox_{$this->id}", $data);
    }
}