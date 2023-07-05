<?php
/**
 *  Functions and definitions for auxin framework
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2022
 * @link       http://averta.net
 */

/*-----------------------------------------------------------------------------------*/
/*  Add your custom functions here -  We recommend you to use "code-snippets" plugin instead
/*  https://wordpress.org/plugins/code-snippets/
/*-----------------------------------------------------------------------------------*/	


//$v2=pvc_get_post_views(get_the_ID());
//$v2= intval($v2)+1;
update_post_meta(get_the_ID(), 'postviews', 15);
//update_post_meta( 5777, 'postviews', $v2);

//
add_filter('wp_is_application_passwords_available', '__return_true' );
add_action( 'rest_api_init', function () {
  register_rest_route( 'api', 'get-alfresco-data', array(
    'methods' => 'POST',
    'callback' => 'getAlfrescoData',
  ) );
	register_rest_route( 'api', 'get-alfresco-data/(?P<node_id>[a-zA-Z0-9-]+)', array(
    'methods' => 'GET',
    'callback' => 'getAlfrescoDataByNodeId',
  ) );
	register_rest_route( 'api', 'get-filter-data', array(
    'methods' => 'POST',
    'callback' => 'getFilterData',
  ) );
	register_rest_route( 'api', 'get-publication-data', array(
    'methods' => 'POST',
    'callback' => 'filterPublicationData',
  ) );
	register_rest_route( 'api', 'download-alfresco-data/(?P<node_id>[a-zA-Z0-9-]+)', array(
    'methods' => 'GET',
    'callback' => 'downloadAlfrescoDataByNodeId',
  ) );
	register_rest_route( 'api', 'get-file-thumbnail/(?P<node_id>[a-zA-Z0-9-]+)', array(
    'methods' => 'GET',
    'callback' => 'getFileThumbnailByNodeId',
  ) );
	register_rest_route( 'api', 'get-files-from-cmis', array(
    'methods' => 'GET',
    'callback' => 'getFilesFromCmis',
  ) );

} );

function getAlfrescoData( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  $curl = curl_init();

    curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/search/versions/1/search',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>$request->get_body(),
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		'Content-Type: application/json'
	  ),
	));

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}

function getAlfrescoDataByNodeId( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  $curl = curl_init();

    curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/'.$request['node_id'].'?include=association,permissions,allowableOperations,isFavorite,path',
	  CURLOPT_RETURNTRANSFER => true,
  	  CURLOPT_ENCODING => '',
  	  CURLOPT_MAXREDIRS => 10,
  	  CURLOPT_TIMEOUT => 0,
  	  CURLOPT_FOLLOWLOCATION => true,
  	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		'Content-Type: application/json'
	  ),
	));

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}

function downloadAlfrescoDataByNodeId( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  set_time_limit(0);
  $curl = curl_init();

    curl_setopt_array($curl, array(
	  CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/'.$request['node_id'].'/content?attachment=true',
	    CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
  		CURLOPT_TIMEOUT => 2400,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		 'Content-Type: application/octet-stream' 
	  ),
	));

    $result = curl_exec($curl);

    curl_close($curl);

    echo $result;
}

function getFilesFromCmis( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  $curl = curl_init();

    curl_setopt_array($curl, array(
	   CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/search/versions/1/search',
		CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
 		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'POST',
  		CURLOPT_POSTFIELDS =>'{
            "query": {
                "query": "(TYPE:\\"prd:produit\\")",
                "language": "afts"
            },
            "include": [
                "properties",
                "association"
            ]
        }',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		'Content-Type: application/json'
	  ),
	));
	
	 $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}

function getFilterData(WP_REST_Request $request) {
  // You can access parameters via direct array access on the object:
  $curl = curl_init();

    curl_setopt_array($curl, array(
	   CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/search/versions/1/search',
		CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
 		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'POST',
  		CURLOPT_POSTFIELDS =>'{
			  "query": {
				"language": "afts",
				"query": "+TYPE:\\"prd:produit\\" @prd\\\\:langue:\\"'.$request['langue'].'\\" "
			  },
			  "paging": {
				"maxItems": 100,
				"skipCount": 0
			  },
			  "include": [
				"allowableOperations","properties","association","aspectNames"
			  ]
			}',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		'Content-Type: application/json'
	  ),
	));
	
	 $result = curl_exec($curl);

    curl_close($curl);

     $liste = json_decode($result, true);
	$donne = array();
	$donne = $liste['list'];
	return $donne;
}

function filterPublicationData( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  $curl = curl_init();

    curl_setopt_array($curl, array(
	   CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/search/versions/1/search',
		CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
 		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'POST',
  		CURLOPT_POSTFIELDS =>'{
			  "query": {
				"language": "afts",
				"query": "+TYPE:\\"prd:produit\\" @prd\\\\:Poids:\\"'.$request['poids'].'\\" @prd\\\\:langue:\\"'.$request['langue'].'\\" @cm\\\\:author:\\"'.$request['auteur'].'\\" +@prd\\\\:Bailleur:\\"'.$request['bailleur'].'*\\""
			  },
			  "paging": {
				"maxItems": 100,
				"skipCount": 0
			  },
			  "include": [
				"allowableOperations","properties","association","aspectNames"
			  ]
			}',
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		'Content-Type: application/json'
	  ),
	));
	
	 $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}


function getFileThumbnailByNodeId( WP_REST_Request $request ) {
  // You can access parameters via direct array access on the object:
  set_time_limit(0);
	//header('Content-Type: image/jpeg');

  $curl = curl_init();

    curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://130.162.214.241:8080/alfresco/api/-default-/public/alfresco/versions/1/nodes/'.$request['node_id'].'/renditions/imgpreview/content?attachment=false&placeholder=false',		
	     CURLOPT_RETURNTRANSFER => true,
  		CURLOPT_ENCODING => '',
  		CURLOPT_MAXREDIRS => 10,
  		CURLOPT_TIMEOUT => 0,
  		CURLOPT_FOLLOWLOCATION => true,
  		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  		CURLOPT_CUSTOMREQUEST => 'GET',
	  	CURLOPT_HTTPHEADER => array(
		'Authorization: Basic YWRtaW46YWRtaW4=',
		  'content-type: image/png;charset=UTF-8'
	  ),
	));

    $result = curl_exec($curl);

    curl_close($curl);

    echo $result;
}






/*-----------------------------------------------------------------------------------*/
/*  Init theme framework
/*-----------------------------------------------------------------------------------*/
require( 'auxin/auxin-include/auxin.php' );
/*-----------------------------------------------------------------------------------*/
