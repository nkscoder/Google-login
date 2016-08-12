

public function google_login(){

    
require_once ('./Google/autoload.php');

//Insert your cient ID and secret 
//You can get it from : https://console.developers.google.com/
$client_id = '275051798909-hpnc2fr3dpc0ep08p6ddab1nrdbta63m.apps.googleusercontent.com'; 
$client_secret = 'rtp3S8YWzsse2nnISU14jwA_';
$redirect_uri = 'http://localhost:80/gobarra/home/google_login/';




//incase of logout request, just unset the session var
/*if (isset($_GET['logout'])) {
  unset($_SESSION['access_token']);
}*/

/************************************************
  Make an API request on behalf of a user. In
  this case we need to have a valid OAuth 2.0
  token for the user, so we need to send them
  through a login flow. To do this we need some
  information from our API console project.
 ************************************************/

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");


/************************************************
  When we create the service here, we pass the
  client to it. The client then queries the service
  for the required scopes, and uses that when
  generating the authentication URL later.
 ************************************************/
$service = new Google_Service_Oauth2($client);

/*print_r($redirect_uri);
  die;
*/
/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
*/
  
if (isset($_GET['code'])) {

  $client->authenticate($_GET['code']);
  /*echo "dsjkhfs";
  print_r($client); die;*/
  $_SESSION['access_token'] = $client->getAccessToken();
  /* echo  $_SESSION['access_token']; die;
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  exit;*/
}



/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);


} else {
  $authUrl = $client->createAuthUrl();
}

if (isset($authUrl)){ 
	//show login url
	

	redirect($authUrl);
	
} else {
	
	$user = $service->userinfo->get(); //get user info 

	  if(!$this->home_model->getGoogleUser($user->id)){

            
              $this->home_model->createGoogleUser($user->id,$user->name,$user->email,$user->picture);

                       set_cookie('userName',$user->email, time()+60*60*24*30 );
                        /*set_cookie('pwd',$this->input->post('password'), time()+60*60*24*30 );*/
                          $data = array(
				              'email' => $user->email,
				              'is_logged_in'=> TRUE
			                    );
			               $this->session->set_userdata($data);
			               $this->session->set_flashdata('item', array('message' => 'Login Successfully','class' => 'success'));
			               redirect('home');

	      }

	      else{

              $data = array(
				              'email' => $user->email,
				              'is_logged_in'=> TRUE
			                    );
			               $this->session->set_userdata($data);
			               $this->session->set_flashdata('item', array('message' => 'Login Successfully','class' => 'success'));
			               redirect('home');

	      }

	
  }
 }


public function set_session($user)
		  {
		     
		       
		    $session_data = array(
		        'username'             => $user['name'],
		        'email'                => $user['email'],
		        'user_id'              => $user['id'] //everyone likes to overwrite id so we'll use user_id
		        
		    );
		     
		    $this->session->set_userdata($session_data);
		    
		    return TRUE;
		  }





