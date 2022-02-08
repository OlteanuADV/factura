<template>
    <div class="login-box">
        <div class="login-logo"> <a href="https://dddtop.ro"><b>Factura</b>.ro</a> </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Mai jos sunt afisate toate firmele dumneavoastra:</p>
                <div class="row">
                    <div class="col-12">
                        <div class="btn btn-primary btn-block" v-for="firma in firme" :key="firma.id" v-on:click="selectCompany(firma.id)">
                            {{firma.denumire}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        firme: []
    }),
    created(){

    },
    mounted: async function() {
        let t = this;
        await t.$root.getAPIData();
         
        axios.get(t.$root.$data.adv.url + "/api/company/select/0").then(function(response){
            let data = response.data;
            t.firme = data;
        });
    },
    watch: {
        
    },
    methods: {
        selectCompany: function(id) {
            let t = this;
            axios.get(t.$root.$data.adv.url + "/api/company/select/" + id).then(function(response) {
                let data = response.data;
                if(data.errors)
                    return t.$root.swalAlert("error", data.message);
                window.location = t.$root.$data.adv.url;
            })
        }
    }
}
</script>