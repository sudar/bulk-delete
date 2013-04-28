<?php
/**
 * Class that encapsulates the deletion of Categories
 *
 * @package Bulk Delete
 * @subpackage Categories
 * @author Sudar
 */
class Bulk_Delete_Cat_Days {
    var $days;
    var $op;

    public function __construct(){
        add_action( 'parse_query', array( $this, 'parse_query' ) );
    }

    public function parse_query( $query ) {
        if( isset( $query->query_vars['cats_days'] ) ){
            $this->days = $query->query_vars['cats_days'];
            $this->op = $query->query_vars['cats_op'];

            add_filter( 'posts_where', array( $this, 'filter_where' ) );
            add_filter( 'posts_selection', array( $this, 'remove_where' ) );
        }
    }

    public function filter_where($where = '') {
        $where .= " AND post_date " . $this->op . " '" . date('y-m-d', strtotime('-' . $this->days . ' days')) . "'";
        return $where;
    }

    public function remove_where() {
        remove_filter( 'posts_where', array( $this, 'filter_where' ) );
    }
}
?>
