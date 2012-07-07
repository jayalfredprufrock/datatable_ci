<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
  * CI Datatable
  *
  * This is a wrapper class/library for Allan Jardine's Datatables Jquery plugin that
  * implements the javascript array data source method (http://datatables.net/examples/data_sources/js_array.html)
  *
  * @package      CodeIgniter 2.02
  * @subpackage   libraries
  * @category     library
  * @version      1.0
  * @author       Andrew Smiley <jayalfredprufrock@gmail.com>
  * @website      http://proteanweb.com
  * 
  * @config       Default datatables options where the key corresponds to the libraries internal options name
  *               A key of "_", indicates the datatables parent option name
  *               name : datatables options name (a prefixed underscore indicates a non-datatables option) 
  *               Constants defined within the library are available for use in this file. By default
  *               you may use _ID_ for the table id and _NAME_ for a friendly table title representation (_SINGULARNAME_ for singular) 
  *
  * * Copyright 2011 Andrew Smiley, all rights reserved.
  *
  * This source file is free software, under a BSD 3-point license, available at:
  * http://proteanweb.com/licenses/BSD3.php
  * 
  * 
  */
 
 
$config['options'] = array(


                      'main' => array(
                
                                  '_'      => '',
                
                                  'layout' => array('name'=>'sDom','default'=>'R<"H"iTCfr>t<"F"lp>'),
                                  
                                  'paginate' => array('name'=>'bPaginate','default'=>TRUE),
                                  
                                  'pagination_type' => array('name'=>'sPaginationType','default'=>'full_numbers'),
                                  
                                  'filter' => array('name'=>'bFilter','default'=>TRUE),
                                  
                                  'sort' => array('name'=>'bSort','default'=>TRUE),
                                  
								  'sorting' => array('name'=>'aaSorting','default'=>array(array(1,"desc"))),
                                  
                                  'display_length' => array('name'=>'iDisplayLength','default'=>10), 
                                  
                                  'length_change' => array('name'=>'bLengthChange','default'=>TRUE),
                                  
                                  'info' => array('name'=>'bInfo','default'=>TRUE),
                                  
                                  'jquery_ui' => array('name'=>'bJQueryUI','default'=>FALSE),
                                  
                                  'state_save' => array('name'=>'bStateSave','default'=>FALSE),
                                  
                                  'auto_width' => array('name'=>'bAutoWidth','default'=>TRUE),
                                  
                                  'defer_render' => array('name'=>'bDeferRender','default'=>FALSE),
                                  
                                  'scroll_collapse' => array('name'=>'bScrollCollapse','default'=>TRUE),
                                  
                                  'scroll_auto_css' => array('name'=>'bScrollAutoCss','default'=>FALSE),
                                  
                                  'scroll_x' => array('name'=>'sScrollX','default'=>'100%'),
                                  
                                  'length_menu' => array('name'=>'aLengthMenu','default'=>array(array(5,10,25,50,-1),array(5,10,25,50,"All"))),
                                  
                                  'destroy' => array('name'=>'bDestroy','default'=>TRUE),
                                  
                                  'retrieve' => array('name'=>'bRetrieve','default'=>TRUE),
                                  
                                  'auto_resize' => array('name'=>'_bAutoResize','default'=>TRUE),
                                  
                                  'token_regex' => array('name'=>'_sTokenRegex', 'default'=>'/{([^}]*)}/i'),
                                  
                                  'table' => array('name'=>'_sTable', 'default'=>'<table id="_ID_" cellpadding="0" cellspacing="0" border="0"></table>'),
                                  
                                  'format_js' => array('name'=>'_bFormatJs', 'default'=>FALSE),
                                  
								  'init_complete' => array('name'=>'fnInitComplete', 'default'=>'{init_complete}', 'parameters'=>array('dt')),
								  
								  'row_form' => array('name'=>'_sRowForm', 'default'=>''),
								  
								  'row_form_data' => array('name'=>'_aRowFormData', 'default'=>array()),
								  
								  'key_table' => array('name'=>'_bKeyTable', 'default'=>FALSE)
              
                                ),
                                
                    'column' => array(
                  
                                  '_' => 'aoColumns',
                                  
								  'name' => array('name'=>'sName', 'default'=>''),
								  
								  'title' => array('name'=>'sTitle', 'default'=>''),
                            
                                  'searchable' => array('name'=>'bSearchable', 'default'=>TRUE),
                      
                                  'sortable' => array('name'=>'bSortable', 'default'=>TRUE),
                      
                                  'use_rendered' => array('name'=>'bUseRendered', 'default'=>TRUE),
                      
                                  'visible' => array('name'=>'bVisible', 'default'=>TRUE),
                                        
                                  'class' => array('name'=>'sClass', 'default'=>NULL),
                                  
								  'width' => array('name'=>'sWidth', 'default'=>NULL),	 
								  
								  'cell_value' => array('name'=>'_sCellValue', 'default'=>''),
								  
								  'visibility_toggle'    => array('name'=>'_bVisibilityToggle', 'default'=>TRUE),
								  
								   //if you are adding columns to the table automatically, 
								   //consider using add_column_data() instead of setting this directly
								  'col_data' => array('name'=>'_aColData','default'=>array()),
								  
								  //if you are adding columns to the table automatically, 
								  //consider using add_column_callback() instead of setting this directly
                                  'callbacks' => array('name'=>'_afCallbacks', 'default'=>array()),


								  // JEDITABLE COLUMN OPTIONS	

								  'editable' => array('name'=>'_sEditable', 'default'=>''),
								  						  
								  //be careful when setting the jeditble option "submitdata" as this completely disables
								  //the datatable options "editable_row_data" and "editable_col_data" below
								  'editable_options' => array('name'=>'_aEditableOptions', 'default'=>array('event'=>'dblclick.editable')),
								  								  
								  //these options add data that was set using
								  //add_column_data and add_row_data to the jeditble ajax post 
								  'editable_row_data' => array('name'=>'_aEditableRowData','default'=>array('id')),							  
								  'editable_col_data' => array('name'=>'_aEditableColData','default'=>array())
		  
                            
                                ),
                                
               'table_tools' => array(
               
                                  '_' => 'oTableTools',
                                  
                                  'swf_path' => array('name'=>'sSwfPath', 'default'=>'/assets/swf/copy_cvs_xls_pdf.swf'),
                                  
                                  'buttons' => array('name'=>'aButtons', 'default'=>array(array('sExtends'=>'collection','sButtonText'=>'{lang_export}','aButtons'=>array('csv', 'xls', 'pdf', 'copy'))))
               
                                ),
                                        
                                        
                   'col_vis' => array(
                                    
                                  '_' => 'oColVis',
                                  
                                  'button_text' => array('name'=>'buttonText', 'default'=>'{lang_columns}'),
                                  
								  'align' => array('name'=>'sAlign', 'default'=>'right'),
                                  
                                  'restore' => array('name'=>'bRestore', 'default'=>TRUE),
                                  
                                  'exclude' => array('name'=>'aiExclude', 'default'=>array())
                                    
                                ),
                          
               'col_reorder' => array(
                                    
                                  '_' => 'oColReorder',
                                  
                                  'order' => array('name'=>'aiOrder', 'default'=>''),
                                  
                                  'fixed_columns' => array('name'=>'iFixedColumns', 'default'=>0)
               
                                ),
                                
			   'key_table'  => array(
			   						
									'_' => 'oKeyTable',
									
									'focus' => array('name'=>'focus', 'default'=>array(0,0)),
									
									'focus_class' => array('name'=>'focusClass', 'default'=>'focus'),
									
									'form' => array('name'=>'form', 'default'=>FALSE),
									
									'init_scroll' => array('name'=>'initScroll', 'default'=>TRUE),
									
									'tab_index' => array('name'=>'tabIndex', 'default'=>'')
			   		   
			   					),					
                                
                  'language' => array(
                            
                                  '_' => 'oLanguage',
                                  
                                  'paginate' => array('name'=>'oPaginate', 'default'=>array('sFirst'=>'{lang_first}','sLast'=>'{lang_last}','sNext'=>'{lang_next}','sPrevious'=>'{lang_previous}')),
                                   
                                  'empty_table' => array('name'=>'sEmptyTable', 'default'=>'{lang_empty_table}'),
                                   
                                  'info' => array('name'=>'sInfo', 'default'=>'{lang_info}'),
                                   
                                  'info_empty' => array('name'=>'sInfoEmpty', 'default'=>'{lang_info_empty}'),
                                   
                                  'info_filtered' => array('name'=>'sInfoFiltered', 'default'=>'{lang_info_filtered}'),
                                   
                                  'info_postfix' => array('name'=>'sInfoPostFix', 'default'=>'{lang_info_postfix}'),
                                   
                                  'length_menu' => array('name'=>'sLengthMenu', 'default'=>'{lang_length_menu}'),
                                    
                                  'loading_records' => array('name'=>'sLoadingRecords', 'default'=>'{lang_loading_records}'),
                                    
                                  'processing' => array('name'=>'sProcessing', 'default'=>'{lang_processing}'),
                                  
                                  'search' => array('name'=>'sSearch', 'default'=>'{lang_search}'),
                                  
                                  'zero_records' => array('name'=>'sZeroRecords', 'default'=>'{lang_zero_records}'),
                                  
                                  'url' => array('name'=>'sUrl', 'default'=>'')
                  
                                )                 
                                       
                            ); 
                            
/* End of file config/datatable.php */                      