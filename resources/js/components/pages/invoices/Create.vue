<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        Creaza o noua factura
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" @submit="clientSearch">
                                    <label for="searchfor">Cautare client</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="searchfor" name="searchfor" placeholder="Introduceti CUI-ul/Denumirea/Delegatul/Adresa clientului...">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <form method="post" @submit="clientAdd" class="row">
                            <div class="col-md-6">
                                <label for="cif" class=" mt-3">CUI Client</label>
                                <input type="text" name="cif" id="cif" class="form-control" placeholder="CUI-ul clientului" v-model="client.cui" v-on:input="anafSearch()">
                                <label for="name" class=" mt-3">Denumire Client</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Denumirea clientului" v-model="client.denumire">
                                <label for="delegat" class=" mt-3">Delegat client</label>
                                <input type="text" name="delegat" id="delegat" class="form-control" placeholder="Delegat client" v-model="client.delegat">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class=" mt-3">Adresa Client</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Adresa clientului" v-model="client.adresa">
                                <label for="social_address" class=" mt-3">Adresa sediu social client</label>
                                <input type="text" name="social_address" id="social_address" class="form-control" placeholder="Adresa sediu social client" v-model="client.social_address">
                                <label for="phone" class=" mt-3">Telefon Client</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Telefon client" v-model="client.phone">    
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-9"></div>
                            <div class="col-md-3">
                                <button class="btn btn-success btn-block">
                                    <i class="fa fa-plus"></i> Adauga client
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        client: []
    }),
    created(){
    },
    mounted(){
    },
    watch: {
        
    },
    methods: {
        clientSearch: function(event)
        {
            event.preventDefault();
            let t = this;
            
            axios.post( t.$root.$data.adv.url + '/api/client/search',{
                search: $('#searchfor').val()
            }).then(async function (response) {
                let data = response.data;
                t.client = data;
            });
        },
        clientAdd: function(event)
        {
            event.preventDefault();
            let t = this;

            axios.post( t.$root.$data.adv.url + '/api/clients/add', {
                client: t.client
            }).then(async function (response) {
                let data = response.data;
                console.log(data);
            });
        },
        anafSearch: function()
        {
            let t = this;
            axios.post( t.$root.$data.adv.url + '/api/client/anaf',{
                cui: t.client.cui
            }).then(async function (response) {
                let data = response.data;
                if(data.found[0].denumire !== "")
                {
                    t.client = data.found[0];
                    console.log(t.client);
                }
            });
            
        }
    }
}
</script>