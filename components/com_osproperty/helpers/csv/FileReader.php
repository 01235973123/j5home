<?php
/**
 * @author Nicolas BUI <nbui@wanadoo.fr>
 * 
 * Copyright: 2002 Vitry sur Seine/FRANCE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
require_once( dirname( __FILE__ ) . '/Reader.php' );

class FileReader extends Reader
{
	var $handle 	= null;
	
	public function __construct( $file )
	{
		if ( $file != '' )
		{
			$this->handle = @fopen( $file, 'r' );
		}
	}
	
	function ready()
	{
		return isset( $this->handle );
	}
	
	function skip( $count = 1 )
	{
		if ( $this->ready() == false || $count <= 0) return;
      	return fseek( $this->handle, intval( $count ), SEEK_CUR );
	}
	
	function read( $length = 1 )
	{
		if ( !$this->ready() )
      		return '';
      	if ( is_resource( $this->handle ) )
			return ( $length == 1 ? fgetc( $this->handle ) : fread( $this->handle, $length ) );
		return '';
	}
	
	function reset()
	{
		if ( !$this->ready() )
      		return;
      	@rewind( $this->handle );
	}
	
	function close()
	{
		if ( !$this->ready() )
      		return;
      	@fclose( $this->handle );
	}
	
	function isEOF()
	{
		return feof( $this->handle );
	}
	
	function cursorPosition()
	{
		if ( !$this->ready() )  
      		return -1;
      	return ftell( $this->handle );
	}

	// static function to read direct all file content
	function readFile( $file, $binary = false )
	{
		$content = '';
		if ( file_exists( $file ) )
		{
			$handle = @fopen( $file, 'rb' ); 
			if ( is_resource( $handle ) ) 
			{
				while ( false !== ( $c = fgetc( $handle ) ) )
				{
					$content .= $c;
				}
				fclose( $handle ); 
			}
		}
		return $content;
   }
   
   function is(&$object)
   {
      return is_subclass_of( $object, __CLASS__ );
   }
}
?>
