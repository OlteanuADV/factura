<template>
    <div class="login-box">
        <div class="login-logo"> <a href="https://dddtop.ro"><b>Factura</b>.ro</a> </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Conectati-va folosind contul de Google.</p>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-block"  v-on:click="onSignIn()"> <i class="fab fa-google-plus-g"></i> Conecteaza-te! </button>
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

                            axios.get( t.$root.$data.adv.url + '/api/checkLoginGoogle/'+id_token).then(async function (response) {
                                window.location =  t.$root.$data.adv.url;
                            });
                        }
                    });
                });
            });
        },
    }
}
</script>