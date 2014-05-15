<?php
function func_reg_solr_form_setting()
{
    register_setting('solr_conf_options','wdm_solr_conf_data');
    register_setting('solr_form_options','wdm_solr_form_data');
    register_setting('solr_res_options','wdm_solr_res_data');
    register_setting('solr_facet_options','wdm_solr_facet_data');
}
function fun_add_solr_settings(){
    $img_url=plugins_url('images/WPSOLRDashicon.png',__FILE__);
    add_menu_page( 'WPSOLR', 'WPSOLR', 'manage_options','solr_settings','fun_set_solr_options',$img_url);
    wp_enqueue_style('dashboard_style',plugins_url('css/dashboard_css.css',__FILE__));
     wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('dashboard_js1',plugins_url('js/dashboard.js',__FILE__),array('jquery','jquery-ui-sortable'));
   
    $plugin_vals = array( 'plugin_url' => plugins_url('images/',__FILE__) );
    wp_localize_script( 'dashboard_js1', 'plugin_data', $plugin_vals );
}

function fun_set_solr_options()
{   
    if(isset($_POST['solr_index_data']))
    {
      $solr=new wp_Solr();
      $res=$solr->get_solr_status();
      if($res==0)
      {
        $solr->delete_documents();
        $val=$solr->index_data();
      
        if(count($val)==1 || $val==1)
        {
             echo "<script type='text/javascript'>
                jQuery(document).ready(function(){
                jQuery('.status_index_message').removeClass('loading');
                jQuery('.status_index_message').addClass('success');
                });
            </script>";
        }
        else{
            echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
                jQuery('.status_index_message').removeClass('loading');
                jQuery('.status_index_message').addClass('warning');
                });
            </script>";
        }
      }
      else{
       echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_index_message').removeClass('loading');
               jQuery('.status_index_message').addClass('warning');
            });
            </script>";
      }
     
    }
    if(isset($_POST['solr_delete_index']))
    {
    $solr=new wp_Solr();
      $res=$solr->get_solr_status();
      if($res==0)
      {
      $val=$solr->delete_documents();
    
      if($val==0){
         echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
               jQuery('.status_del_message').addClass('success');
            });
            </script>";
      }
      else{
        echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
                              jQuery('.status_del_message').addClass('warning');
            });
            </script>";
      }
    
      }
  
      else{
        echo "<script type='text/javascript'>
            jQuery(document).ready(function(){
               jQuery('.status_del_message').removeClass('loading');
                              jQuery('.status_del_message').addClass('warning');
            })
            </script>";
      }
    }
      
 
    ?>
<div class="wdm-wrap">
  <div class="page_title"><h1>WPSOLR Settings </h1></div>
  
  <?php
   global $pagenow;
    if ( isset ( $_GET['tab'] ) ) wpsolr_admin_tabs($_GET['tab']); else wpsolr_admin_tabs('solr_config');
    
     if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
                else $tab = 'solr_config';
    
     switch ( $tab )
     {
        case 'solr_config' :
                        ?>
                        <div id="solr-configuration-tab">
                             <div class='wrapper'>
                                                                    <h4 class='head_div'>Solr Configuration</h4>
                                                                    <div  class="wdm_note">
                                            
                                                                    WPSOLR is compatible with the Solr versions listed at the following page: <a href="http://www.wpsolr.com/releases#1.0" target="__wpsolr">Compatible Solr versions</a>. 

Your first action must be to download the two configuration files (schema.xml, solrconfig.xml) listed in the online release section, and upload them to your Solr instance. Everything is described online.
                                                                     
                                                                    </div>
                                                                    <div class="wdm_row">
                                                                    	<div class="submit">
                                                                    		<a href='admin.php?page=solr_settings&tab=solr_hosting' class="button-primary wdm-save">I uploaded my 2 compatible configuration files to my Solr core >></a>
                                                                         </div>
                                                                    </div>
                                                            </div>
                        </div>
                        <?php
                        break;
        
        case 'solr_hosting' :
                        ?>
                        
                        <div id="solr-hosting-tab">
                            <form action="options.php" method="POST" id='settings_conf_form'>
                                
                                   <?php 
                           
                                settings_fields('solr_conf_options');
                                   
                                
                                $solr_options=get_option('wdm_solr_conf_data');
                               
                               
                                $solr_type=$solr_options['host_type'];
                                 
                                  
                                ?>
                              
                            <!--  <div class="wdm_heading wrapper"><h3>Configure Solr</h3></div>-->
                              <input type='hidden' id='adm_path' value='<?php echo admin_url(); ?>'>
                              <div class='wrapper'>
                                    <h4 class='head_div'>Solr Hosting Choice</h4>
                                    <div class="wdm_row">
                                        <div class='col_left'>Select Solr Hosting</div>
                                        <div class='col_right'>
                                            <input type='radio' name='wdm_solr_conf_data[host_type]' value='self_hosted' class='radio_type' id='self_host'
                                            <?php if($solr_options['host_type']=='self_hosted') {?> checked <?php } ?>
                                            
                                            > Self Hosted <br>
                                            <input type='radio' name='wdm_solr_conf_data[host_type]' value='other_hosted' class='radio_type' id='other_host'
                                           <?php if($solr_options['host_type']=='other_hosted')  {?> checked <?php } ?>
                                            > Cloud Hosting
                                            (Click <a target='_blank' href='http://www.wpsolr.com/solr-certified-hosting-providers'> here </a> to visit our certified Solr hosting providers)
                                            <br>
                                        </div>
                                        <div class="clear"></div>
                                        
                                    </div>
                                    
                               </div>  
                                      
                                    <div id='div_self_hosted' class="wrapper"
                                        <?php if($solr_type=='self_hosted'){ echo "style='display:block'";}
                                                else if($solr_type=='other_hosted') { echo "style='display:none'";}
                                                else {  echo "style='display:none'";} ?> >
                                          <h4 class='head_div'>Solr Hosting Settings</h4>
                                          <div  class="wdm_note">
                                            
                                            <b> If your index url is:</b><br>
                                            <span style='margin-left:10px'>
                                               http://localhost:8983/solr/myindex/select 
                                            </span><br><br />
                                             <b>Then your details will be </b><br>
                                              <span style='margin-left:10px'> <b>Protocol:</b> http</span><br>
                                             <span style='margin-left:10px'> <b>Host:</b> localhost</span><br>
                                             <span style='margin-left:10px'> <b>Port:</b> 8983 </span><br>
                                           <span style='margin-left:10px'>  <b> path:</b> /solr/myindex</span>
                                                                     
                                        </div>
                                        <div class="wdm_row">
                                    <div class='solr_error' style='color:#808080'></div>
                                      </div>
                                        <div class="wdm_row">
                                           <div class='col_left'>Solr Protocol</div>
                              
                                            <div class='col_right'>
                                         
                                                <select name='wdm_solr_conf_data[solr_protocol]' id='solr_protocol'>
                                                        <option value='http'
                                                        <?php if($solr_options['solr_protocol']=='http' || $solr_options['solr_protocol']=='' ) { ?>
                                                        selected
                                                              <?php } ?>
                                                        >http</option>
                                                        <option value='https'
                                                        <?php if($solr_options['solr_protocol']=='https') {?>
                                                         selected
                                                              <?php } ?>
                                                            
                                                            >https</option>
                                                </select>
                                                
                                            <span class='ghost_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="wdm_row"> 
                                            <div class='col_left'>Solr Host</div>
                                  
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_host]' id='solr_host'
                                                                value="<?php echo empty($solr_options['solr_host']) ? 'localhost' : $solr_options['solr_host'];?>" >
                                            <span class='host_err'></span></div>
                                            <div class="clear"></div>
                                        </div>  
                                        <div class="wdm_row"> 
                                            <div class='col_left'>Solr Port</div>
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_port]'  id='solr_port'
                                                            value="<?php echo empty($solr_options['solr_port']) ? '8983' : $solr_options['solr_port'];?>" >
                                            <span class='port_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                            </div>
                                        <div class="wdm_row">
                                            <div class='col_left'>Solr Path</div>
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_path]'  id='solr_path'
                                                            value="<?php echo empty($solr_options['solr_path']) ? '/solr' : $solr_options['solr_path'];?>">
                                            <span class='path_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                         </div>
                                        <div class='wdm_row'>
                                         <div class="submit">
                                            <!--<input name="save_selected_options" id='save_selected_options' type="submit" class="button-primary wdm-save" value="<?php //esc_attr_e('Save Changes'); ?>" />-->
                                            <input name="check_solr_status" id='check_solr_status' type="button" class="button-primary wdm-save" value="Check Solr Status, Then Save" />
                                            <span   >
                                                <img src='<?php echo plugins_url('images/gif-load_cir.gif',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-load' />
                                                <img src='<?php echo plugins_url('images/success.png',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-succ' />
                                                <img src='<?php echo plugins_url('images/warning.png',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-err'/>
                                            </span>   
                                         </div>
                                        </div>
                                         <div class="clear"></div>
                                    </div>
                              
                              <div id='hosted_on_other' class="wrapper" <?php if($solr_type=='self_hosted'){ echo "style='display:none'";}
                                                else if($solr_type=='other_hosted') { echo "style='display:block'";}
                                                else {  echo "style='display:none'";} ?>>
                                        <h4 class='head_div'>Solr Hosting Connection</h4>
                                        <div  class="wdm_note">
                                            
                                            <b> If your index url is:</b><br>
                                            <span style='margin-left:10px'> https://877d83f3-1055-4086-9fe6-cecd1b48411f-index.solrdata.com:8983/solr/e86f82a682564c23b7802b6827f3ccd4.24b7729e02dc47d19c15f1310098f93f/select
                                            </span><br><br />
                                             <b>Then your details will be </b><br>
                                             <span style='margin-left:10px'> <b>Protocol:</b> https</span><br>
                                             <span style='margin-left:10px'> <b>Host:</b>  877d83f3-1055-4086-9fe6-cecd1b48411f-index.solrdata.com</span><br>
                                             <span style='margin-left:10px'> <b>Port:</b> 8983 </span><br>
                                           <span style='margin-left:10px'>  <b> path:</b> /solr/e86f82a682564c23b7802b6827f3ccd4.24b7729e02dc47d19c15f1310098f93f</span>
                                                                     
                                        </div>
                                         <div class="wdm_row">
                                    <div class='solr_error' ></div>
                                      </div>
                                        <div class="wdm_row">
                                            <div class='col_left'>Solr Protocol</div>
                              
                                            <div class='col_right'>
                                                           
                                                            
                                                            <select name='wdm_solr_conf_data[solr_protocol_goto]' id='gtsolr_protocol'>
                                                                <option value='http'
                                                        <?php if($solr_options['solr_protocol_goto']=='http') {?>
                                                        selected
                                                              <?php } ?>
                                                            
                                                            >http</option>
                                                        <option value='https'
                                                        <?php if($solr_options['solr_protocol_goto']=='https' || $solr_options['solr_protocol_goto']==null ) {?>
                                                        selected
                                                              <?php } ?>
                                                        >https</option>
                                                        
                                                </select>
                                                    <span class='ghost_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                         </div>
                                         <div class="wdm_row">
                                            <div class='col_left'>Solr Host</div>
                              
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_host_goto]' id='gtsolr_host'
                                                            value="<?php echo empty($solr_options['solr_host_goto']) ? 'localhost' : $solr_options['solr_host_goto'];?>" >
                                                    <span class='ghost_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="wdm_row">
                                            <div class='col_left'>Solr Port</div>
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_port_goto]' id='gtsolr_port'
                                                            value="<?php echo empty($solr_options['solr_port_goto']) ? '8983' : $solr_options['solr_port_goto'];?>" >
                                            <span class='gport_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                            </div>
                                        <div class="wdm_row">
                                            <div class='col_left'>Solr Path</div>
                                            <div class='col_right'><input type='text' name='wdm_solr_conf_data[solr_path_goto]' id='gtsolr_path'
                                                            value="<?php echo empty($solr_options['solr_path_goto']) ? '/solr' : $solr_options['solr_path_goto'];?>">
                                            <span class='gpath_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="wdm_row">              
                                            <div class='col_left'>Key</div>
                                            <div class='col_right'>
                                                    <input type='text' name='wdm_solr_conf_data[solr_key_goto]' id='gtsolr_key'
                                                            value="<?php echo empty($solr_options['solr_key_goto']) ? '' : $solr_options['solr_key_goto'];?>">
                                               <span class='gkey_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="wdm_row">  
                                            <div class='col_left'>Secret</div>
                                            <div class='col_right'>
                                                    <input type='text' name='wdm_solr_conf_data[solr_secret_goto]' id='gtsolr_secret'
                                                            value="<?php echo empty($solr_options['solr_secret_goto']) ? '' : $solr_options['solr_secret_goto'];?>">
                                                <span class='gsec_err'></span>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                     	<div class="wdm_row">
                                         <div class="submit">
                                            <!--<input name="save_selected_options_third" type="submit" class="button-primary wdm-save" value="<?php //esc_attr_e('Save Changes'); ?>" />-->
                                            <input name="check_solr_status_third" id='check_solr_status_third' type="button" class="button-primary wdm-save" value="Check Solr Status, Then Save" /> <span ><img src='<?php echo plugins_url('images/gif-load_cir.gif',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-load' >
                                            
                                             <img src='<?php echo plugins_url('images/success.png',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-succ' />
                                                <img src='<?php echo plugins_url('images/warning.png',__FILE__)?>' style='height:18px;width:18px;margin-top: 10px;display: none' class='img-err'/></span>                        
                                            </div>
                                        </div>
                                         <div class="clear"></div>
                                </div>
                                  
                                
                                
                            </form>
                        </div>
                                
                        
                        <?php
                        break;
                        case 'solr_option':
                     ?>
                                 <div id="solr-option-tab">
                                    
                                        <?php
                                      if ( isset ( $_GET['tab'] ) )
                                            {
                                                if($_GET['tab'] =='solr_option')
                                                {
                                                        if ( isset ( $_GET['subtab'] ) ) wpsolr_admin_sub_tabs($_GET['subtab']); else wpsolr_admin_sub_tabs('index_opt');
                                                }
                                            }
                        
                                            if ( isset ( $_GET['subtab'] ) ) $subtab = $_GET['subtab'];
                                            else $subtab = 'index_opt';
                                           
                                            switch ( $subtab )
                                            {
                                                        case 'result_opt':
                                                             
                                                             
                                                    ?>
                                                    <div id="solr-results-options" class="wdm-vertical-tabs-content">
                                                                 <form action="options.php" method="POST" id='res_settings_form'>
                                                                    <?php
                                                                    settings_fields('solr_res_options');
                                                            $solr_res_options=get_option('wdm_solr_res_data',array('default_search'=>0,'res_info'=>'0','spellchecker'=>'0'
				    
                                    ));
                                                            ?>
                                                                    
                                                                <div class='wrapper'>
                                                                <h4 class='head_div'>Result Options</h4>
                                                                <div  class="wdm_note">
                                            
                                                                  In this section, you will choose how to display the results returned by a query to your Solr instance.
                                                                    
                                                                    </div>
                                                                <div class="wdm_row">
                                                                    <div class='col_left'>Display suggestions (Did you mean?)</div>
                                                                    <div class='col_right'>
                                                                        <input type='checkbox' name='wdm_solr_res_data[<?php echo 'spellchecker'?>]' value='spellchecker'
                                                                        <?php checked( 'spellchecker', $solr_res_options['spellchecker'] );?>> 
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                <div class="wdm_row">
                                                                    <div class='col_left'>Display number of results and current page</div>
                                                                    <div class='col_right'>
                                                                        <input type='checkbox' name='wdm_solr_res_data[res_info]' value='res_info'
                                                                                                                          <?php checked( 'res_info', $solr_res_options['res_info'] );?>> 
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>    
                                                                <div class="wdm_row">  
                                                                    <div class='col_left'>Replace WordPress Default Search</div>
                                                                    <div class='col_right'>
                                                                        <input type='checkbox' name='wdm_solr_res_data[default_search]' value='1'
                                                                                                                       <?php checked( '1', $solr_res_options['default_search'] );?>> 
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                <div class="wdm_row">        
                                                                    <div class='col_left'>No. of results per page</div>
                                                                    <div class='col_right'>
                                                                        <input type='text' id='number_of_res' name='wdm_solr_res_data[no_res]' placeholder="Enter a Number"
                                                                        value="<?php echo empty($solr_res_options['no_res']) ? '20' : $solr_res_options['no_res'];?>"> <span class='res_err'></span><br>
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                <div class="wdm_row">   
                                                                    <div class='col_left'>No. of values to be displayed by facets</div>
                                                                    <div class='col_right'>
                                                                        <input type='text' id='number_of_fac' name='wdm_solr_res_data[no_fac]' placeholder="Enter a Number"
                                                                        value="<?php echo empty($solr_res_options['no_fac']) ? '20' : $solr_res_options['no_fac'];?>"><span class='fac_err'></span> <br>
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                <div class='wdm_row'>
                                                                <div class="submit">
                                                                   <input name="save_selected_options_res_form" id="save_selected_res_options_form" type="submit" class="button-primary wdm-save" value="Save Options" />
                                                                  
                                                                   
                                                                   </div>
                                                               </div>
                                                            </div>
                                                            
                                                                 </form>
                                                        </div>
                                                        <?php
                                                        break;
                                                    case 'index_opt':
                                                        
                                                             
                                                        $posts=get_post_types();
                                                            $args=array(
                                                                'public'   => true,
                                                                '_builtin' => false
                                                    
                                                                        );
                                                            $output = 'names'; // or objects
                                                            $operator = 'and'; // 'and' or 'or'
                                                            $taxonomies=get_taxonomies($args,$output,$operator);
                                                            global $wpdb;
                                                            $limit = (int) apply_filters( 'postmeta_form_limit', 30 );
                                                            $keys = $wpdb->get_col( "
                                                                    SELECT meta_key
                                                                    FROM $wpdb->postmeta
                                                                    WHERE meta_key!='bwps_enable_ssl' 
                                                                    GROUP BY meta_key
                                                                    HAVING meta_key NOT LIKE '\_%'
                                                                    ORDER BY meta_key" );
                                                            $post_types=array();
                                                            foreach($posts as $ps)
                                                            {
                                                                if($ps!='attachement' && $ps!='revision' && $ps!='nav_menu_item' )
                                                                    array_push($post_types,$ps);
                                                            }
                                                            
                                                   
                                                    ?>
                                                    <div id="solr-indexing-options" class="wdm-vertical-tabs-content">
                                                    <form action="options.php" method="POST" id='settings_form'>
                                                        <?php
                                                        settings_fields('solr_form_options');
                                                            $solr_options=get_option('wdm_solr_form_data',array('comments'=>0,'p_types'=>'','taxonomies'=>'','cust_fields'=>''));
                                                            ?>
                                                            
                                                            
                                                             <div class='indexing_option wrapper'>
                                                                  <h4 class='head_div'>Indexing Options</h4>
                                                                  <div  class="wdm_note">
                                            
                                                                   In this section, you will choose among all the data stored in your Wordpress site, which you want to load in your Solr index.
                                                                     
                                                                    </div>
                                                                      <div class="wdm_row">
                                                                      <div class='col_left'>Post types to be indexed</div>
                                                                      <div class='col_right'>
                                                                      <input type='hidden'name='wdm_solr_form_data[p_types]' id='p_types'>
                                                                      <?php
                                                                          $post_types_opt=$solr_options['p_types'];
                                                                          foreach($post_types as $type)
                                                                             {
                                                                                 if($type!='attachment')
                                                                                 {
                                                                                 ?>
                                                                                 <input type='checkbox' name='post_tys' value='<?php echo $type ?>'
                                                                                 <?php if(strpos($post_types_opt,$type)!==false) { ?> checked <?php } ?>> <?php echo $type ?> <br> 
                                                                              <?php
                                                                              }
                                                                             }
                                                                       ?>
                                                              
                                                                      </div>
                                                                      <div class="clear"></div>
                                                                      </div>
                                                                      <div class="wdm_row">
                                                                          <div class='col_left'>Custom taxonomies to be indexed</div>
                                                                          <div class='col_right'>
                                                                          <div class='cust_tax'><!--new div class given-->
                                                                          <input type='hidden'name='wdm_solr_form_data[taxonomies]' id='tax_types'>
                                                                                  <?php
                                                                                  $tax_types_opt=$solr_options['taxonomies'];
                                                                                  if(count($taxonomies)>0)
                                                                                  {
                                                                                    foreach($taxonomies as $type)
                                                                                   {
                                                                                       ?>
                                                                                      
                                                                                       <input type='checkbox' name='taxon' value='<?php echo $type."_str" ?>'
                                                                                       <?php if(strpos($tax_types_opt,$type."_str")!==false) { ?> checked <?php } ?>//end here
                                                                                       > <?php echo $type ?> <br>
                                                                                       <?php
                                                                                    }
                                                                                                    
                                                                                  }
                                                                                  else{
                                                                                   echo 'None';
                                                                                  }      ?>
                                                                          </div>
                                                                      </div>
                                                                      <div class="clear"></div>
                                                                      </div>
                                                                      <div class="wdm_row">
                                                                      <div class='col_left'>Custom Fields to be indexed</div>
                                                          
                                                                      <div class='col_right'>
                                                                          <input type='hidden' name='wdm_solr_form_data[cust_fields]' id='field_types'>
                                                                          <div class='cust_fields'><!--new div class given-->
                                                                          <?php
                                                                              $field_types_opt=$solr_options['cust_fields'];
                                                                              if(count($keys)>0)
                                                                              {
                                                                                  foreach($keys as $key)
                                                                                  {
                                                                                  ?>
                                                                                 
                                                                                  <input type='checkbox' name='cust_fields' value='<?php echo $key."_str" ?>'
                                                                                  <?php if(strpos($field_types_opt,$key."_str")!==false) { ?> checked <?php } ?>> <?php echo $key ?> <br>
                                                                                  <?php
                                                                                  }
                                                                              
                                                                              }
                                                                              else    
                                                                                  echo 'None';
                                                                          ?>
                                                                          </div> 
                                                                          </div>
                                                                          <div class="clear"></div>
                                                                       </div>
                                                                       <div class="wdm_row">
                                                                              <div class='col_left'>Index Comments</div>
                                                                              <div class='col_right'>
                                                                                      <input type='checkbox' name='wdm_solr_form_data[comments]' value='1'  <?php checked( '1', $solr_options['comments'] );?>> 
                                                                          
                                                                              </div>
                                                                              <div class="clear"></div>
                                                                          </div>
                                                                          <div class="wdm_row">
                                                                              <div class='col_left'>Exclude items(Posts,Pages,...)</div>
                                                                              <div class='col_right'>
                                                                                      <input type='text' name='wdm_solr_form_data[exclude_ids]' placeholder="Comma separated ID's list"
                                                                                      value="<?php echo empty($solr_options['exclude_ids']) ? '' : $solr_options['exclude_ids'];?>"> <br>
                                                                                      (Comma separated ids list)
                                                                               </div>
                                                                              <div class="clear"></div>
                                                                            </div>
                                                                          <div class='wdm_row'>
                                                                    <div class="submit">
                                                                   <input name="save_selected_index_options_form" id="save_selected_index_options_form" type="submit" class="button-primary wdm-save" value="Save Options" />
                                                                  
                                                                   
                                                                   </div>
                                                               </div>
                                                                  
                                                              </div>
                                                    	</form>
                                                    </div>
                                                    <?php   
                                                        break;
                                                    case 'facet_opt':
                                                           $solr_options=get_option('wdm_solr_form_data');
                                                           $checked_fls=$solr_options['cust_fields'].','.$solr_options['taxonomies'];
                                                           
                                                           $checked_fields=array();
                                                          $checked_fields= explode(',',$checked_fls);
                                                           $img_path=plugins_url('images/plus.png',__FILE__);
                                                            $minus_path=plugins_url('images/minus.png',__FILE__);
                                                            $built_in=array('Type','Author','Categories','Tags');
                                                            $built_in=array_merge($built_in,$checked_fields);
                                                           ?>
                                                        <div id="solr-facets-options" class="wdm-vertical-tabs-content">
                                                        <form action="options.php" method="POST" id='fac_settings_form'>
                                                           <?php
                                                            settings_fields('solr_facet_options');
                                                            $solr_fac_options=get_option('wdm_solr_facet_data');
                                                            $selected_facets_value=$solr_fac_options['facets'] ;
                                                            if($selected_facets_value!='')
                                                            $selected_array=explode(',',$selected_facets_value);
                                                           else
                                                           $selected_array=array();
                                                            ?>
                                                            <div class='wrapper'>
                                                                    <h4 class='head_div'>Facets Options</h4>
                                                                    <div  class="wdm_note">
                                            
                                                                     In this section, you will choose which data you want to display as facets in your search results. Facets are extra filters usually seen in the left hand side of the results, displayed as a list of links. You can add facets only to data you've selected to be indexed.
                                                                     
                                                                    </div>
                                                           			<div class="wdm_note">
                                            							<h4>Instructions</h4>
                                                                        <ul class="wdm-instructions">
                                                                        	<li>Click on the 'Plus' icon to add the facets</li>
                                                                            <li>Click on the 'Minus' icon to remove the facets</li>
                                                                            <li>Sort the items in the order you want to display them by dragging and dropping them at the desired plcae</li>
                                                                        </ul>
                                                                    </div>
                                                            
                                                            <div class="wdm_row">
                                                                <div class='avail_fac'>
                                                                    <h4>Available items for facets</h4>
                                                                    <input type='hidden' id='checked_options' name='checked_options' value='<?php echo $checked_fls ?>'>
                                                                                        <input type='hidden' id='select_fac' name='wdm_solr_facet_data[facets]' value='<?php echo $selected_facets_value ?>'>
                                                                                        
                                                                                        
                                                                                         <ul id="sortable1" class="connectedSortable">
                                                                                            <?php
                                                                                            if($selected_facets_value!='')
                                                                                            {
                                                                                             foreach($selected_array as $selected_val)
                                                                                            
                                                                                            {
                                                                                                if($selected_val!='')
                                                                                                {
                                                                                                if(substr($selected_val,(strlen($selected_val)-4),strlen($selected_val))=="_str")
                                                                                            $dis_text=substr($selected_val,0,(strlen($selected_val)-4));
	
                                                                                            else
	
                                                                                                 $dis_text=$selected_val;
                                                                                                 
                                                                                                 
                                                                                                  echo "<li id='$selected_val' class='ui-state-default facets facet_selected'>$dis_text
                                                                                                    <img src='$img_path'  class='plus_icon' style='display:none'>
                                                                                                <img src='$minus_path' class='minus_icon' style='display:inline' title='Click to Remove the Facet'></li>";
                                                                                                }
                                                                                            }
                                                                                            }
                                                                                            foreach($built_in as $built_fac)
                                                                                            {
                                                                                              if($built_fac!='')
                                                                                                {
                                                                                              $buil_fac=strtolower($built_fac);
                                                                                              if(substr($buil_fac,(strlen($buil_fac)-4),strlen($buil_fac))=="_str")
                                                                                            $dis_text=substr($buil_fac,0,(strlen($buil_fac)-4));
	
                                                                                            else
	
                                                                                                 $dis_text=$buil_fac;
                                                                                              
                                                                                                if(!in_array($buil_fac,$selected_array))
                                                                                                   {
                                                                                                  
                                                                                                   echo "<li id='$buil_fac' class='ui-state-default facets'>$dis_text
                                                                                                    <img src='$img_path'  class='plus_icon' style='display:inline' title='Click to Add the Facet'>
                                                                                                <img src='$minus_path' class='minus_icon' style='display:none'></li>";
                                                                                                }
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                            
                                                                                            
                                                                                        </ul>
                                                                </div>
                                                                
                                                                <div class="clear"></div>
                                                                </div>
                                                            <div class='wdm_row'>
                                                                    <div class="submit">
                                                                   <input name="save_selected_options_form" id="save_selected_options_form" type="submit" class="button-primary wdm-save" value="Save Options" />
                                                                  
                                                                   
                                                                   </div>
                                                               </div>
                                                             </div>
                                                        	</form>
                                                        </div>
                                                        <?php
                                                        break;
                                                    
                                                    
                                            }
                                        
                                        ?>
                                    
                                 </div>
                    <?php
                    break;
                case 'solr_operations':
                            ?>
                        
                            <div id="solr-operations-tab">
                            <form method='post' id='solr_actions'>
                            <div class='wrapper'>
                            <h4 class='head_div'>Solr Operations</h4>
                             <div  class="wdm_note">
                               <b>
                                <?php
                                $solr_options=get_option('wdm_solr_conf_data');
         
                                    if($solr_options['host_type']=='self_hosted')
                                      {
                                         $doc_count=get_option('solr_docs_in_self_index');
                                         if($doc_count!='')
                                         echo $doc_count;
                                         else
                                         echo 0;
                                      }
                                      
                                      else
                                       {
                                         $doc_count=get_option('solr_docs_in_cloud_index');
                                         if($doc_count!='')
                                         echo $doc_count;
                                         else
                                         echo 0;
                                       }
           
                                ?>
                                </b> documents are currently in your index
                            
                                                                    </div>
                                <div class="wdm_row">
                                    <div class="submit">
                                        <input name="solr_index_data" type="submit" class="button-primary wdm-save" id='solr_index_data' value="Load Data" />
                                        <span class='status_index_message' >
                              
                                        </span>
                            
                                        <input name="solr_delete_index" id="solr_delete_index" type="submit" class="button-primary wdm-save" value="Delete Data" />
                            
                                        <span class='status_del_message'>
                              
                                        </span>
                                    </div>
                                 </div>
                            </div>
                            </form>
                            </div>
                        <?php
                        break;
       
            
     }
      
                ?>
  
  
 
    
   </div> 
    <?php

    
}

function wpsolr_admin_tabs( $current = 'solr_config' ) {
    $tabs = array( 'solr_config' => 'Solr Configuration', 'solr_hosting' => 'Solr Hosting', 'solr_option' => 'Solr Options','solr_operations'=>'Solr Operations');
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='admin.php?page=solr_settings&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}


function wpsolr_admin_sub_tabs( $current = 'index_opt' ) {
    $tab=$_GET['tab'];
    $subtabs = array( 'index_opt' => 'Indexing Options', 'result_opt' => 'Result Options', 'facet_opt' => 'Facets Options');
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper wdm-vertical-tabs">';
    foreach( $subtabs as $subtab => $name ){
        $class = ( $subtab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='admin.php?page=solr_settings&tab=$tab&subtab=$subtab'>$name</a>";

    }
    echo '</h2>';
}

