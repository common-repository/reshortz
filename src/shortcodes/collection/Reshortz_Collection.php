<?php

require_once RESHORTZ_ROOT . 'src/core/Reshortz_MetaWorker.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_API_Adapter.php';
require_once RESHORTZ_ROOT . 'src/core/Reshortz_Query_Builder.php';
require_once RESHORTZ_ROOT . 'src/core/models/Reshortz_Shortcode_Model.php';

class Reshortz_Collection
{
    use Reshortz_MetaWorker;

    /**
     * @var Reshortz_API_Adapter $adapter
     */
    protected $adapter;

    /**
     * @var Reshortz_Query_Builder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * Reshortz_Collection constructor.
     */
    public function __construct()
    {
        $this->adapter = new Reshortz_API_Adapter();
        $this->queryBuilder = new Reshortz_Query_Builder();

        add_action('init', array($this, 'register_shortcode'));
    }

    /**
     * Register shortcode
     */
    public function register_shortcode()
    {
        add_shortcode('reshortz_collection', array($this, 'handle'));
    }

    /**
     * Get shortcode view path
     *
     * @return string
     */
    public function get_view_path()
    {
        return RESHORTZ_ROOT . 'views/shortcodes/collection/collection.php';
    }

    /**
     * Enqueue needed assets for the shortcode
     */
    public function enqueue_assets()
    {
        wp_enqueue_script( 'vue' );
        wp_enqueue_script( 'reshortz-collection' );
        wp_enqueue_style( 'reshortz-styles' );
    }

    /**
     * @param $collection
     * @param $ui_settings4
     */
    public function localize_script($collection, $ui_settings, $collection_id)
    {
        wp_localize_script( 'reshortz-collection', 'reshortz', [
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            'items_' . $collection_id => $collection,
            'ui_settings_' . $collection_id => $ui_settings,
            'l10n'        => [
                'viewing_tag'       => esc_html__('Tagged in', 'reshortz'),
                'viewing_category'  => esc_html__('In category', 'reshortz'),
                'comments'          => esc_html__('Comments', 'reshortz'),
                'no_comments'       => esc_html__('No Comments yet.', 'reshortz'),
                'comments_disabled' => esc_html__('Comments are disabled.', 'reshortz'),
                'comment_added'     => esc_html__('Comment added!', 'reshortz'),
                'show_all'          => esc_html__('Show all', 'reshortz'),
                'remaining'         => esc_html__('more to view', 'reshortz')
            ],
        ]);

    }

    /**
     * Validate shortcode attributes.
     * This has a minimal implementation thanks to Shortcode Manager.
     * All options are visually built.
     *
     * @param $atts
     * @return bool
     */
    public function validate_shortcode_atts($atts)
    {
        if(!isset($atts['id'])) {
            return false;
        }

        return true;
    }

    /**
     * Handle shortcode logics and build everything
     *
     * @param $atts
     * @return false|string
     */
    public function handle($atts)
    {
        if(!$this->validate_shortcode_atts($atts)) {
            return '';
        }

        $this->enqueue_assets();

        // Extract collection ID
        $collection_id = $atts['id'];

        // Initialize Shortcode DB model
        $model = new Reshortz_Shortcode_Model($collection_id);

        // Load shortcode settings
        $items = $this->queryBuilder->get_shortcode_items($collection_id, $model);

        // Shortcode UI Settings
        $ui_settings = $model->get_ui_settings();

        // Load an array where ReShorts will be stored
        $collection = array();

        // Load items
        foreach ($items as $item_id) {
            $data = $this->adapter->build_data($item_id);
            if ($data) {
                $collection[] = $data;
            }
        }

        // Localize JS attributes before loading Vue template
        $this->localize_script($collection, $ui_settings, $collection_id);

        // Load view
        ob_start();
        include $this->get_view_path();
        return ob_get_clean();
    }

}
