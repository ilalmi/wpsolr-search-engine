<?php
function wdm_return_solr_rows()
{
 if( isset( $_POST['security'] ) 
    && wp_verify_nonce( $_POST['security'], 'nonce_for_autocomplete' ) 
) {
  if(session_id() == '') {
    session_start();
}
 
    $input = $_POST['word'];

    if($_SESSION['wdm-host']=='')
{
    $solr_options=get_option('wdm_solr_conf_data');
    $_SESSION['wdm-host']=$solr_options['solr_host'];
    $_SESSION['wdm-port']=$solr_options['solr_port'];
    $_SESSION['wdm-path']=$solr_options['solr_path'];
}
    $host=$_SESSION['wdm-host'];
    $port=$_SESSION['wdm-port'];
    $spath=$_SESSION['wdm-path'];

    $config = array(
                    "endpoint" =>
                                array("localhost" => array(
                                                            "host"=>$host,
                                                            "port"=>$port,
                                                            "path"=>$spath
                                                            )
                                    )
                    );
    require('vendor/autoload.php');
    $client = new Solarium\Client($config);
    $input=strtolower($input); 
    $res=array();
       
    $suggestqry = $client->createSuggester();
    $suggestqry->setHandler('suggest');
    $suggestqry->setDictionary('suggest');
            
    $suggestqry->setQuery($input);
    $suggestqry->setCount(5);
    $suggestqry->setCollate(true);
    $suggestqry->setOnlyMorePopular(true);
    
    $resultset = $client->suggester($suggestqry);
            
    foreach ($resultset as $term => $termResult)
    {
        foreach($termResult as $result)
        {
               
            array_push($res,$result);
        }
    }
          
    $result1=json_encode($res);
    
    echo $result1;
 }
die();
}

add_action( 'wp_ajax_wdm_return_solr_rows', 'wdm_return_solr_rows' );      
add_action( 'wp_ajax_nopriv_wdm_return_solr_rows', 'wdm_return_solr_rows' );

function wdm_return_goto_solr_rows()
{
 if( isset( $_POST['security'] ) 
    && wp_verify_nonce( $_POST['security'], 'nonce_for_autocomplete' ) 
) {
if(session_id() == '') {
    session_start();
}


  $input = $_POST['word'];
  $in=substr($input,0,1);


if($result=='')
{

$input=str_replace(' ','%20',$input);
$input=strtolower($input);

if($_SESSION['wdm-ghost']=='')
{
    $solr_options=get_option('wdm_solr_conf_data');
    $_SESSION['wdm-ghost']=$solr_options['solr_host_goto'];
	$_SESSION['wdm-gport']=$solr_options['solr_port_goto'];
	$_SESSION['wdm-gpath']=$solr_options['solr_path_goto'];
        $_SESSION['wdm-guser']=$solr_options['solr_key_goto'];
	$_SESSION['wdm-gpwd']=$solr_options['solr_secret_goto'];
	$_SESSION['wdm-gproto'] =$solr_options['solr_protocol_goto'];
}

 $host=$_SESSION['wdm-ghost'];
 $port=$_SESSION['wdm-gport'];
 $spath=$_SESSION['wdm-gpath'];
 $user=$_SESSION['wdm-guser'];
 $pwd=$_SESSION['wdm-gpwd'];
 $proto=$_SESSION['wdm-gproto'];
 
  $url="$proto://".$host.":".$port.$spath."/suggest?q=$input&wt=json" ;


               $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch,CURLOPT_USERPWD ,"$user:$pwd");
                                                                           
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                if( ($currectres=curl_exec($ch)) === false)
		    {
		      echo  "curl_error:" . curl_error($ch) ;
		    }
           else{
	   
            $res=json_decode($currectres);
         
            $suggest= $res->spellcheck->suggestions[1]->suggestion;
	    $result= json_encode($suggest);
           }
}
echo $result;
 }
	   die();
}

add_action( 'wp_ajax_wdm_return_goto_solr_rows', 'wdm_return_goto_solr_rows' );      
add_action( 'wp_ajax_nopriv_wdm_return_goto_solr_rows', 'wdm_return_goto_solr_rows' );