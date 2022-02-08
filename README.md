<meta name="google-signin-client_id" content="572750383529-8ujv1oagblneqh0jdt4tk0qtcpmu450k.apps.googleusercontent.com">
<script src="https://apis.google.com/js/platform.js" async defer></script>
secret = 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
<button class="btn btn-danger btn-block text-white signInGoogle" type="button" v-on:click="onSignIn()">
                                        <i class="fab fa-google"></i> Sign in with Google
                                    </button>

selectAccountByGoogle: function(id) {
            let t = this;
            axios.get( t.$root.$data.url + '/api/selectAccountByGoogle/'+t.token_id+"/"+id).then(async function (response) {
                let data = response.data;
                if(data.errors == 1)
                    return showToast('error', data.message);
                else{
                    showToast('success', data.message);
                    $('#exampleModal').modal('toggle');
                    t.$root.$data.auth.check = true;
                    t.$root.$data.auth.user = data.user;
                    return t.$router.push('/');
                }
            });
        },
        onSignIn: function(googleUser) {
            let t = this;
            gapi.load('auth2', function() {
                gapi.auth2.init({
                    client_id: "1072148777461-0sftuatjavl03oq9dqptji0qifc2flrc.apps.googleusercontent.com",
                }).then(function(auth2) {
                    console.log( "signed in: " + auth2.isSignedIn.get() );
                    auth2.signIn();
                    auth2.currentUser.listen(function(googleUser) {
                        if (googleUser) {
                            var id_token = googleUser.getAuthResponse().id_token;
                            t.token_id = id_token;
                            //send token to backend and get auth data informations
                            axios.get( t.$root.$data.url + '/api/checkLoginGoogle/'+id_token).then(async function (response) {
                                let data = response.data;
                                console.log(data);
                                t.accounts = data;
                                $('#exampleModal').modal('show');
                            });
                        }
                    });
                });
            });
        },


        public function checkLoginGoogle($token) {
		//https://oauth2.googleapis.com/tokeninfo?id_token=XYZ123
		$data = file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=".$token);
		$data = json_decode($data);
		$users = User::where('Email', $data->email)->select('id','name','Level','Hours','Warn','Skin','Status','LastOn')->get();
		return $users;
	}

	public function selectAccountByGoogle($token, $id) {
		$data = file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=".$token);
		$data = json_decode($data);
		$user = User::where('Email', $data->email)->where('id',$id)->first();
		if(empty($user)) {
			return json_encode(['errors' => 1, 'message' => 'Account not found, please try again.']);
		}
		Auth::loginUsingId($user->id, false);//, true
		DB::table('panel_logins')->insert([
			'PlayerID' => $user->id,
			'PostedOn' => date('Y-m-d H:i:s'),
			'IP' => $_SERVER['REMOTE_ADDR']
		]);
		return json_encode(['success' => 1, 'message' => 'V-ati conectat cu succes folosind contul de google. Va multumim!', 'user' => $user]);
	}