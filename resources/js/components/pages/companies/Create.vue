<template>
    <div class="login-box">
        <div class="login-logo"> <a href="https://dddtop.ro"><b>Factura</b>.ro</a> </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Se pare ca nu aveti asociata nicio firma/asociatie, completati urmatoarele informatii pentru a va putea adauga o firma/societate.</p>
                <div class="row">
                    <form action="" @submit="saveCompany" class="col-12">
                        <label>Introduceti CUI-ul societatii:</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" placeholder="Introduceti CUI-ul firmei/societatii dumneavoastra" v-model="cui" v-on:input="searchCompany()">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fa fa-hashtag"></i>
                                </div>
                            </div>
                        </div>
                        <div v-if="firma.adresa != undefined">
                            <label>Denumirea societatii:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Introduceti denumirea firmei/societatii dumneavoastra" v-model="firma.denumire" disabled>
                            </div>
                            <label>Adresa societatii:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Introduceti adresa firmei/societatii dumneavoastra" v-model="firma.adresa" disabled>
                            </div>
                            <label>Nr. registrul comertului al societatii:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Introduceti nr.registrul comertului firmei/societatii dumneavoastra" v-model="firma.nrRegCom" disabled>
                            </div>
                            <div class="text-center">
                                Se pare ca firma <span v-if="firma.scpTVA">este</span><span v-else>nu este</span> platitoare de T.V.A.
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-briefcase"></i> Asociaza-ti societatea!
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: () => ({
        cui: '',
        firma: []
    }),
    created(){

    },
    mounted(){
        
    },
    watch: {
        
    },
    methods: {
        searchCompany: function(e) {
            let t = this;
            axios.get(t.$root.$data.adv.url + '/api/anaf/search/' + t.cui).then(function(response){
                let firma = response.data;
                if(firma.adresa != "")
                    t.firma = firma;
                else
                    t.firma = [];
            });
        },
        saveCompany: function(e) {
            e.preventDefault();
            let t = this;

            Swal.fire({
                title: 'Esti sigur?',
                text: "Astfel va veti asocia societatea "+t.firma.denumire+" contului dumneavoastra, iar aceasta actiune este ireversibila!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Da, adauga!',
                cancelButtonText: "Anuleaza"
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(t.$root.$data.adv.url + '/api/company/create', {
                        firma: t.firma
                    }).then(function(response) {
                        let data = response.data;
                        if(data.errors)
                            return t.$root.swalAlert("error", data.message);
                        window.location = t.$root.$data.adv.url;
                    });
                }
            })
        }
    }
}
</script>