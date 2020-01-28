export const editTaskFrontEndValidationMixin = {
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
                editTaskInputError: false,
                editTaskInputErrorMessage: false
            },
        }
    },
    methods: {
        editTaskFrontEndValidationMixin: function (miniToastr, self) {
            console.log(self, self.Data, self.UI)
            /** FRONTEND VALIDATE NEW TASK **/
            if (self.Data.editTask.name != null) {
                /** NEW TASK NAME MINIMUM LENGTH 3 **/
                if (self.Data.editTask.name.length < 3) {
                    self.UI.editTaskInputError = true;
                    self.UI.editTaskInputErrorMessage = 'New Task Name Must Be More Than 2 Characters In Length';
                    miniToastr.error("New Task Name Must Be More Than 2 Characters In Length.", "Failed to Add Task");
                    self.UI.editTaskBusy = false;
                    this.$Progress.fail();
                    return false;
                    /** NEW TASK NAME MAXIMUM LENGTH 20 **/
                } else if (this.Data.editTask.name.length > 20) {
                    self.UI.editTaskInputError = true;
                    self.UI.editTaskInputErrorMessage = 'New Task Name Must Be Less Than 21 Characters In Length';
                    miniToastr.error("New Task Name Must Be Less Than 21 Characters In Length.", "Failed to Add Task");
                    self.UI.editTaskBusy = false;
                    this.$Progress.fail();
                    return false;
                } else {
                    self.UI.editTaskInputError = false;
                    self.UI.editTaskInputErrorMessage = null;
                    this.$Progress.increase(25);
                    return true;
                }
            } else {
                self.UI.editTaskInputError = true;
                self.UI.editTaskInputErrorMessage = 'New Task Name Must Not Be Empty';
                miniToastr.error("New Task Name Must Not Be Empty.", "Failed to Add Task");
                self.UI.editTaskBusy = false;
                this.$Progress.fail();
                return false;
            }
        }
    },
}
