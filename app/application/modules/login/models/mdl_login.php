<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*====================================================================================================
 |	ORIGINAL TEMPLATE BY DAVID CONNELLY @ INSIDERCLUB.COM MODIFIED BY LEWIS NELSON @ LEWNELSON.COM
 |====================================================================================================
 |
 |	This is the basic model template where only the table name changes and the
 |	class name changes. It was originally put together by David Connelly from
 |	http://insiderclub.com and the original template can be found at
 |
 |	http://www.insiderclub.org/perfectcontroller
 |
 |	It has been modified by Lewis Nelson from http://lewnelson.com to include
 |	comments and add a few more functions for some additional common queries.
 |
 ====================================================================================================
 |
 |	Template designed to run with CodeIgniter & wiredesignz Modular Extensions - HMVC
 |
 |====================================================================================================
 */

class Mdl_Login extends CI_Model
{

	private $database;
	private $table;

    function __construct() {
		parent::__construct();
		$this->database = $this->load->database('login', TRUE);
    }

	// Unique to models with multiple tables
	function set_table($table) {
		$this->table = $table;
	}
	
	// Get table from table property
    function get_table() {
		$table = $this->table;
		return $table;
    }

	// Retrieve all data from database and order by column return query
    function get($order_by) {
		$db = $this->database;
		$table = $this->get_table();
		$db->order_by($order_by);
		$query=$db->get($table);
		return $query;
    }

	// Limit results, then offset and order by column return query
    function get_with_limit($limit, $offset, $order_by) {
		$db = $this->database;
		$table = $this->get_table();
		$db->limit($limit, $offset);
		$db->order_by($order_by);
		$query=$db->get($table);
		return $query;
    }

	// Get where column id is ... return query
    function get_where($id) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where('id', $id);
		$query=$db->get($table);
		return $query;
    }

	// Get where custom column is .... return query
    function get_where_custom($col, $value) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where($col, $value);
		$query=$db->get($table);
		return $query;
    }
	
	// Get where with multiple where conditions $data contains conditions as associative
	// array column=>condition
    function get_multiple_where($data) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where($data);
		$query=$db->get($table);
		return $query;
    }
	
	// Get where column like %match% for single where condition
    function get_where_like($column, $match) {
		$db = $this->database;
		$table = $this->get_table();
		$db->like($column, $match);
		$query=$db->get($table);
		return $query;
    }
	
	// Get where column like %match% for each $data. $data is associative array column=>match
    function get_where_like_multiple($data) {
		$db = $this->database;
		$table = $this->get_table();
		$db->like($data);
		$query=$db->get($table);
		return $query;
    }
	
	// Get where column not like %match% for single where condition
    function get_where_not_like($column, $match) {
		$db = $this->database;
		$table = $this->get_table();
		$db->not_like($column, $match);
		$query=$db->get($table);
		return $query;
    }

	// Insert data into table $data is an associative array column=>value
    function _insert($data) {
		$db = $this->database;
		$table = $this->get_table();
		$db->insert($table, $data);
    }
	
	// Insert data into table $data is an associative array column=>value
    function insert_batch($data) {
		$db = $this->database;
		$table = $this->get_table();
		$db->insert_batch($table, $data);
    }

	// Update existing row where id = $id and data is an associative array column=>value
    function _update($id, $data) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where('id', $id);
		$db->update($table, $data);
    }

	// Delete a row where id = $id
    function _delete($id) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where('id', $id);
		$db->delete($table);
    }

	// Delete a row where $column = $value
    function delete_where($column, $value) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where($column, $value);
		$db->delete($table);
    }
	
	// Count results where column = value and return integer
    function count_where($column, $value) {
		$db = $this->database;
		$table = $this->get_table();
		$db->where($column, $value);
		$query=$db->get($table);
		$num_rows = $query->num_rows();
		return $num_rows;
    }

	// Count all the rows in a table and return integer
    function count_all() {
		$db = $this->database;
		$table = $this->get_table();
		$query=$db->get($table);
		$num_rows = $query->num_rows();
		return $num_rows;
    }

	// Find the highest value in id then return id
    function get_max() {
		$db = $this->database;
		$table = $this->get_table();
		$db->select_max('id');
		$query = $db->get($table);
		$row=$query->row();
		$id=$row->id;
		return $id;
    }

	// Specify a custom query then return query
	function _custom_query($mysql_query) {
		$db = $this->database;
		$query = $db->query($mysql_query);
		return $query;
    }

}