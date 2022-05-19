<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="card collapsed-card">
                    <div class="card-header text-center">
                        Codurile mele CPV
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>                            
                        </div>
                    </div>
                    <div class="card-body"  style="display:none;">
                        <form method="post" @submit="addCpv">
                        <div class="input-group">
                                <select class="custom-select" name="cpvs" id="cpvs" v-on:click="loadCPVS()">
                                    <option v-for="cpv in cpvs" :key="cpv.id" v-bind:value="cpv.id">{{ cpv.text }}</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                        </div>
                        </form>
                        <hr>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="row p-1"  v-for="cpv in my_cpvs" :key="cpv.id">
                                    <div class="col-md-8">
                                        {{ cpv.text }}
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-danger float-right" type="button" v-on:click="removeCpv(cpv.id)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card collapsed-card">
                    <div class="card-header text-center">
                        Cuvintele mele cheie
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>                            
                        </div>
                    </div>
                    <div class="card-body" style="display:none;">
                        <form method="post" @submit="addWord">
                            <div class="input-group">
                                    <input id="cuvant_nou" type="text" class="form-control" placeholder="Introduceti cuvantul...">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                            </div>
                        </form>
                        <hr>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="row p-1"  v-for="word in my_words" :key="word.id">
                                    <div class="col-md-8">
                                        {{ word.text }}
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-danger float-right" type="button" v-on:click="removeWord(word.id)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        Anunturi SEAP
                    </div>
                    <div class="card-body">
                        <div v-if="loading">
                            <h4 class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> <br>
                                Anunturile din SEAP se incarca, va rugam asteptati...
                            </h4>
                        </div>
                        <div v-else v-for="anunt in seap" :key="anunt.id">
                            <div v-bind:class="(new Date(anunt.Deadline) < new Date()) ? 'row bg-danger' : 'row'">
                                <div class="col-md-2 p-5">
                                    <h2 class="text-center">
                                        {{anunt.Region}}
                                    </h2>
                                </div>
                                <div class="col-md-10 p-1">
                                    <div class="text-center">
                                        <router-link class="h5" to="/">
                                            {{anunt.contractObject}}
                                        </router-link>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <i class="fa-solid fa-folder"></i> Cod si denumire CPV: {{anunt.cpvCodeText}} <br>
                                            <i class="fa-solid fa-briefcase"></i> Denumire contractant: {{anunt.AuthorityName}} <br>
                                            <i class="fa-solid fa-calendar-alt"></i> Data limita depunere: {{anunt.Deadline}} <br>
                                            <span v-if="anunt.documentUrl != null">
                                                <i class="fa-solid fa-file-alt"></i> Document: <a :href="'https://www.e-licitatie.ro/' + anunt.documentUrl" target="_blank">Descarca</a>
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <h2 class="text-center p-3">
                                                {{anunt.Pret}} RON
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
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
        cpvs: [],
        my_cpvs: [],
        my_words: [],
        seap: [],
        loading: false,
    }),
    created(){
        this.loadData();
        this.loadAnnounces();
    },
    mounted(){
    },
    watch: {
        
    },
    methods: {
       loadData: function() {
           let t = this;
           axios.get('api/seap/cpvs/my').then(function(response) {
                t.my_cpvs = response.data;
            });
            axios.get('api/seap/words/my').then(function(response) {
                t.my_words = response.data;
            });
       },
       loadAnnounces: function() {
           this.loading = true;
           let t = this;

           axios.get('api/seap/announces/my').then(function(response) {
                t.seap = response.data;
                t.loading = false;
            });
       },
       addCpv: function(e) {
           console.log("cpv loading");
           e.preventDefault();
           let t = this;

            let cpv_id = $('#cpvs').val();
            axios.post('api/seap/cpvs/add', {code: cpv_id}).then(function(response){
                if(response.data.success)
                {
                    t.loadData();
                    t.$root.swalAlert('success', response.data.message);
                }
            });
       },
       loadCPVS: function() {
            if(this.cpvs.length == 0)
            {
                Swal.showLoading();
                let t = this;
                axios.get('api/seap/cpvs').then(function(response) {
                    t.cpvs = response.data;
                    Swal.close();
                });
            }
       },
       addWord: function(e) {
           e.preventDefault();
            let t = this;
            let word = $('#cuvant_nou').val();
            axios.post('api/seap/words/add', {word: word}).then(function(response){
                if(response.data.success)
                {
                    t.$root.swalAlert('success', response.data.message);
                    t.loadData();
                }
            });
       },
       removeCpv: function() {

       },

    }
}
</script>