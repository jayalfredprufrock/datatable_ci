CodeIgniter - jQuery DataTables Wrapper
================
This is a wrapper class/library for Allan Jardine's Datatables Jquery plugin that
implements the javascript array data source method (http://datatables.net/examples/data_sources/js_array.html).
It strives to provide complete control over the javascript plugin from the server-side, and even adds a couple
of nifty features as a result (like CI language support)
  

Author
----------------
Andrew Smiley <jayalfredprufrock>


Requirements
----------------

1. PHP 5.1+
2. CodeIgniter 2.0+
3. jQuery + Datatables Plugin
4. Other Datatables extensions optional (currently supported: ColReorder, ColVis, KeyTable, Editable, and TableTools)
     
     
Usage
----------------

There are lots and lots of options, so the following usage example will focus on some of the more
popular use cases. I hope to expand the documentation substantially when I have some more time, but
doesn't everybody say that :)
	
	// any datatables parameter can be set, with defaults coming from the datatables config file
	// whenever setting options, the datatables option name can be used, or a more friendly codeignited name can be used 
	$this->datatable->set_options(array('layout'=>'R<"H"iTCfr>t<"F"lp>','paginate'=>FALSE));
	
	// load data directly from db result object 
	// passing TRUE to the second parameter automically adds columns for all row keys
	$this->datatable->load_data($this->locations_m->get_locations(), TRUE); 
	
	// explicitly add a column
	// eg. maybe we want to combine the city and state columns into a single column
	// use {columname} to grab the column data for a particular row
	$this->datatable->add_column( array('name'     => 'city_state',
										 'title'    => 'City/State'       
										 'celldata' => '{city} / {state}' //if not provided, would have defaulted 
										 'sortable'	=> FALSE));        //to {city_state} which isn't a valid column
	
	// remove the city and state columns
	// note that their data is stil available in other methods using {city} and {state}
	$this->datatable->remove_columns(array('city','state'));		
			
	// set column options after addition
	$this->datatable->set_column_option('city_state','searchable',FALSE);		
										 		  
										 		  
	// store data in the <tr> element (jQuery element storage)
	// the 3rd parameter can be an index of a particular row you'd like to add data to 
	$this->datatable->add_row_data('id', '{country_id}') 
	
	// adds a class to the first row
	// adds an id to all rows to indicate the country_id
	// all methods return the datatables object so as to allow chaining
	$this->datatable->add_row_class('first-row',0)
					 ->add_row_id('country_{country_id}');
					 

    // run a callback function on a columns values                
  	$this->datatable->add_column_callback('cost','format_money');
  	
  	// hide columns that should not be displayed
  	// the second parameter controls whether it can be toggled back into display
  	// requires the ColVis Plugin
  	$this->datatable->hide_columns('cities', FALSE);		                
  
   	                          
  	// actually generate the html table and jquery datatables initialization js
  	// the third parameter determines whether the markup and js is output directly to the screen
  	// or is returned as an array. The default is FALSE
  	$table = $this->datatable->generate('countries','Supported Countries',FALSE);                               
  
    $table['countries] = HTML Table Markup
    $table['js'] = datatables initialization code
    
    //Restores the library state to the point before columns/rows were added. 
	//Options previously set, including constants, will remain in place. 
    $this->datatable->clear();
    
    //To clear everything, use initialize() 
    $this->datatable->initialize();
    

This is just some of what the library can do - most other Datatable plugins and options are supported. I tried to comment the code
as much as possible (both in the library and config files), and have successfully used this spark on a couple of projects already. 
Still, I imagine there are lots of bugs yet to be found, as the library supports way more use cases than I've used it for thus far, 
and I haven't had much time to properly test things. I'm really hoping the community finds this useful and that there are some people 
out there that can help me clean it up, debug it, and make it that much more useful to all. In short, please fork!