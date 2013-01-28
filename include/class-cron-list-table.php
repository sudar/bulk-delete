<?php
/**
 * Table to show cron list
 *
 * @package WordPress
 * @subpackage bulk-delete
 * @author Sudar
 */
class Cron_List_Table extends WP_List_Table {

	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	 function __construct() {
		 parent::__construct( array(
		'singular'=> 'cron_list', //Singular label
		'plural' => 'cron_lists', //plural label, also this well be one of the table css class
		'ajax'	=> false //We won't support Ajax for this table
		) );
	 }

	/**
	 * Add extra markup in the toolbars before or after the list
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			//The code that goes before the table is here
            echo '<p>&nbsp;';
            echo '</p>';
            echo '<p>';
            _e('This is the list of cron jobs that are currently scheduled for deleting posts by Bulk Delete Plugin.', 'bulk-delete');
            echo '</p>';
            echo '<p>';
            _e('Note: ', 'bulk-delete');
            _e('Scheduling post deletion using cron jobs is available only in the Pro version of the Plugin', 'bulk-delete');
            echo '</p>';
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
		}
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'col_cron_due'=>__('Next Due (GMT/UTC)'),
			'col_cron_schedule'=>__('Schedule'),
			'col_cron_type'=>__('Type'),
			'col_cron_options'=>__('Options')
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return $sortable = array(
			'col_cron_type'=>array('cron_type')
		);
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
		global $_wp_column_headers;
		$screen = get_current_screen();

		/* -- Preparing your query -- */
		$cron = _get_cron_array();
		$date_format = _x( 'M j, Y @ G:i', 'Publish box date format', 'cron-view' );
		foreach ( $cron as $timestamp => $cronhooks ) {
			foreach ( (array) $cronhooks as $hook => $events ) {
                if (substr($hook, 0, 15) != 'do-bulk-delete-') {
                    unset($cron[$timestamp]);
                } else {
                    foreach ( (array) $events as $key => $event ) {
                        $cron[ $timestamp ][ $hook ][ $key ][ 'date' ] = date_i18n( $date_format, $timestamp );
                    }
                }
			}
		}

        $totalitems = count($cron);
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

		/* -- Fetch the items -- */
        //$this->items = $wpdb->get_results($query);
        $this->items = $cron;
	}

    function column_col_cron_due($item) {
        foreach ($item as $key => $values) {
            foreach ($values as $value) {
                echo $value['date'];
            }
        }
    }

    function column_col_cron_schedule($item) {
        foreach ($item as $key => $values) {
            foreach ($values as $value) {
                echo $value['schedule'];
            }
        }
    }

    function column_col_cron_type($item) {
        foreach ($item as $key => $value) {
            echo $key;
        }
    }

    function column_col_cron_options($item) {
        foreach ($item as $key => $values) {
            foreach ($values as $value) {
                print_r($value['args']);
            }
        }
    }
}
?>
