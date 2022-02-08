<template>
    <div>
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <b>
                    {{$root.$data.adv.name}}
                </b>
            </div>
            <!-- User name -->
            <h4 class="lockscreen-name text-center" v-if="$root.$data.adv.auth !== undefined">
                Bine ati venit, <br>
                {{$root.$data.adv.auth.user.lastname}} {{$root.$data.adv.auth.user.firstname}}
            </h4>

            <!-- <div class="text-center">
                Se pare ca nu aveti nicio firma inregistrata in baza noastra de date, introduceti mai jos informatii despre firma dumneavoastra
            </div> -->
            
            <div class="card card-body">
                <form @submit="addCompany" method="post">
                    <label for="cui">Va rugam sa introduceti mai jos CUI-ul firmei dumneavoastra:</label>
                    <input type="number" name="cui" id="cui" class="form-control" placeholder="Va rugam sa tastati CUI-ul firmei dumneavoastra..." v-on:input="cautaFirma()">

                    <div v-if="firma.denumire">
                        <label for="name">Numele firmei:</label>
                        <input type="text" name="denumire" id="denumire" class="form-control" v-bind:value="firma.denumire" disabled>

                        <label for="adresa">Adresa firmei:</label>
                        <input type="text" name="adresa" id="adresa" v-bind:value="firma.adresa" class="form-control" disabled>
                        
                        <label for="nrRegCom">Numarul registrul comertului:</label>
                        <input type="text" name="nrRegCom" id="nrRegCom" v-bind:value="firma.nrRegCom" class="form-control" disabled>
                        <br>
                        <div class="text-center">
                            Se pare ca firma dumneavoastra 
                            <span v-if="firma.scpTVA">este</span>
                            <span v-else>nu este</span>
                            platitoare de TVA.
                        </div>
                        <hr>
                        <button class="btn btn-danger btn-block" type="submit">
                            <i class="fa fa-briefcase"></i> Adaugati firma
                        </button>
                    </div>
                </form>
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
        firma: []
    }),
    created(){
        
    },
    mounted(){
    },
    methods: {
        cautaFirma: function() {
           let t = this;
           let cui = $('#cui').val();
           axios.get(t.$root.$data.adv.url + '/api/anaf/search/'+cui).then(function(response) {
                var data = response.data;
                t.firma = data;
            })
        },
        addCompany: function(e) {
            let t = this;
            e.preventDefault();
            
            if(t.firma.adresa == "")
                return 0;

            
            Swal.fire({
                title: 'Esti sigur ca vrei sa iti atribui compania '+t.firma.denumire+'?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Da!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(t.$root.$data.adv.url + '/api/company/create', {
                        firma: t.firma
                    }).then(function(response) {
                        let data = response.data;
                        console.log(data);
                    });
                }
            })
        }
   }
}
</script>
