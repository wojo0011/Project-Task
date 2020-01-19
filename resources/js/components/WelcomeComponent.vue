<template>
    <div>
        <project-selection-component></project-selection-component>
        <task-component :selectedProject="selectedProject"></task-component>
    </div>
</template>
<script>

    export default {
        data() {
            return {
                selectedProject: null
            }
        },
        mounted: function() {
                this.$root.$on('selected_project',(selectedProject) => {
                this.selectedProject = selectedProject;
            });
        },
        methods: {
        }
    }
</script>

<style scoped>
</style>
