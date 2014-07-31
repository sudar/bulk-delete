<?php
/**
 * Table to show cron list
 *
 * @package    Bulk_Delete
 * @subpackage Cron
 * @author     Sudar
 */
class Cron_List_Table extends WP_List_Table {

	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	 function __construct() {
		 parent::__construct( array(
		'singular' => 'cron_list', //Singular label
		'plural'   => 'cron_lists', //plural label, also this well be one of the table css class
		'ajax'     => false //We won't support Ajax for this table
		) );
	 }

	/**
	 * Add extra markup in the toolbars before or after the list
     *
	 * @param string $which Whether the markup should appear after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			//The code that goes before the table is here
            echo '<p>';
            _e('This is the list of jobs that are currently scheduled for auto deleting posts in Bulk Delete Plugin.', 'bulk-delete');
            echo '</p>';
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
            echo '<p>&nbsp;</p>';
            echo '<p>';
            echo '<strong>';
            _e('Note: ', 'bulk-delete');
            echo '</strong>';
            _e('Scheduling auto post or user deletion is available only when you buy pro addons.', 'bulk-delete');
            echo '</p>';

            BD_License::display_available_addon_list();
		}
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'col_cron_due'=>__('Next Due', 'bulk-delete'),
			'col_cron_schedule'=>__('Schedule', 'bulk-delete'),
			'col_cron_type'=>__('Type', 'bulk-delete'),
			'col_cron_options'=>__('Options', 'bulk-delete')
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
     *
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return $sortable = array(
			'col_cron_type' => array( 'cron_type', true )
		);
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
        $cron_items = Bulk_Delete_Util::get_cron_schedules();
        $totalitems = count($cron_items);

        //How many to display per page?
        $perpage = 50;

        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
		//The pagination links are automatically built according to those parameters

		/* — Register the Columns — */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

        $this->items = $cron_items;
	}

    function column_col_cron_due($item) {
        //Build row actions
        $actions = array(
            'delete'    => sprintf( '<a href="?page=%s&bd_action=%s&cron_id=%s&%s=%s">%s</a>',
                                $_REQUEST['page'],
                                'delete_cron',
                                $item['id'],
                                'sm-bulk-delete-cron-nonce',
                                wp_create_nonce( 'sm-bulk-delete-cron' ),
                                __( 'Delete', 'bulk-delete' )
                            ),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(%2$s)</span>%3$s',
            /*$1%s*/ $item['due'],
            /*$2%s*/ ($item['timestamp'] + get_option('gmt_offset') * 60 * 60 ),
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_col_cron_schedule($item) {
        echo $item['schedule'];
    }

    function column_col_cron_type($item) {
        echo $item['type'];
    }

    function column_col_cron_options($item) {
        // TODO: Make it pretty
        print_r ($item['args']);
    }

    function no_items() {
        _e('You have not scheduled any bulk delete jobs.', 'bulk-delete');
    }
}
?>
