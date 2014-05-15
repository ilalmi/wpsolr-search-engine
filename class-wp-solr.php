<?php
class wp_Solr {
    
    protected $config;
    public $client;
    public $select_query;
    public function __construct()
    {
        
        $path = plugin_dir_path( __FILE__).'vendor/autoload.php';
        require_once $path;
        $solr_options=get_option('wdm_solr_conf_data');
         
        if($solr_options['host_type']=='self_hosted')
        {
                if($solr_options['solr_host']!='')
                    $host=$solr_options['solr_host'];
               
                if($solr_options['solr_path']!='')
                    $path=$solr_options['solr_path'];
               
                    
                if($solr_options['solr_port']!='')
                    $port=$solr_options['solr_port'];
               
                   $config = array(
                                    "endpoint" =>
                                    array("localhost" => array  (
                                                                    "host"=>$host,
                                                                    "port"=>$port,
                                                                    "path"=>$path)
                                                                )
                                        );
         }
         else if( $solr_options['host_type']=='other_hosted')
         {
            
                if($solr_options['solr_host_goto']!='')
                    $host=$solr_options['solr_host_goto'];
               
                if($solr_options['solr_path_goto']!='')
                    $path=$solr_options['solr_path_goto'];
              
                    
                if($solr_options['solr_port_goto']!='')
                    $port=$solr_options['solr_port_goto'];
              
                
                $username=$solr_options['solr_key_goto'];
                $password=$solr_options['solr_secret_goto'];
                 $config = array(
                                'endpoint' => array(
                                    'localhost1' => array ('host' =>  "$host",
                                       'username' => "$username",
                                       'password' =>"$password",
                                       'port' => "$port",
                                       'path'=>"$path"
                                    )
                                )
                                );          
         }
        
        $this->client = new Solarium\Client($config);
   
    }
    
    public function get_solr_status()
    {
        $solr_options=get_option('wdm_solr_conf_data');
         
        if($solr_options['host_type']=='self_hosted')
        {
        $client=$this->client;
        
        $ping = $client->createPing();
        
        try{
                $result = $client->ping($ping);
                $res=$result->getStatus();
                return $res;
            }
        catch(Exception $e)
            {
                return 1;
            }
        }
        else{
            
             if($solr_options['solr_host_goto']!='')
                    $host=$solr_options['solr_host_goto'];
               
                if($solr_options['solr_path_goto']!='')
                    $path=$solr_options['solr_path_goto'];
              
                    
                if($solr_options['solr_port_goto']!='')
                    $port=$solr_options['solr_port_goto'];
              
                $protocol=$solr_options['solr_protocol_goto'];
                $username=$solr_options['solr_key_goto'];
                $password=$solr_options['solr_secret_goto'];
                
            $url="$protocol://".$host.":".$port.$path."/admin/ping";
            $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_USERPWD ,"$username:$password");
                                                                           
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                if( ($res=curl_exec($ch)) === false)
                    {
                       
                         $search_result[]= 'Curl error: ' . curl_error($ch);
                    }
                    else
                    {
                        if(strpos($res,'OK')!=false)
                            return 0;
                        else
                            {
                                return 1;
                            }
                    }
        }

    }
     public function delete_documents()
    {
        $solr_options=get_option('wdm_solr_conf_data');
         
         if($solr_options['host_type']=='self_hosted')
         {
            $client=$this->client;
            $deleteQuery = $client->createUpdate();
            $deleteQuery->addDeleteQuery('*:*');
            $deleteQuery->addCommit();
            $result= $client->update($deleteQuery);
            $res=$result->getData();
                      
             update_option('solr_docs_in_self_index',0);
           
            return isset($res['status'])?$res['status']:'';
            
         }
         else{
               if($solr_options['solr_host_goto']!='')
                    $host=$solr_options['solr_host_goto'];
               
                if($solr_options['solr_path_goto']!='')
                    $path=$solr_options['solr_path_goto'];
              
                    
                if($solr_options['solr_port_goto']!='')
                    $port=$solr_options['solr_port_goto'];
              
                 $protocol=$solr_options['solr_protocol_goto'];
                $username=$solr_options['solr_key_goto'];
                $password=$solr_options['solr_secret_goto'];
                
                
                $url="$protocol://".$host.":".$port.$path."/update/?commit=true" ;
                $del_var='<delete><query>id:*</query></delete>';
                
                $ch = curl_init();
			
 
			$header = array("Content-type:text/xml; charset=utf-8");
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch,CURLOPT_USERPWD ,"$username:$password");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $del_var);
                            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
 
			$data = curl_exec($ch);
                        
			if (curl_errno($ch)) {
			   return 1 ;
			} else {
                          
       
                         update_option('solr_docs_in_cloud_index',0);
         
			   curl_close($ch);
			  return 0;
			}
         }
    }
    /*Returns array of result
    * Different blocks are written for self host and other hosted index
    * Returns array of result
    * Result[0]= Spellchecker-Did you mean
    * Result[1]= Array of Facets
    * Result[2]= No of documents found
    * Result[3]= Array of documents
    * Result[4]=Result info
    * */
    public function get_search_results($term,$facet_options,$start,$sort)
    {
        
        $output=array();
        $search_result=array();
      
            $solr_options=get_option('wdm_solr_conf_data');
            $ind_opt=get_option('wdm_solr_form_data');
            $res_opt=get_option('wdm_solr_res_data');
            $fac_opt=get_option('wdm_solr_facet_data');
            
        $number_of_res=$res_opt['no_res'];
         if($number_of_res=='')
                    $number_of_res=20;
                    
        $field_comment=isset($ind_opt['comments'])?$ind_opt['comments']:'';
        $options=$fac_opt['facets'];
         
        if($solr_options['host_type']=='self_hosted')
        {
        
            $msg='';
            $client=$this->client;
            $term=str_replace(' ','\ ',$term);
          
            $query = $client->createSelect();
            //$helper = $query->getHelper();
            $query->setQuery($term);
            $query->setFields(array('id','title','content','author','displaydate','categories','numcomments','comments','type','permalink'));
            if($sort!=null)
            {
                if($sort=='new')
                {
                    $sort_field='date';
                    $sort_value=$query::SORT_DESC;
                }
                else if($sort=='old')
                {
                    $sort_field='date';
                    $sort_value=$query::SORT_ASC;
                }
                else if($sort=='mcomm')
                {
                    $sort_field='numcomments';
                    $sort_value=$query::SORT_DESC;
                }
                else if($sort=='lcomm')
                {
                    $sort_field='numcomments';
                    $sort_value=$query::SORT_ASC;
                }
                
                
                
            }
            else
            {
                    $sort_field='id';
                    $sort_value=$query::SORT_DESC;
            }
                
                $query->addSort($sort_field,$sort_value);
                $query->setQueryDefaultOperator('AND');
           
          
        
            if( $res_opt['spellchecker']=='spellchecker')
            {
                  
                $spellChk = $query->getSpellcheck();
                $spellChk->setCount(10);
                $spellChk->setCollate(true);
                $spellChk->setExtendedResults(true);
                $spellChk->setCollateExtendedResults(true);
                $resultset = $client->select($query);
          
                $spell_msg='';
                $spellChkResult = $resultset->getSpellcheck();
                if (!$spellChkResult->getCorrectlySpelled())
                {
                    $collations = $spellChkResult->getCollations();
                    $term='';
                    foreach($collations as $collation)
                    {
                        foreach($collation->getCorrections() as $input => $correction)
                        {
                            $term.=  $correction ;
                        }
                    }
                    
                    if(strlen($term)>0)
                    {
                        $err_msg='Did you mean: <b>'.$term.'</b><br />';
                    
                        $query->setQuery($term);
           
                    }
                 $search_result[] =$err_msg;
                 
                }
                else
                {
                    $search_result[] =0;
                }
                       
            }
            else
            {
                 $search_result[] =0;
            }
           $fac_count= $res_opt['no_fac'];
                if($fac_count=='')
                    $fac_count=20;
                    
            if($options!='')
            {
                
                 $facets_array=explode(',',$fac_opt['facets']);
                
                $facetSet = $query->getFacetSet();
                $facetSet->setMinCount(1);
               // $facetSet->;
                 foreach($facets_array as $facet)
                {
                    $fact=strtolower($facet);
                     
                        $facetSet->createFacetField("$fact")->setField("$fact")->setLimit($fac_count);
                     
                }
            }
              $resultset = $client->select($query);
             if($options!='')
            {
             foreach($facets_array as $facet)
                {
                    
                  $fact=strtolower($facet);
                        $facet_res = $resultset->getFacetSet()->getFacet("$fact");
                
                        foreach($facet_res as $value => $count)
                        {
                                $output[$facet][] =array($value , $count );
                        }
                
                        
                }
                    $search_result[]=$output;
           
            }
            else
            {
                 $search_result[]=0;
            }
           
            $bound='';
             if($facet_options!=null || $facet_options!='')
            {
                 $f_array=explode(':',$facet_options);
                 
                $fac_field=strtolower($f_array[0]);
                $fac_type=isset($f_array[1])?$f_array[1]:'';
               
                
                        if($fac_field!='' && $fac_type!='' )
                    {
                        $fac_fd="$fac_field";
                        $fac_tp=str_replace(' ','\ ',$fac_type);
                       
                         $query->addFilterQuery(array('key'=>"$fac_fd", 'query'=>"$fac_fd:$fac_tp"));  
                    }
                    
                   if( isset($f_array[2]) && $f_array[2]!='')
                   $bound=$f_array[2];
              
            }
            
             
                
                if($start==0 || $start==1)
                  {
                    $st=0;
                   
                   }
                   else
                   {
                    $st=(($start-1)*$number_of_res);
                   
                   }
                
               if($bound!='' && $bound<$number_of_res)
               {
                
                 $query->setStart($st)->setRows($bound);
                  
               }
               else{
                    $query->setStart($st)->setRows($number_of_res);
                    
               }
            
             
             $resultset = $client->select($query);
             
            $found = $resultset->getNumFound();
            
             if($bound!='')
               {
                    $search_result[]=$bound;
                       
                            
               }
               else{
                 $search_result[]=$found;
                   
               }
            
                    $hl = $query->getHighlighting();
                    $hl->getField('title')->setSimplePrefix('<b>')->setSimplePostfix('</b>');
                   $hl->getField('content')->setSimplePrefix('<b>')->setSimplePostfix('</b>');
                
             
                    if($field_comment==1)
                        $hl->getField('comments')->setSimplePrefix('<b>')->setSimplePostfix('</b>');
                     
                     $resultSet='';
                      $resultSet =  $client->select($query);
                      
                      
                    $results=array();
                   $highlighting = $resultSet->getHighlighting();
                    
                  
                   $i=1;
                   $cat_arr=array();
                 foreach($resultset as $document)
                {
                        $id=$document->id;
                        $pid=$document->PID;
                        $name=$document->title;
                        $content=$document->content;
                        
                        $no_comments=$document->numcomments;
                        if($field_comment==1)
                            $comments=$document->comments;
                        $date=date('m/d/Y', strtotime($document->displaydate));
                        
                        if (property_exists($document, "categories")) 
                            $cat_arr=$document->categories;
                        
                        
                        $cat=implode(',',$cat_arr);
                        $auth=$document->author;
                        
                        $cont=substr($content,0,100);
                        
                     $url=get_permalink($id);
                        
                         $highlightedDoc = $highlighting->getResult($document->id);
                         $cont_no=0;$comm_no=0;
                        if($highlightedDoc)
                        {
                           
                            foreach($highlightedDoc as $field => $highlight)
                            {
                                $msg='';
                                if($field=='title')
                                {
                                   $name=implode(' (...) ', $highlight);
                                 
                                }
                               
                                else if($field=='content')
                                {
                                    $cont=implode(' (...) ', $highlight);
                                  $cont_no=1;
                                }
                                else if($field=='comments')
                                {
                                   $comments=implode(' (...) ', $highlight);
                                   $comm_no=1;
                                }
                                
                            }
                            
                            
                        }
                         $msg='';
                                    $msg.="<div id='res$i'><div class='p_title'><a href='$url' target='_blank'>$name</a></div>";
                                    if($cont_no==1)
                                    $msg.="<div class='p_content'>$cont - <a href='$url' target='_blank'>Content match</a></div>";
                                    else
                                    $msg.="<div class='p_content'>$cont</div>";
                                    if($comm_no==1)
                                    $msg.="<div class='p_comment'>".$comments. "-<a href='$url' target='_blank'>Comment match</a></div>";
                                    $msg.="<div class='p_misc'>
                                            By <span class='pauthor'>$auth</span>
                                            in <span class='pcat'>$cat</span>
                                            <span class='pdate'>$date</span>
                                            <span class='pcat'> $no_comments -comments</span>
                                            </div></div><hr>";
                                              array_push($results,$msg);
                       $i=$i+1;
                 }
            //  $msg.='</div>';
            
            
            if(count($results)<0)
                $search_result[]=0;
            else
                $search_result[]=$results;
      
            $fir=$st+1;
          
              $last=$st+$number_of_res; 
             if($last>$found)
                $last=$found;  
            else
                $last=$st+$number_of_res;  
        
            $search_result[]="<span class='infor'>Showing $fir to $last results out of $found</span>";
         }
         else{
            
           // $term=solr_escape($term);
                $user=$solr_options['solr_key_goto'];
                $pwd=$solr_options['solr_secret_goto'];
                $protocol=$solr_options['solr_protocol_goto'];
                $fl='id,title,PID,content,author,displaydate,categories,numcomments,comments,type,permalink';
                $tp='wt=json&defType=dismax';
                if(strpos($term,"'")==false)
                    $tp.='&q.op=AND';
                $sort_field='';
                if($sort!=null)
                {
                        if($sort=='new')
                        {
                            $sort_field='date%20desc';
                        }
                        else if($sort=='old')
                        {
                            $sort_field='date%20asc';
                            
                        }
                        else if($sort=='mcomm')
                        {
                            $sort_field='numcomments%20desc';
                            
                        }
                        else if($sort=='lcomm')
                        {
                            $sort_field='numcomments%20asc';
                           
                        }
                }
                else
                {
                       $sort_field='id%20desc';
                }
            $facet_opt='';
             $fac_count=$res_opt['no_fac'];
                if($fac_count=='')
                    $fac_count=20;
            if($options!='')
            {
                $facets=array();
                 $facets_array=explode(',',$fac_opt['facets']);
                
               
                 foreach($facets_array as $facet)
                {
                    $fact=strtolower($facet);
                       array_push($facets,"facet.field=$fact");
                     
                }
                $facet_opt=implode('&',$facets);
                $facet_opt.="&facet=true&facet.mincount=1&facet.limit=$fac_count";
            }
            $f_array=array();
            $bound='';
             if($facet_options!=null || $facet_options!='')
             {
                 $f_array=explode(':',$facet_options);
               
                $fac_field=strtolower($f_array[0]);
                if(count($f_array)>1)
                 $fac_type=$f_array[1];
                else
                $fac_type='';
                
                        if($fac_field!='' && $fac_type!='' )
                    {
                        $fac_fd="$fac_field";
                        $fac_tp=str_replace(' ','\%20',$fac_type);
                        $facet_opt.="&fq=$fac_fd:$fac_tp";
                           
                    }
                      if(count($f_array)>2)
                 {
                    if($f_array[2]!='')
                   $bound=$f_array[2];
                 }
                 else
                 $bound='';
            }
       
            $term=str_replace(' ','+',$term);
        
               $correction='';
               if($res_opt['spellchecker']=='spellchecker')
                {
                   $suggest_url=$protocol.'://'.$solr_options['solr_host_goto'].':'.$solr_options['solr_port_goto'].$solr_options['solr_path_goto']."/select/?q=$term&spellcheck=true&spellcheck.onlyMorePopular=true&spellcheck.extendedResults=true&spellcheck.count=5&wt=json";
                   
                   
                   $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $suggest_url);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_USERPWD ,"$user:$pwd");
                                                                           
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                if( ($currectres=curl_exec($ch)) === false)
                    {
                       $error_in_query='0';
                       
                    }
                    else
                    {
                        $res_cor=json_decode($currectres);
                      
                        if($res_cor->spellcheck)
                        {
                             foreach($res_cor->spellcheck as $r)
                                   {
                                    if(isset($r[5][1]))
                                    $correction= $r[5][1];
                                    else
                                    $correction='';
                                    }
                        }
                        if($correction=='')
                            $search_result[]=0;
                            else
                            $search_result[]='Did you mean: <b>'.$correction.'</b><br />';
                           
                    }
                        curl_close($ch);
           
                }
                else{
                    $search_result[]=0;
                }
              if($correction!='')
                $term=$correction;
                 if($field_comment==1)
                    $hl="hl.fl=title,content&hl=true&hl.q=$term";
                else
                    $hl="hl.fl=title,content,comments&hl=true&hl.q=$term";
              
               
                          
               
                if($start==0 || $start==1)
                  {
                    $st=0;
                   
                   }
                   else
                   {
                    $st=(($start-1)*$number_of_res);
                   
                   }
                
               if($bound!='' && $bound<$number_of_res)
               {
                
                 $number_of_res=$bound;
                  
               }
                          
              $url=$protocol.'://'.$solr_options['solr_host_goto'].':'.$solr_options['solr_port_goto'].$solr_options['solr_path_goto']."/select/?q=$term&$tp&fl=$fl&$hl&start=$st&rows=$number_of_res&sort=$sort_field";
              
         
               if($facet_opt!='')
               {
                $url.="&$facet_opt";
                
               }
           
             
                 $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_USERPWD ,"$user:$pwd");
                                                                           
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                if( ($res=curl_exec($ch)) === false)
                    {
                       
                         $search_result[]= 'Curl error: ' . curl_error($ch);
                    }
                    else
                    {
                        
                        $res1=json_decode($res);
                       
                        $resultset=$res1->response->docs;
                        
                        if($options!='')
                            $search_result[]=$res1->facet_counts->facet_fields;
                        else
                            $search_result[]=0;
                            
                        $total=$res1->response->numFound;
                        
                            if($bound!='')
                            {
                                    $search_result[]=$bound;
                            }
                            else{
                                 $search_result[]=$res1->response->numFound;
                                
                            }
                            
                               
                          
                            $i=1;
                            $results=array();
                            foreach($resultset as $document)
                            {
                                $msg='';
                                $id=$document->id;
                                $pid=$document->PID;
                                $name=$document->title;
                                $content=$document->content;
                                
                                $no_comments=$document->numcomments;
                                if($field_comment==1)
                                {
                                    if (property_exists($document, "comments")) 
                                            $comments=$document->comments;
                                }
                                $date=date('m/d/Y', strtotime($document->displaydate));
                                if(isset($document->categories))
                                {
                                    if(count($document->categories)>0)
                                        $cat=implode(',',$document->categories);
                                     else
                                        $cat='';
                                }
                                else
                                 $cat='';
                                $auth=$document->author;
                              $url=get_permalink($id);
                                $cont=substr($content,0,250);
                                $title_term='';
                                $conten_term='';
                                $comment_term='';
                              if(isset($res1->highlighting->$pid->title))
                                $title_term=$res1->highlighting->$pid->title;
                               
                                  if(isset($res1->highlighting->$pid->content))   
                                $conten_term=$res1->highlighting->$pid->content;
                              
                                if($field_comment==1)
                                {
                                    if(isset($res1->highlighting->$pid->comments))   
                                    $comment_term=$res1->highlighting->$pid->comments;
                                   
                                }
                        
                                $cont_no=0;$comm_no=0;
                              
                                if(count($title_term)>0 && $title_term!='')
                                {
                                   $name=implode(' (...) ', $title_term);
                                 
                                }
                                if(count($conten_term)>0 && $conten_term!='')
                                {
                                    $cont=implode(' (...) ', $conten_term);
                                    $cont_no=1;
                                }
                                if($field_comment==1 && count($comment_term)>0 && $comment_term!='')
                                {
                                   $comments=implode(' (...) ', $comment_term);
                                   $comm_no=1;
                                }
                        
                                $msg.="<div id='res$i'><div class='p_title'><a href='$url' target='_blank'>$name</a></div>";
                                if($cont_no==1)
                                    $msg.="<div class='p_content'>$cont-<a href='$url' target='_blank'>Content match</a></div>";
                                    else
                                    $msg.="<div class='p_content'>$cont</div>";
                                if($comm_no==1)
                                      $msg.="<div class='p_comment'>".$comments. "-<a href='$url' target='_blank'>Comment match</a></div>";
                                  
                                $msg.="<div class='p_misc'>
                                            By <span class='pauthor'>$auth</span>
                                            in <span class='pcat'>$cat</span>
                                            <span class='pdate'>$date</span>
                                            <span class='pcat'> $no_comments -comments</span>
                                            </div></div><hr>";
                                
                                array_push($results,$msg);
                                $i++;
                            }
                      
                   
                            curl_close($ch);
                            if(count($results)>0)
                            $search_result[]=$results;
                            else
                            $search_result[]=0;
                        }
                        
                       
                        $fir=$st+1;
                        
                        $last=$st+$number_of_res; 
                        if($total<$last)
                            $last=$total;  
                        else
                            $last=$st+$number_of_res;  
            
                        $search_result[]="<span class='infor'>Showing $fir to $last results out of $total</span>";
                    
         }
         
           return $search_result;
    }
    
    public function auto_complete_suggestions($input)
    {
        $res=array();
       
          $client=$this->client;
       
     
    
            $suggestqry = $client->createSuggester();
            $suggestqry->setHandler('suggest');
            $suggestqry->setDictionary('suggest');
            
            $suggestqry->setQuery($input);
            $suggestqry->setCount(5);
            $suggestqry->setCollate(true);
            $suggestqry->setOnlyMorePopular(true);

            $resultset = $client->suggester($suggestqry);
            
            foreach ($resultset as $term => $termResult) {
           // $msg.='<strong>' . $term . '</strong><br/>';
            
            foreach($termResult as $result){
               
            array_push($res,$wd);
            }
            }
          
       $result=json_encode($res);
        return $result;
    }
    public function index_data()
    {
       
        return wp_Solr::build_query();
       
    }
    public function build_query()
    {
        global $wpdb;
        $batchsize=100;
        $cnt=0;
        $tbl=$wpdb->prefix.'posts';
        $where='';
        
         $client=$this->client;
        $updateQuery = $client->createUpdate();
        
        $ind_opt=get_option('wdm_solr_form_data');
       
        $post_types=$ind_opt['p_types'];
        $exclude_id=$ind_opt['exclude_ids'];
        $ex_ids=array();
        $ex_ids=explode(',',$exclude_id);
        $posts=explode(',',$post_types);
        for($i=0;$i<=count($posts)-2;$i++){
            $where.=" post_type='$posts[$i]' OR";
        }
         $where.=" post_type='$posts[$i]'";
      $query="SELECT ID FROM $tbl WHERE post_status='publish' AND ( $where ) ORDER BY ID ";
     
        $ids_array=$wpdb->get_col($query);
        
        $postcount = count($ids_array);
        $doc_count=0;
	for ($idx = 0; $idx < $postcount; $idx++)
        {
		$postid = $ids_array[$idx];
		
                if (!in_array($postid, $ex_ids) )
                {
                   $doc_count++;
                    $documents[] = wp_Solr::get_single_document($updateQuery,$ind_opt,get_post($postid) );    
		}
               
	
		$cnt++;
		if ($cnt == $batchsize || $cnt== $postcount) {
			$res_final=wp_Solr::add_data_to_index( $updateQuery, $documents);
		                       
		                      
			$cnt = 0;
			$documents = array();
			
		}
                
	}
         //
        
         $solr_options=get_option('wdm_solr_conf_data');
         
         if($solr_options['host_type']=='self_hosted')
           {
             update_option('solr_docs_in_self_index',$doc_count);
           }
           else{
            update_option('solr_docs_in_cloud_index',$doc_count);
           }
          
          return $res_final;
            
    }
    public function get_single_document($updateQuery,$opt,$post)
        {
                     
            $pid=$post->ID;
            $ptitle=$post->post_title;
            $pcontent=$post->post_content;
            $pauth_info = get_userdata( $post->post_author );
            $pauthor= $pauth_info->display_name ;
	    $pauthor_s= get_author_posts_url($pauth_info->ID, $pauth_info->user_nicename);
            $ptype= $post->post_type;
	    $pdate= solr_format_date($post->post_date_gmt) ;
            $pmodified= solr_format_date($post->post_modified_gmt) ;
            $pdisplaydate= $post->post_date ;
            $pdisplaymodified= $post->post_modified;
            $purl=get_permalink($pid);
            $pcomments=array();
            $comments_con=array();
            $comm=isset($opt['comments']) ? $opt['comments'] : '';
            
            $numcomments=0;
            if($comm)
            {
                $comments_con=array();
		
			$comments = get_comments("status=approve&post_id={$post->ID}");
			foreach ($comments as $comment) {
				array_push($comments_con,$comment->comment_content);
				$numcomments += 1;
			}
	    
            }
            $pcomments= $comments_con;
            $pnumcomments=$numcomments;
            
           $cats=array();
           $categories = get_the_category($post->ID);
		if ( ! $categories == NULL ) {
			foreach( $categories as $category ) {
				    array_push($cats,$category->cat_name);
					
				
			}
		}
                
                $tag_array=array();
		$tags = get_the_tags($post->ID);
		if ( ! $tags == NULL ) {
			foreach( $tags as $tag ) {
                            array_push($tag_array,$tag->name);
				
			}
		}
  
            
            $solr_options=get_option('wdm_solr_conf_data');
         
         if($solr_options['host_type']=='self_hosted')
           {
            $doc1 = $updateQuery->createDocument();
            $numcomments = 0;
            
            $doc1->id=$pid;
            $doc1->PID=$pid;
            $doc1->title=$ptitle;
            $doc1->content=strip_tags($pcontent);
           
            $doc1->author=$pauthor ;
	    $doc1->author_s= $pauthor_s;
            $doc1->type=$ptype;
		$doc1->date= $pdate;
		$doc1->modified= $pmodified;
		$doc1->displaydate= $pdisplaydate;
		$doc1->displaymodified= $pdisplaymodified;
            
           $doc1->permalink=$purl;
            $doc1->comments= $pcomments;
            $doc1->numcomments=$pnumcomments;
            $doc1->categories=$cats;
               
            $doc1->tags=$tag_array;
            
            $custom_taxo=array();
            $taxo = $opt['taxonomies'];
		$aTaxo = explode(',', $taxo);
                 $newTax=array();
                foreach($aTaxo as $a)
                {
                     if(substr($a,(strlen($a)-4),strlen($a))=="_str")
                                            $a=substr($a,0,(strlen($a)-4));
                    array_push($newTax,$a);
                    
                }
                
		$taxonomies = (array)get_taxonomies(array('_builtin'=>FALSE),'names');
		foreach($taxonomies as $parent) {
			if (in_array($parent, $newTax)) {
				$terms = get_the_terms( $post->ID, $parent );
				if ((array) $terms === $terms) {
					$parent =  strtolower(str_replace(' ', '_', $parent));
					foreach ($terms as $term) {
                                            $nm1=$parent.'_str';
                                            $nm2=$parent . '_srch';
                                             $doc1->$nm1= $term->name;
						$doc1->$nm2= $term->name;
					}
				}
			}
		}
                
                
                $custom = $opt['cust_fields'];
		$aCustom = explode(',', $custom);
		if (count($aCustom)>0) {
			if (count($custom_fields = get_post_custom($post->ID))) {
				foreach ((array)$aCustom as $field_name ) {
                                    if(substr($field_name,(strlen($field_name)-4),strlen($field_name))=="_str")
                                            $field_name=substr($field_name,0,(strlen($field_name)-4));
                                            if(isset($custom_fields[$field_name]))
                                            {    $field = (array)$custom_fields[$field_name];
                                            
                                               
					$field_name =  strtolower(str_replace(' ', '_', $field_name));
					foreach ( $field as $key => $value ) {
                                            $nm1=$field_name . '_str';
                                            $nm2=$field_name . '_srch';
						$doc1->$nm1= $value;
						$doc1->$nm2= $value;
                                        }
					}
				}
			}
		}
           }
           else{
            
            
              
                       
                  $cust_fx='';    
             $custom_taxo=array();
            $taxo = $opt['taxonomies'];
		$aTaxo = explode(',', $taxo);
                $newTax=array();
                foreach($aTaxo as $a)
                {
                     if(substr($a,(strlen($a)-4),strlen($a))=="_str")
                                            $a=substr($a,0,(strlen($a)-4));
                    array_push($newTax,$a);
                    
                }
		$taxonomies = (array)get_taxonomies(array('_builtin'=>FALSE),'names');
		foreach($taxonomies as $parent) {
			if (in_array($parent, $newTax)) {
				$terms = get_the_terms( $post->ID, $parent );
				if ((array) $terms === $terms) {
					$parent =  strtolower(str_replace(' ', '_', $parent));
					foreach ($terms as $term) {
                                            $nm1=$parent.'_str';
                                            $nm2=$parent . '_srch';
                                            $nm_val=$term->name;
                                           
                                            $custom_taxo[]=array("$nm1"=>$nm_val);
                                            $custom_taxo[]=array("$nm2"=>$nm_val);
                                           
					}
				}
			}
		}
                
                $cust_fx=$custom_taxo;
             
               $cust_tx='';
                $custom_fl=array();
               
                $custom = $opt['cust_fields'];
		$aCustom = explode(',', $custom);
                
		if (count($aCustom)>0) {
                    
			if (count($custom_fields = get_post_custom($post->ID))) {
                            
				foreach ((array)$aCustom as $field_name )
                                {
                                   
                                        if(substr($field_name,(strlen($field_name)-4),strlen($field_name))=="_str")
                                            $field_name=substr($field_name,0,(strlen($field_name)-4));
                                         
					$field = (array)$custom_fields[$field_name];
					$field_name =  strtolower(str_replace(' ', '_', $field_name));
					foreach ( $field as $key => $value ) {
                                            $nm1=$field_name . '_str';
                                            $nm2=$field_name . '_srch';
                                             
                                         $custom_fl[]=array("$nm1"=>$value);
                                           $custom_fl[]=array("$nm2"=>$value);
                                          
					}
				}
			}
		}
                $cust_tx=$custom_fl;
        
        
            
              $doc1=array(
                        'id'=>$pid,
                        'PID'=>$pid,
                        'title'=>$ptitle,
                        'content'=>$pcontent,
                        'author'=>$pauthor,
                        'permalink'=>$purl,
                        'type'=> $ptype,
                        'date'=> $pdate,
                        'modified'=>$pmodified,
                        'displaydate'=> $pdisplaydate,
                        'displaymodified'=> $pdisplaymodified,
                        'comments'=> $pcomments,
                        'numcomments'=>$pnumcomments,
                        'categories'=>$cats,
                        'tags'=>$tag_array,
                           );
                 if($cust_tx!='')
              {
                foreach($custom_fl as $k)
                {
                    $doc1=array_merge($doc1,$k);
                }
              }
              
              if($cust_fx!='')
              {
                
                foreach($custom_taxo as $k)
                {
                    $doc1=array_merge($doc1,$k);
                }
              }
              
             
             
           }
           
          return $doc1;
           
        }
        public function add_data_to_index($updateQuery, $documents)
        {
            $solr_options=get_option('wdm_solr_conf_data');
         
         if($solr_options['host_type']=='self_hosted')
           {
            $client=$this->client;
            $updateQuery->addDocuments($documents);
            $updateQuery->addCommit();
            $result = $client->update($updateQuery);
            return $result;
           }
           else
           {
            $user=$solr_options['solr_key_goto'];
            $pwd=$solr_options['solr_secret_goto'];
            $protocol=$solr_options['solr_protocol_goto'];
               $var= json_encode($documents);
               $url=$protocol.'://'.$solr_options['solr_host_goto'].':'.$solr_options['solr_port_goto'].$solr_options['solr_path_goto'].'/update/json?commit=true';
               
                $ch = curl_init();//creating cUrl instance
                curl_setopt($ch, CURLOPT_URL, $url);//setting our URL
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_USERPWD ,"$user:$pwd");
           
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
                curl_setopt($ch, CURLOPT_POSTFIELDS, $var);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                            'Content-Type: application/json',                                                                                
                            'Content-Length: ' . strlen($var))                                                                       
                            ); 
                        if( ($res=curl_exec($ch)) === false)
                    {
                        return 'Curl error: ' . curl_error($ch);
                    }
                    else
                    {
                        return '1';
                
                    }
                curl_close($ch);
                }
                
          }
      
  
}