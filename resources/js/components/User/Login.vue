<template>
   <div class="register-box">
      <div class="register-logo">
         <router-link to="/">dddtop.ro</router-link>
      </div>
      <div class="card">
         <div class="card-body register-card-body">
            <p class="login-box-msg">Conecteaza-te la contul tau...</p>
            <form method="post" @submit="login">
               <div class="input-group mb-3">
                  <input type="email" class="form-control" placeholder="Email" id="email" name="email">
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-user"></span>
                     </div>
                  </div>
               </div>
               <div class="input-group mb-3">
                  <input type="password" class="form-control" placeholder="Parola" id="password" name="password">
                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-user"></span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="remember" value="yes">
                        <label for="remember" class="custom-control-label form-check-label">Tine-mi minte contul si nu ma deconecta.</label>
                     </div>
                  </div>
               </div>
               <br>
               <button class="btn btn-success btn-block">
                  <i class="fa fa-sign-in-alt mr-2"></i>
                  Conecteaza-te
               </button>
            </form>
            <div class="social-auth-links text-center">
               <hr>
               <a href="#" class="btn btn-block btn-danger">
                  <i class="fab fa-google-plus mr-2"></i>
                  Inregistreaza-te folosind Google+
               </a>
            </div>
            <div class="text-center">
               <router-link to="/register">Nu am cont, doresc sa ma inregistrez.</router-link>
            </div>
         </div>
         <!-- /.form-box -->
      </div>
      <!-- /.card -->
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
   methods: {
      login: function(e) {
         e.preventDefault();
         let t = this;

         let email = $('#email').val();
         let password = $('#password').val();
         let remember = $('#remember').is(":checked");


         axios.post(t.$root.$data.adv.url + '/api/login', {
            email: email,
            password: password,
            remember: remember
         }).then(function(response) {
            var data = response.data;
            console.log(data);
            if(data.errors) {
               return t.$root.swalAlert('error', data.message);
            } else {
               t.$root.swalAlert('success', data.message);
               console.log('conectat');
               t.$root.getAPIData();
               setTimeout(function() {
                  t.$router.push('/');
               }, 1500);
            }
         }).catch(function(err){
            if(err.response)
               return t.$root.swalAlert('error', err.response.data.message);
         });
      }
   }
}
</script>
