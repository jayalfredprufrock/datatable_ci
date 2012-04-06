<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
  
  /**
  * CI Datatables
  *
  * This is a wrapper class/library for Allan Jardine's Datatables Jquery plugin that
  * implements the javascript array data source method (http://datatables.net/examples/data_sources/js_array.html)
  *
  * @package      CodeIgniter 2.1.0
  * @subpackage   libraries
  * @category     library
  * @version      1.0
  * @author       Andrew Smiley <jayalfredprufrock@gmail.com>
  * @website      http://proteanweb.com
  *
  * * Copyright 2011 Andrew Smiley, all rights reserved.
  *
  * This source file is free software, under a BSD 3-point license, available at:
  * http://proteanweb.com/licenses/BSD3.php
  * 
  * */
 
  
  class Datatables
  {
      //codeigniter instance
      protected $ci;

      //datatable default options
      protected $config = array();
      
      //datatable options
      protected $options = array();
      
      //datatable column defintions
      protected $columns = array();

      //datatable row data
      protected $data = array();
	  
	  //datatable callbacks
	  protected $callbacks = array();
	 
	  //datatable actions
	  protected $actions = array();
	  
	  //simple constants where the _key_ is 
	  //replaced by value in the final json result
	  protected $constants = array();
	  


      /**
      * Constructor - stores CI instance, loads helpers and default options from configuration file
      * 
      * @return $this
      */
  
      public function __construct(){
          
          $this->ci =& get_instance();  
          
          //load configuration default options
          //$this->ci->config->load('datatables', TRUE);
         
          $this->config = config_item('options', 'datatables');
		  
          //loop over default options, creating default datatables option object
          foreach($this->config as $type=>$option){
              
              //determine whether option is a main datatables option, or a plugin option
              if ($option['_']){
                
                  $this->options[$option['_']] = array();
                  
                  $t =& $this->options[$option['_']]; 
              }
              else {
                
                  $t =& $this->options;
              }
               
              unset($option['_']);
              
              //copy over default values
              foreach($option as $o){
                
                  $t[$o['name']] = $o['default'];          
              }

          }          
          
          return $this; 
      }
      
  
      /**
      * Reloads original library configuration, overriding options provided by the parameter
      *
      * @param array $options : array or array of arrays (keyed by option type) overriding 
      * default options using the library's or datatable's option names
      * 
      * @return $this
      */
  
      public function initialize($options = array()) {
          
		  $this->columns = array();
		  $this->constants = array();
		  $this->callbacks = array();
		  $this->data = array();
		  $this->options = array();
		  $this->actions = array();
		  
          foreach($options as $type=>$option){
              
              if (is_array($option)){
                  
                  $this->set_options($option, $type);
              }
              else {
                  
                  //in this case, $type = value
                  $this->set_option($option, $type);
              }
          }        

          return $this;  
      }
	  
	  /**
      * Restores the library state to the point before columns/rows were added. 
	  * Options previously set, including constants, will remain in place. 
	  * To clear everything, use initialize() 
      *
      * @return $this
      */
     
	  public function clear(){
          
          $this->columns = array();
   		  $this->callbacks = array();
		  $this->data = array();
		  $this->actions = array();
		  
          $this->options['aoColumns'] = array();
          
          //copy default column options from config
          foreach($this->config['column'] as $i=>$column){
              
              if ($i != '_'){
                  
                  $this->options['aoColumns'][$column['name']] = $column['default'];
              }
          }

          return $this;
      }
	  
      
	  
      /**
      * Sets a single datatables global option
      *
      * @param string $option : library/datatable option name
      * @param mixed  $value  : option value
      * @param string $type   : library/datatable option type name (main, column, table_tools, col_vis, col_reorder, language)
	  *                       : setting 'column' options only affect future created columns, to set an existing
	  *                       : column's options, use set_column_option()  
      *
      * @return $this
      */
  
      public function set_option($option, $value, $type = 'main'){
    
          //determine whether option is a main datatables option, or a plugin option
          if ($type != 'main'){
            
              //resolve type if non-datatables name was used
              if (isset($this->config[$type])){
                  
                  //try and resolve option name, since a non-datatables type was used
                  if (isset($this->config[$type][$option])){
                    
                      $option = $this->config[$type][$option]['name'];
                  }
                  
                  $type = $this->config[$type]['_'];
              }
              else {
                  //this handles the case where a datatables type name is used 
                  //with a non-datatables option name
                  foreach($this->config as $t){
                    
                      if ($t['_'] == $type){
                        
                            if (isset($t[$option])){
                              
                                $option = $t[$option]['name'];
                            }
   
                            break;
                      }
                  }
              }

			  if (!isset($this->options[$type])){
			  		$this->options[$type] = array();
			  }        

              $t =& $this->options[$type];             
          }
          else {
            
              $t =& $this->options; 
              
              //try and resolve option name in case non-datatables name was used
              $option = isset($this->config['main'][$option]) ? $this->config['main'][$option]['name'] : $option;

          }
			
          //finally, set option value!      
          $t[$option] = $value;


          return $this;
      }



  
      /**
      * Sets multiple datatable options of a given type
      *
      * @param array $options : array of options, where the key is the datatables/libraries option name
      * @param string $type   : library/datatable option type name
      * 
      * @return $this
      */
     
      public function set_options($options, $type = 'main'){

          foreach($options as $o=>$v){
              $this->set_option($o, $v, $type);
          }

          return $this;
      }
	  
	  
	  
	  
	  /**
      * Removes a column from the table
      *
      * @param mixed $column : any valid get_column_index() parameter
	  * @param string $option : library/datatable column option name
	  * @param mixed $value   : column option value  
	  * 
      * @return $this
      */    
	  public function set_column_option($column, $option, $value){
	  	
			$index = $this->get_column_index($column);
			
			if ($index != '-1') {
				
				//support non-datatable option names
				if (isset($this->config['column'][$option])){
					
					$option = $this->config['column'][$option]['name'];	
				}
				
				$this->columns[$index][$option] = $value;
			}
			
			return $this;
		
	  }
  
  
  
      /**
      * Retrieves a column index by name or index
      *
      * @param mixed $column : string - name/title of column, 
      *                        positive int - index of column
      *                        negative int - index of column from the end 
      * 
      * @return mixed : int - column index,
	  *                 bool FALSE - if not found
      */ 
      public function get_column_index($column) {
        
          if (is_int($column)){
            
              //specifying column index from the right
              if ($column < 0){
                
                  $column += count($this->columns);
              }
              
              return $column < 0 || $column >= count($this->columns) ? -1 : $column;
          }
          else {
            
              $column = strtolower($column);
              
              foreach($this->columns as $index => $col){
                
                  if (strtolower($col['sTitle']) == $column || strtolower($col['sName']) == $column){
                    
                      return $index;
                  }
              }
          }
          
          return FALSE;
      }
	  
	  
	  
	  /**
      * Adds a constant to be replaced before outputting the finalized js / table strings
      *
      * @param string $key : name of constant
      * @param string $value : value to replace occurances of _name_
      * 
      * @return $this
      */  
	  public function add_constant($key, $value){
	  	
			//trim "_" in case the key is already "underscored"
			$this->constants[trim($key, "_")] = $value;
			
			return $this;
	  }
  


  
      /**
      * Loads initial datatable data, optionally creating columns
      *
      * @param mixed $data : array of or single array/object of data to add to datatable
      * @param bool $add_columns : use keys of first data row to create columns
      *
      * @return $this
      */    
      public function load_data($data, $add_columns = FALSE){
       
          $this->data = array();
         
          foreach((array)$data as $row){
          	
              //force data to be an array of arrays   
			  $row = (array)$row;      
             
              //json encode arrays/objects
              foreach($row as $c=>$r){
              		
					if (is_array($r) || is_object($r)){
						$row[$c] = json_encode($r);
					}

					//create columns 
					if ($add_columns){
                  		$this->add_column($c);
					}
              }
				

              $add_columns = FALSE; 
              
			  
			  $this->data[] = $row;
             
          }

          return $this;
      }
  
  
  
      /**
      * Adds a column to the table
      *
      * @param mixed $name : string - the column name
      *                      array  - an array of column options 
      * @param mixed $position : any valid get_column_index() parameter
	  * @param mixed $cell_value : string - value for each cell in this column (use {column_name} to pull in row data values)
	  *                          : bool false - juse display the row value normally ({$name}) 	
	  * @param array $options    : additional column options to set 			
	  * 
      * @return $this
      */
      public function add_column($name_arguments, $position = FALSE, $cell_value = FALSE, $options = array()){ 
        
          //allow the first parameter to be used to
          //pass an array of arguments
          if (is_array($name_arguments)){
		         	
				 extract($name_arguments);
				 
				 //don't want to add these to the options array
				 unset($name_arguments['name'], $name_arguments['sName'], $name_arguments['cell_value'], $name_arguments['_sCellValue'], $name_arguments['position']);
						
				 $options += $name_arguments;

  		  }
		  else {
		  		 $name = $name_arguments;
		  }	

          //set default column options
          $col = $this->options['aoColumns'];
		  
		  //merge column option overrides
		  //supporting both the datatable's option name and library's option name
		  foreach($options as $o=>$v){
		  	
		  		if (isset($this->config['column'][$o])){
	  				
					$o = $this->config['column'][$o]['name'];		
		  		}
				$col[$o] = $v;
		  }
		  

		  //handle the variables that can be passed directly to the function	
          $col['sName'] = isset($sName) ? $sName : (isset($name) ? $name : $col['sName']);
		  
		  $col['sTitle'] = isset($sTitle) && $sTitle !== FALSE ? $sTitle : (isset($title) && $title !== FALSE ? $title : humanize($name));
		  
          $col['_sCellValue'] = (isset($_sCellValue) && $_sCellValue !== FALSE) ? $_sCellValue : ((isset($cell_value) && $cell_value !== FALSE) ? $cell_value : '{'.$name.'}');

		  //set position if supplied, or default to the end of the column array
          $position = !isset($position) || $position === FALSE ? count($this->columns) : $this->get_column_index($position);
      
          array_splice($this->columns, $position, 0, array($col));
		  
		  //if option set, disable ability to toggle visibility
		  if (!$col['_bVisibilityToggle']){
		  			
				//Let's hope this doesn't cause any problems
				//Should try to handle this more elegantly
				while (in_array($position,$this->options['oColVis']['aiExclude'])){
					$position++;
				}
		  		$this->options['oColVis']['aiExclude'][] = $position;
		  }
          
          return $this;
      }




	  /**
      * Adds a "action" column to the table
      *
      * @param array $options : an array containing options to pass to the add_column method
	  * 					    additionally, this array must contain a key called 'name' whose value is the 
	  * 					    event action name to trigger when a cell in this column is clicked ("action_" is prepended to this name)
	  * 					    optionally, you may provide a key called "js" which contains the javascript code to execute when
	  *                         the event is triggered                  
      * @param mixed $position : any valid get_column_index() parameter				
	  * 
      * @return $this
      */
	  public function add_action_column($options, $position = FALSE){
	  		
			//force required parameter 'action'
			if (isset($options['name'])){
					
				$this->actions[$options['name']] = isset($options['js']) ? $options['js'] : FALSE;
				
				$this->add_column($options, $position);
			}
		
			return $this;
	  }




      /**
      * Removes a column from the table. This effectively changes the indexes of the remaining columns, so 
	  * remove any columns before adding new columns or changing index-dependent options for existing columns 
      *
      * @param mixed $columns : an array of or single valid get_column_index() parameter
	  * 		
      * @return $this
      */
      public function remove_columns($columns){
            
          foreach((array)$columns as $column){
                
              $index = $this->get_column_index($column);
          
              if ($index !== FALSE){
                
                  array_splice($this->columns, $index, 1);
              }
            
          }        
    
          return $this;
      } 
  
  
  
  	  /**
      * Provides a way to alter column cell values by executing a php callback function
      *
      * @param mixed $columns : any valid get_column_index() parameter	
	  * @param string $function : the name of a function to call
	  * @param mixed $parameters : one or more parameters to pass to the function, for more than one use an array
	  *                          : use 'cell_value' to pass the contents of the cell to the function
	  *                          : if not provided, 'cell_value' is automatically added to the beginning of the paramater array 	 
	  *  
      * @return $this
      */  
      public function add_column_callback($column, $function, $parameters=array()){
        
          $index = $this->get_column_index($column);
          
          if ($index !== FALSE){
              
              if (is_callable($function)){
              	
				  if (!is_array($parameters)){
				  	  $parameters = array($parameters);
				  }
				
                  if (!in_array('cell_value', $parameters)){
                  	
                      array_unshift($parameters,'cell_value');
                  }            
                  
                  
                  $this->columns[$index]['_afCallbacks'][] = array('function'=>$function, 'parameters' => $parameters);
              }
          }
           
          return $this;
      }
	  
	  
	  
	  /**
      * Adds column data to a datatable column <th> element, specified by the index
	  * 
      * @param mixed $columns : any valid get_column_index() parameter	
      * @param string $key : key to use for data storage
	  * @param string $value : value for data storage
	  * 
      * @return $this
      */
	  public function add_column_data($column, $key, $value){
	  		
			$index = $this->get_column_index($column);
          
          	if ($index !== FALSE){
					
				$this->columns[$index]['_aColData'][$key] = $value;
			}
			
			return $this;
	  }
  
  
  
  
      /**
      * Hides a column from the table
      *
      * @param mixed $position : an array of or single valid get_column_index() parameter
      * @return $this
      */
      public function hide_columns($columns, $toggle_visibility = TRUE){
            
          foreach((array)$columns as $column){
                
              $index = $this->get_column_index($column);
          
              if ($index !== FALSE){
              	
                  $this->set_column_option($index, 'bVisible', FALSE);
				  
				  if (!$toggle_visibility){
				  	
				  		$this->options['oColVis']['aiExclude'][] = $index;
				  }
              }     
          } 

          return $this;
      } 
	  
  
  
  
      /**
      * Adds a row id to datatable rows specified by the index
      *
      * @param string $id : id to add to row <tr> element (include row data variables using {column_name})
      * @param mixed $index : row index or FALSE for all rows
      *
      * @return $this
      */
      public function add_row_id($id, $index = FALSE){
      
	  	  if ($index === FALSE){
	  	  		for ($i = 0; $i < count($this->data); $i++){
	  	 			$this->data[$i]['DT_RowId'] = $id;
				}
			  
		  }	
		  elseif (isset($this->data[$index])) {
			  $this->data[$index]['DT_RowId'] = $id;
		  }

          return $this;
      }
      
	  
      
      
      /**
      * Adds a row class to datatable rows specified by the index
      *
      * @param string $class : class to add to row <tr> element (include row data variables using {column_name})
      * @param mixed $index : row index, or FALSE to add to all rows
      *
      * @return $this
      */
      public function add_row_class($class, $index = FALSE){
        
		
		if ($index === FALSE){
			$start = 0;
			$end = count($this->data);
		}
		else {
			$start = $index;
			$end = $start + 1;
		}
		
		for ($i = $start; $i < $end; $i++){
			
			if (!isset($this->data[$i]['DT_RowClass'])){
          
	          $this->data[$i]['DT_RowClass'] = $class;
	        }
	        //append to existing class if a class has already been set
			elseif (isset($this->data[$i])) {
	          
	          $this->data[$i]['DT_RowClass'] .= ' ' . $class;
	        }
		}  
        
        return $this;
      }
	  
	  
	  
	  
	  
	  /**
      * Adds row data to datatable rows <tr> elements specified by the index
      *
      * @param string $key : key to use for data storage
	  * @param string $value : value for data storage (include row data variables using {column_name})
      * @param mixed $index : row index or FALSE for all rows
      *
      * @return $this
      */
      public function add_row_data($key, $value, $index = FALSE){
      
	  	   if ($index === FALSE){
	  	  		for ($i = 0; $i < count($this->data); $i++){
	  	 			$this->data[$i]['DT_RowData'][$key] = $value;
				}
			  
		  }	
		  else {
			  $this->data[$index]['DT_RowData'][$key] = $value;
		  }
         
          return $this;
      }
  
	  
	 
	 

      /**
      * Builds and outputs/returns the table according to initialization parameters 
      * @param string $id : Unique id for table 
      * @param bool $output : Output table and js to browser
      * 
      * @return array($id=>$table,'js'=>$js)
      */
      public function generate($id, $name = FALSE, $output = FALSE)
      {   
      	  
		  $this->add_constant('id', $id);
		  $this->add_constant('name', $name ? $name : str_replace('_', ' ', $id));
		  $this->add_constant('singularname', singular($this->constants['name']));
		  
		  $this->callbacks['init_complete'] = '';
		  $table['js'] = ''; 
		  	
          $this->options['aaData'] = array();
          
		  if ($this->data){
		  	
	          //temporarily add these columns to allow the adding of row ids and classes
	          $this->add_column('DT_RowId')
	               ->add_column('DT_RowClass')
				   ->add_column('DT_RowData'); 

	          //loop through columns
	          foreach($this->columns as $col_index=>$col){
	           
	              //extract column variables     
	              preg_match_all($this->options['_sTokenRegex'], $col['_sCellValue'], $vars);
	      
	              //loop through rows, replacing variables
	              foreach($this->data as $row_index=>$row){            

	                    //apply row ids/classes special case
	                    if ($col['sName'] == 'DT_RowId' || $col['sName'] == 'DT_RowClass' || $col['sName'] == 'DT_RowData'){
	  
	                        if (isset($row[$col['sName']])){
	                        
	                            $col['_sCellValue'] = $col['sName'] == 'DT_RowData' ? implode('~_|_~',array_values($row['DT_RowData'])) : $row[$col['sName']];				
	                            
	                            $col_index = $col['sName'];
	                            
	                            //need to run regexp again
	                            preg_match_all($this->options['_sTokenRegex'], $col['_sCellValue'], $vars);
								
								//replace with data
								foreach($vars[0] as $i=>$v){
	      
		  							$replacement_col_index = $this->get_column_index($vars[1][$i]);
									
			                        //try to replace variables with row data already processed
			                        if ($replacement_col_index !== FALSE){
			      
			                              $col['_sCellValue'] = str_replace($v, $this->options['aaData'][$row_index][$replacement_col_index], $col['_sCellValue']);
			                        }
									else if (array_key_exists($vars[1][$i],$row)){
										
										  $col['_sCellValue'] = str_replace($v, $row[$vars[1][$i]], $col['_sCellValue']);
									}
			                    }
								
								if ($col['sName'] == 'DT_RowData'){
									
									$cell_data = array_combine(array_keys($row['DT_RowData']), explode('~_|_~',$col['_sCellValue']));
		
									foreach($cell_data as $d_key=>$d_val){
										$this->options['aaData'][$row_index]["0"] .= form_textarea($d_key, $d_val, 'style="display:none"');
									}
									
								}
								else {
									$this->options['aaData'][$row_index][$col['sName']] = $col['_sCellValue'];    
								}
	  
	                        }
	                        
	                        continue;
	                    }
	                    
	                    $this->options['aaData'][$row_index][$col_index] = $col['_sCellValue'];
	                          
	                    foreach($vars[0] as $i=>$v){
	      
	                        //try to replace variables with data
	                        //important to use array_key_exists, since isset returns
	                        //false in case the value is NULL, which could be the desired replacement value
	                        if (array_key_exists($vars[1][$i],$row)){
	      
	                              $this->options['aaData'][$row_index][$col_index] = str_replace($v, $row[$vars[1][$i]], $this->options['aaData'][$row_index][$col_index]);
	                        }
	                    }         
	                    
	                    //run column callbacks on row
	                    foreach($col['_afCallbacks'] as $cb){
	                        $cb['parameters'] = str_replace('cell_value', $this->options['aaData'][$row_index][$col_index], $cb['parameters']);
	                        $this->options['aaData'][$row_index][$col_index] = call_user_func_array($cb['function'], $cb['parameters']);
	                    }
	              }

 				 //add column data
				 foreach($col['_aColData'] as $key=>$value){
					
					$value = str_replace("'","\'", $value);	
					$this->callbacks['init_complete'] .= "$(dt.aoColumns[" . ($col_index) . "].nTh).data('$key','$value');";
				 }


				  if ($col['_sEditable']){

					if (!isset($col['_aEditableOptions']['submitdata'])){
						
						$col['_aEditableOptions']['submitdata'] = 'function(value,settings){return {';
						
						if ($this->ci->config->item('csrf_protection') === TRUE){
							$col['_aEditableOptions']['submitdata'] .= $this->ci->security->get_csrf_token_name() .':\''. $this->ci->security->get_csrf_hash() . '\',';
						}					
						
						foreach($col['_aEditableRowData'] as $key){
							$col['_aEditableOptions']['submitdata'] .= 'row_'.$key.':$(this).parent().data(\'' . $key . '\'),';
						}
						
						foreach($col['_aEditableColData'] as $key){
							$col['_aEditableOptions']['submitdata'] .= 'col_'.$key.":$(dt.aoColumns[" . ($col_index) . "].nTh).data('$key'),";	
						}
						
						$col['_aEditableOptions']['submitdata'] = rtrim($col['_aEditableOptions']['submitdata'],',') . '};}';
														
					}
					
					if ($this->options['_bKeyTable']){
						
						$col['_aEditableOptions']['onreset'] = "function(jeditable,td){ setTimeout( function () {{$id}_keys.block = false;}, 0);}";	
						$col['_aEditableOptions']['callback'] = "function(jeditable,td){ {$id}_keys.block = false;}";	
					}
					
					$js = "$(dt.nTable).dataTable().$('td:nth-child(__COL_INDEX__)').editable('__URL__',__OPTIONS__);";
					
					$this->callbacks['init_complete'] .= str_replace(array('__COL_INDEX__','__URL__','__OPTIONS__'),
							                            array($col_index+1,$col['_sEditable'],json_escape_function(json_encode($col['_aEditableOptions'])))
							                            ,$js);
				}


	          }
	      
	          //remove temporary columns
	          $this->remove_columns(array('DT_RowId','DT_RowClass','DT_RowData'));
			  
			  //load column definitions
          	  $this->options['aoColumns'] = $this->columns;
          }
		  else {
			  $this->options['aoColumns'] = array($this->columns);
			  $this->options['bSort'] = FALSE;
		  }


          
          
          //add callback to set placeholder on filter search box 
          //and callback to store row data   
          $this->callbacks['init_complete'] .=  "$(dt.nTableWrapper).find('.dataTables_filter input').attr('placeholder','{lang_search_placeholder}');
          											
													var rows = $(dt.nTable).dataTable().fnGetNodes();
													$.each(rows,function(i){
														var row = $(this);
														row.children().first().children('textarea').each(function(){
															row.data($(this).attr('name'),$(this).val());
															$(this).remove();
														});
													});
          										
									            ";
												
		  $table[$id] = '';
		         
          
          //create html table element
          $table[$id] .= $this->options['_sTable'];
          
 
          //create datatable object, setting options and escaping callback functions
          $table['js'] = "var $id = $('#$id').dataTable(" . $this->json_options($this->options) . ');' . $table['js'];   
		  
		  
		  //store singular name for reference
		  $table['js'] .= "$id.data('singular_name','_SINGULARNAME_');";
		  
          //add window resize event to keep column widths in sync
          if ($this->options['_bAutoResize']){
          	
             	$table['js'] .= "$(window).bind('resize', function(){{$id}.fnAdjustColumnSizing();});";
          }
          
          if ($this->options['_bKeyTable']){
          				      	
          		$table['js'] .= "var {$id}_keys = new KeyTable( { table : $('#{$id}')[0], datatable : $id ," . substr(json_encode($this->options['oKeyTable']),1) . ');';
          		
          		$table['js'] .= "{$id}_keys.event.action(null,null,function(td, x, y){ var jeditable_event = $(td).data('event.editable'); if (jeditable_event) {{$id}_keys.block = true; $(td).triggerHandler(jeditable_event); }});";	
          }
          	
          
          
          //Append button to "Add Row"
          if ($this->options['_sRowForm']){
  					
  				$table[$id] .= '<span class="icon-add" title="Add _SINGULARNAME_"></span>';
  				
  				$table['js'] .= "(function ($, table) {
  					
								 var form = $('<div title=\"Add _SINGULARNAME_\"></div>');
								 form.html('" . strip_newlines($this->ci->load->view($this->options['_sRowForm'],$this->options['_aRowFormData'], TRUE)) . "');
								 $(document.body).append(form);
  					             var icon = table.closest('.dataTables_wrapper').next('span');
								 var reset = form.find('button[type=reset]');	
								
								 form.dialog({
									 resizable: false,
									 modal: true,
									 autoOpen: false,
									 width: 450,
									 open: function(e,ui){
									 	var first = form.find('input,select,textarea').first();
						        	 	if (!first.hasClass('date') && !first.is('focus')){
						        	 		first.focus();
						        	 	}
									 },
									 close: function(e,ui){
										reset.click();
									 }
								 });
								
								 reset.click(function(){
									if (form.dialog('isOpen')){
										form.dialog('close');
									}
								 });
								
								
								 icon.click(function(){
									form.dialog('open');
								 });
								
								 form.find('form').bind('formSubmitSuccess',function(e, resp){
									
									var columns = $id.fnSettings().aoColumns;
									
									var data = $id.fnGetData(0);
									
									form.dialog('close');
									
									if (data){
										var row = [];
										$.each(data, function(col_index, value){
											if (columns[col_index]){
												row.push(resp.row[columns[col_index].sName] != null ? resp.row[columns[col_index].sName] : value);
											}
										});
										
										$id.fnAddData(row);
									}
									else {
										window.location.reload();
									}
								 });
								 })(jQuery, $id);";	
          }
          
          if ($this->actions) {
          	
			  //add click event to table to handle actions
			  $table['js'] .= "$id.click(function(e){
								if (e.target.className.indexOf('action[') != -1){
									var actions = /action\[(\S+)\]/ig.exec(e.target.className)[1].split(',');
									for (var i=0; i < actions.length; i++){
										$id.triggerHandler('action_'+actions[i],[ $id , $(e.target).closest('tr')]);
									}
								}
							  });";
			  
			  //bind events for each action			  
			  foreach($this->actions as $action => $js) {
			  		if ($js){
						$table['js'] .= "$id.bind('action_$action',function(e, table, row){
											$js
			 			   				 });";
					}
			  }
			  
		  }			  
		  
		  $table[$id] = $this->replace_constants($this->replace_language_vars($table[$id]));
		  $table['js'] = $this->replace_constants($this->replace_language_vars($table['js']));
          
          //output to browser
          if ($output){

              echo $table[$id] . '<script type="text/javascript">$(function(){' . $table['js'] . '}</script>';
          }
          
    
          return $table;
  
      }
      
      



	  public function json_options($options){
	  	
			$options = $this->remove_custom_options($options);		
				
			
			$options = $this->replace_callbacks(json_encode($options));
		
			
			return $options;
	  }
	  
	  public function json_storage_encode($obj){
	
	  		return '_JSON_STORAGE_'.str_replace(array(':','[',']','"',"'","|"),array('_COLON_','_LBRACKET_','_RBRACKET_','_QUOTE_','_APOSTROPHE_','_PIPE_'),json_encode($obj));
	  		
	  }
	  
	  public function json_storage_decode($json){
	  		if (strpos($json,'_JSON_STORAGE_') === 0){
	  			$json = str_replace(array('_COLON_','_LBRACKET_','_RBRACKET_','_QUOTE_','_APOSTROPHE_','_PIPE_'),array(':','[',']','"',"'","|"),substr($json,14));
	  		}
			return $json;
	  }
	  

      public function remove_custom_options($options){
          
          //recursively move through options array      
          foreach($options as $option=>$value){
              
              if (is_array($value)){

                  $options[$option] = $this->remove_custom_options($value);
              }
              //remove options preceded by an underscore
              else if (substr($option,0,1) == "_"){
                
                  unset($options[$option]);
              }    
          }

          return $options;
      }
	  
	  
	  public function replace_callbacks($string){
	  	
			foreach($this->callbacks as $cb=>$f){
	  			$string = str_ireplace('"{'.$cb.'}"', 'function(' . implode(', ', $this->config['main'][$cb]['parameters']) . '){' . $f . '}', $string);
	  		}
			
			return $string;
	  }
  
      public function replace_language_vars($string){

          preg_match_all('/{lang_([^}\s]*)}/i', $string, $vars);
          
          foreach($vars[0] as $i=>$var){
              
              $string = str_ireplace($var, lang('dt_'.$vars[1][$i]), $string);
          }
          
          
          return $string;        
      }
	  
	  
  	  public function replace_constants($string){
  	  	
			foreach($this->constants as $c=>$v){
				
				$string = str_ireplace('_'.$c.'_', $v, $string);
			}
		
	  		return $string;
	  }
	  
	  

      

  }

/* End of file datatables.php */