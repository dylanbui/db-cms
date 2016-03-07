<?php

namespace App\Controller\MemberManager;

use App\Lib\Core\BaseController;

class FacebookController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

    public function loadFbMemberAction()
    {
        $array_fb_conf = array (
            'app_id' => _FB_APP_ID,
            'app_secret' => _FB_APP_SECRET,
            'default_graph_version' => 'v2.2',
        );

        $fb = new \Facebook\Facebook($array_fb_conf);
        $helper = $fb->getJavaScriptHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error (1): ' . $e->getMessage();
            exit;
        }

        $returnVal = array('result' => false, 'message' => null, 'data' => null);
        if (isset($accessToken)) // Logged in
        {
            $response = $fb->get('/me', $accessToken->getValue()); // var_dump($accessToken->getValue());

            // -- Lay ve ds friends => chi nhung friend su dung app_id nay moi duoc tra ve trong ds --
            // -- Total friends : danh sach friend cua user (chi lay duoc tong so) --
//            $response = $fb->get('/me/friends', $accessToken->getValue()); // var_dump($accessToken->getValue());
//            $response = $fb->get('/me/friends?limit=5000&fields=id,name', $accessToken->getValue()); // var_dump($accessToken->getValue());
//            $res = $fb->post('/me/feed', ['foo' => 'bar'], '{access-token}');
//            $res = $fb->delete('/{node-id}', '{access-token}');

//            $json_string = $response->getBody(); // tra ve data json
            $fbme = $response->getDecodedBody(); // tra ve array data
            //$inPath = "http://graph.facebook.com/{$fbid}/picture?type=large";
            $fbme['picture'] = "https://graph.facebook.com/{$fbme['id']}/picture?type=large";
            $fbme['updated_time_convert'] = strtotime($fbme['updated_time']) ;

            $objMember = new \App\Model\Member();
            $rowMember = $objMember->getRow("email = ?", array($fbme['email']));
            if ($rowMember)
            {
                // -- Neu ton tai account roi thi update fbid --
                // -- Co the update them cac field khac tuy rule --
                $arr = array(
                    'fbid' => $fbme['id'],
                    'avatar' => $fbme['picture'],
                    'access_token' => $accessToken->getValue()
                );
                $current_id = $objMember->update($rowMember['id'], $arr);
            }
            else
            {
                // -- Tao 1 user account voi password --
                $pw = strtolower(create_uniqid('7'));
                $arr = array(
                    'fbid' => $fbme["id"],
                    'username' => $fbme["name"],
                    'gender' => $fbme["gender"],
                    'email' => $fbme["email"],
                    'first_name' => $fbme["first_name"],
                    'last_name' => $fbme["last_name"],
                    'full_name' => $fbme["first_name"].' '.$fbme["last_name"],
                    'birthday' => $fbme['birthday'],
                    'password' => encryption($pw),
                    'plain_password' => $pw,
                    'avatar' => $fbme['picture'],
                    'access_token' => $accessToken->getValue(),
                    'create_at' => now_to_mysql()
                );
                $current_id = $objMember->insert($arr);
                // -- Xu ly sent email thong bao da tao 1 account cho member voi pw --
            }
            // -- Get member info again --
            $rowMember = $objMember->getRow("email = ?", array($fbme['email']));

            // -- Save to session --
            $this->oSession->userdata['current_user'] = $rowMember;

            $returnVal = array('result' => true, 'message' => null, 'data' => $rowMember);
        }
        echo json_encode($returnVal);
        exit();
    }
}
