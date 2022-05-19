<template>
    <div class="login-box">
        <div class="login-logo"> <a href="https://dddtop.ro"><b>Factura</b>.ro</a> </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Conectati-va folosind contul de Google.</p>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-block"  v-on:click="googleLogin()"> <i class="fab fa-google-plus-g"></i> Conecteaza-te cu Google! </button>
                        <button type="submit" class="btn btn-primary btn-block"  v-on:click="facebookLogin()"> <i class="fab fa-facebook"></i> Conecteaza-te cu Facebook! </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({

    }),
    created(){

    },
    mounted(){
        
    },
    watch: {
        
    },
    methods: {
        googleLogin: function(googleUser) 
        {
            let t = this;
            gapi.load('auth2', function() {
                gapi.auth2.init({
                    client_id: t.$root.$data.adv.google,
                }).then(function(auth2) {
                    console.log( "signed in: " + auth2.isSignedIn.get() );
                    auth2.signIn();
                    auth2.currentUser.listen(function(googleUser) {
                        if (googleUser) {
                            var id_token = googleUser.getAuthResponse().id_token;

                            axios.get( t.$root.$data.adv.url + '/api/login/google/'+id_token).then(async function (response) {
                                window.location =  t.$root.$data.adv.url;
                            });
                        }
                    });
                });
            });
        },
        facebookLogin: function()
        {
            let t = this;
            FB.login(function(response) {
                if(response.status === "connected")
                {
                    console.log(response.authResponse);
                    let id_token = response.authResponse.accessToken;
                    axios.get( t.$root.$data.adv.url + '/api/login/facebook/'+id_token).then(async function (response) {
                        console.log(response.data);
                        window.location =  t.$root.$data.adv.url;
                    });
                }
            }, {scope: 'public_profile,email,user_birthday,user_gender'});
        }
    }
}
</script>