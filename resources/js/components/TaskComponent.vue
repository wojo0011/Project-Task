<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center font-weight-bold text-dark">Tasks ({{(Data.tasks.length >= 0) ? Data.tasks.length : 0 }})</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-1"><strong>Task Priority</strong></div>
                            <div class="col-md-3"><strong>Task Name</strong></div>
                            <div class="col-md-3"><strong>Project</strong></div>
                            <div class="col-md-3"><strong>Created At</strong></div>
                            <div class="col-md-2"><strong>Actions</strong>
                                <div v-if="!UI.loadingTasks" class="btn btn-light btn-refresh btn-sm float-right" @click="getTasks(selectedProject)">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div v-if="UI.loadingTasks" class="btn btn-light btn-refresh btn-sm float-right">
                                    <i class="fas fa-sync-alt fa-spin"></i>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="float-right text-danger small" v-if="!UI.dragAndDropEnabled"><img width="20px" height="20px" src="../../svg/mouse-solid-red.svg" alt="Mouse Icon">
 Drag And Drop / Re Order Disabled</span>
                                <span class="float-right text-success small" v-if="UI.dragAndDropEnabled"><img width="20px" height="20px" src="../../svg/mouse-solid-green.svg" alt="Mouse Icon">
 Drag And Drop / Re Order Enabled</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <draggable v-if="Data.tasks.length > 0" @update="update" v-model="Data.tasks" :disabled="!UI.dragAndDropEnabled">
                            <div v-for="(task, selectedTaskIndex) in Data.tasks" :key="task.id" class="row p-1 task-item" v-bind:class="{ noDrop : !UI.dragAndDropEnabled, drop : UI.dragAndDropEnabled }">
                                <div class="col-md-1" v-if="UI.dragAndDropEnabled">{{selectedTaskIndex+1}}</div>
                                <div class="col-md-1" v-if="!UI.dragAndDropEnabled">{{task.priority+1}}</div>
                                <div class="col-md-3">{{task.name}}</div>
                                <div class="col-md-3">{{task.project.data.name}}</div>
                                <div class="col-md-3">{{task.created_at}}</div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-info pull-right" @click="showEditTaskModal(task, selectedTaskIndex)"><i class="far fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger pull-right" @click="deleteTask(task, selectedTaskIndex)"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </draggable>
                        <div class="row" v-if="Data.tasks.length === 0">
                            <div class="col-md-12 alert alert-info">
                                There Are No Tasks In The Project
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span v-if="!UI.newTaskBusy && !UI.newTaskInputError" class="input-group-text">New Task</span>
                                        <span v-if="UI.newTaskBusy && !UI.newTaskInputError" class="input-group-text"><i class="fas fa-spin fa-spinner"></i></span>
                                        <span v-if="UI.newTaskInputError" class="input-group-text text-danger alert-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                    </div>
                                    <input type="text" class="form-control new-task-input" @keyup.enter="addTask" v-model="Data.newTask" placeholder="Enter New Task" aria-label="newTask" aria-describedby="newTask">
                                    <v-select class="input-grouped-v-select" label="name" v-model="Data.newSelectedProject" :options="Data.projects" :clearable="false"></v-select>
                                    <button class="btn btn-info merge-left" @click="addTask">Add Task</button>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="UI.newTaskInputError">
                            <div class="col-md-12">
                                <div class="alert alert-danger" role="alert">
                                    {{ UI.newTaskErrorMessage }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <edit-task-modal-component></edit-task-modal-component>
    </div>
</template>
<script>


    import Vue from 'vue';
    import draggable from 'vuedraggable';
    import miniToastr from 'mini-toastr';
    import VModal from 'vue-js-modal';
    import { addTaskServerValidationMixin } from '../mixins/addTaskServerValidation';
    import { addTaskFrontEndValidationMixin } from '../mixins/addTaskFrontEndValidation';
    import VueProgressBar from 'vue-progressbar'

    Vue.use(VueProgressBar, {
        color: 'rgb(143, 255, 199)',
        failedColor: 'red',
        height: '2px'
    });

    Vue.use(VModal);

    miniToastr.init()// config can be passed here miniToastr.init(config)
    export default {
        mixins: [addTaskServerValidationMixin, addTaskFrontEndValidationMixin],
        data() {
            return {
                Data: {
                    tasks: [],
                    projects: [],
                    newTask: null,
                    selectedTaskIndex: null,
                    newSelectedProject: null,
                },
                UI: {
                    newTaskInputError: false,
                    newTaskErrorMessage: '',
                    newTaskBusy: false,
                    loadingTasks: false,
                    dragAndDropEnabled: false
                },
            }
        },
        props: {
            selectedProject: {id: 0, name: "All Projects", created_at: null, updated_at: null, deleted_at: null}
        },
        mounted: function() {
            /** GET ALL TASKS ON LOAD **/
            this.getTasks();

            /** SET THE SELECTED PROJECT WHEN PROJECT COMPONENT CHANGES **/
            this.$root.$on('selectedProject', (selectedProject) => {
                this.selectedProject = selectedProject;
            });

            /** GET A LIST OF PROJECTS ON LOAD **/
            this.projects = this.getProjects();
        },
        methods: {
            /**
             * GET TASKS RELATED TO THE SELECTED PROJECT WHEN PROJECT IS SELECTED IN DROP DOWN
             * @param selectedProject
             */
            setSelectedProject(selectedProject) {
                this.getTasks(selectedProject);
            },
            /**
             * GET TASKS RELATED TO THE SELECTED PROJECT
             * @param Project
             */
            getTasks(Project = null) {
                this.$Progress.start(); this.UI.loadingTasks=true;
                /** NO PROJECT SELECTED - SET PROJECT TO ALL PROJECTS **/
                if(Project === null) {
                    Project = { id: 0 }
                }

                /** WHEN ALL PROJECTS SHOWING DISABLE DRAG AND DROP REORDER FUNCTIONALITY **/
                if(Project.id === 0) {
                    this.UI.dragAndDropEnabled = false;
                } else {
                    this.UI.dragAndDropEnabled = true;
                }

                /** GET ALL TASKS FROM API CALL **/
                axios.get('/task',
                    { params: {projectId : Project.id}
                }).then(response => {
                    this.Data.tasks = response.data.data;
                    this.$Progress.finish();
                    this.UI.loadingTasks=false;
                })
                .catch(function (error) {
                    console.log(error);
                    this.$Progress.fail();
                    this.UI.loadingTasks=false;
                });
            },
            /**
             * HANDLES CREATE A NEW TASK
             */
            addTask() {
                /** SHOW USER SYSTEM IS WORKING ON CREATING A NEW TASK **/
                this.UI.newTaskBusy=true;
                this.$Progress.start();

                if(this.addTaskFrontEndValidationMixin(miniToastr, this)) {
                    let self = this;
                    /** MAKE API CALL TO STORE THE NEW TASK **/
                    axios({
                        method: 'post',
                        url: '/task',
                        data: {
                            name: self.Data.newTask,
                            project_id: self.Data.newSelectedProject.id
                        }
                    }).then(response => {
                        self.UI.newTaskBusy=false;
                        miniToastr.success("Added Task '" + self.newTask + "'.", "Added Task Successfully");
                        self.Data.newTask = '';
                        this.UI.newTaskInputError = false;
                        /** Refresh Task List **/
                        this.$Progress.increase(25);
                        self.getTasks(self.selectedProject);
                        this.$Progress.finish();
                    }).catch(function (error) {
                        self.addTaskServerValidationMixin(error,miniToastr,self);
                    });
                }
            },
            /**
             * DELETE A SELECTED TASK
             * @param task
             * @param selectedTaskIndex
             */
            deleteTask(task, selectedTaskIndex) {
                /** SHOW USER SYSTEM IS WORKING ON CREATING A NEW TASK **/
                this.UI.newTaskBusy=true;
                let self = this;
                /** MAKE API CALL TO DELETE SELECTED TASK **/
                axios({
                    method: 'delete',
                    url: '/task/'+task.id,
                    params: task,
                }).then(response => {
                    /** REMOVE TASK FROM TASKS LIST **/
                    self.Data.tasks.splice(selectedTaskIndex, 1);
                    self.UI.newTaskBusy=false;
                    miniToastr.success("Deleted Task '" + task.name + "'.", "Deleted Task Successfully");
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            /**
             * UPDATE
             */
            update() {
                let self = this;
                axios({
                    method: 'post',
                    url: '/updateAllPriorities',
                    data: {
                        tasks: self.Data.tasks
                    },
                    params: {
                        priorityId : self.selectedProject.id
                    }
                }).then(response => {
                    self.getTasks(self.selectedProject).then(function (tasks){
                       this.Data.tasks = tasks;
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            getProjects() {
                axios.get('/project',{
                    params:{
                        all:true
                    }
                }).then(response => {
                    this.Data.projects = response.data.data;
                    this.Data.newSelectedProject = this.Data.projects[0];
                }).catch(function (error) {
                    console.log(error);
                });
            },
            showEditTaskModal (task, selectedTaskIndex) {
                this.$modal.show('edit-task-modal', {task: JSON.parse(JSON.stringify(task)), selectedTaskIndex: selectedTaskIndex, projects: this.Data.projects, selectedProject: this.selectedProject});
            },

        },
        watch: {
            'selectedProject' : function(selectedProject) {
                this.setSelectedProject(selectedProject);
            }
        }

    }
</script>

<style scoped>
    .merge-left {
        border-top-left-radius: unset;
        border-bottom-left-radius: unset;
    }
    .task-item:first-child {
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    .task-item:last-child {
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    .task-item {
        margin: 0 5px 0 5px;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    .task-item div {
        line-height: 36px;
    }
    .btn-info {
        color: #ffffff;
    }
    .noDrop {
        cursor: no-drop;
    }
    .drop {
        cursor: grab;
    }
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
    .btn-refresh {
        border: 1px solid rgba(60,60,60,.26);
    }

</style>

<style>
    .input-grouped-v-select .vs__dropdown-toggle {
        border-radius: unset !important;
        min-width: 200px;
    }

</style>
