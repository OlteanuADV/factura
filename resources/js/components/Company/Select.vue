<template>
    <div>
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <b>
                    {{$root.$data.adv.name}}
                </b>
            </div>
            <!-- User name -->
            <h3 class="lockscreen-name text-center">
                {{$root.$data.adv.auth.user.lastname}} {{$root.$data.adv.auth.user.firstname}}
            </h3>

            <div v-if="true">
                <div class="text-center">
                    Se pare ca nu aveti nicio firma inregistrata in baza noastra de date, va rugam sa va creati una 
                    <router-link to="/company/create">aici</router-link>.
                </div>
            </div>
            <div v-else>
                <div class="card card-body">
                    DDD TOP CONFORT EXPERT SRL
                </div>
                    
                <div class="card card-body">
                    DDD TOP CONFORT INVEST SRL
                </div>
                
                <div class="card card-body">
                    RCS RDS SA
                </div>
            </div>


            <div class="lockscreen-footer text-center">
                <strong>Copyright &copy; since 2021 <a href="https://adminlte.io">dddtop.ro</a>.</strong> All rights reserved.
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
        // this.$router.push('/company/create');
    },
    methods: {

    }
}
</script>