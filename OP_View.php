<?php 

class OP_View {

	protected $_path = null;
	protected $_data = null;

	public function __construct(){
		$this->_data = array();
	}

	public function render(){
		return OPView::render( $this->_path, $this->_data );
	}

	public function set_path( $view_path ){
		$this->_path = $view_path;
	}

	public function set_data( array $data ){
		$this->_data = array_merge( $this->_data, $data);
	}

	public function __get( $name ) {
		return $this->_data[$name];
	}

	public function __set( $name, $value ) {

		if ( method_exists( $this, 'set_' . $name ) ){
			call_user_func( array( $this, 'set_' . $name ), $value );
		} else{
			$this->_data[$name] = $value;
		}
	}

	public function __isset( $name ) {
		return isset( $this->_data[$name] );
	}

}