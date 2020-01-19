<template>
    <div>
        <div class="row mt-2" v-if="UI.errorMessage">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    {{UI.errorMessage}}
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <h2 class="text-center font-weight-bold text-dark">Projects ({{ (Data.projects.length > 0) ? Data.projects.length -1 : 0 }})</h2>
            </div>
        </div>
        <div class="row mt-3 mb-4">
            <div class="col-md-12">
                <v-select label="name" v-model="Data.selectedProject":component-data="sendSelectProject()" :options="Data.projects"></v-select>
            </div>
        </div>
    </div>
</template>
<script>
    import 'vue-select/dist/vue-select.css';

    export default {
        data() {
            return {
                Data: {
                    projects: [],
                    selectedProject: {id: 0, name: "All Projects", created_at: null, updated_at: null, deleted_at: null}
                },
                UI: {
                    errorMessage: null
                }
            }
        },
        mounted: function() {
            this.getProjects();
        },
        methods: {
            sendSelectProject() {
                console.log('emitting');
                this.$root.$emit('selected_project',(this.Data.selectedProject));
            },
            getProjects() {
                let self = this;
                axios.get('/project',{
                    params:{
                    all:true,
                    dropDown:true
                    }
                }).then(response => {
                    console.log(response.data.data);
                    self.Data.projects = response.data.data;
                }).catch(function (error) {
                    self.UI.errorMessage = error.message;
                });
            },
        }
    }
</script>

<style>
    .vs__dropdown-toggle {
        padding: 9px 10px 10px;
        background-color: white;
    }
</style>
