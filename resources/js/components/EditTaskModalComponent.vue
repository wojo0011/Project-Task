<template>
    <div>
        <modal height="auto" name="edit-task-modal" draggable=".window-header" :clickToClose="false" :reset="true" :delay="300" @before-open="beforeOpen">
            <div class="row window-header">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Edit Task
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span v-if="!UI.editTaskBusy" class="input-group-text" id="editTask">Edit Task</span>
                                            <span v-if="UI.editTaskBusy" class="input-group-text" id="editTask"><i class="fas fa-spin fa-spinner"></i></span>
                                        </div>
                                        <input type="text" class="form-control new-task-input" v-model="Data.editTask.name" placeholder="editTask" aria-label="editTask" aria-describedby="editTask">
                                        <v-select class="input-grouped-v-select" label="name" v-model="Data.editTask.project.data" :options="Data.projects" :clearable="false"></v-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-sm btn-danger" @click="this.hideEditTaskModal">Cancel</button>
                            <button class="btn btn-sm btn-info" @click="this.updateTask">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    import Vue from 'vue';
    import miniToastr from 'mini-toastr';
    import VModal from 'vue-js-modal';
    Vue.use(VModal);

    miniToastr.init()// config can be passed here miniToastr.init(config)
    export default {
        data() {
            return {
                Data: {
                    editTask: {
                        name: null,
                        project: {
                            data: null
                        }
                    },
                    projects: [],
                    selectedProject: null
                },
                UI: {
                    editTaskBusy: false,
                },
            }
        },
        methods: {
            updateTask() {
                /** SHOW USER SYSTEM IS WORKING ON CREATING A NEW TASK **/
                this.UI.editTaskBusy=true;
                let self = this;
                /** MAKE API CALL TO DELETE SELECTED TASK **/
                axios({
                    method: 'put',
                    url: '/task/' +self.Data.editTask.id,
                    params: self.Data.editTask
                }).then(response => {
                    self.UI.editTaskBusy=false;
                    self.Data.editTask = response.data.data;
                    miniToastr.success("Update Task '" + self.Data.editTask.name + "'.", "Updated Task Successfully");
                    self.hideEditTaskModal();
                    self.$parent.getTasks(this.Data.selectedProject);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            hideEditTaskModal () {
                this.$modal.hide('edit-task-modal');
            },
            beforeOpen(event) {
                this.Data.selectedTaskIndex = event.params.selectedTaskIndex;
                this.Data.editTask = event.params.task;
                this.Data.projects = event.params.projects;
                this.Data.selectedProject = event.params.selectedProject;
            }
        },
    }
</script>

<style scoped>
    .vs__dropdown-toggle {
        padding: 9px 10px 10px;
    }
    .new-task-input {
        line-height: inherit;
        margin: 0;
        font-family: inherit;
        font-size: inherit;
        height: 47px;
    }
</style>

<style>
    .input-grouped-v-select .vs__dropdown-toggle {
        border-radius: unset !important;
        min-width: 200px;
    }
</style>
