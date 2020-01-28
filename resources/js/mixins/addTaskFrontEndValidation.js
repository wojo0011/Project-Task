export const addTaskFrontEndValidationMixin = {
    data() {
        return {
            Data: {
                newTask: null,
            },
            UI: {
                newTaskInputError: false,
                newTaskErrorMessage: '',
                newTaskBusy: false,
            },
        }
    },
    methods: {
        addTaskFrontEndValidationMixin: function (miniToastr, self) {
            console.log(self, self.Data, self.UI)
            /** FRONTEND VALIDATE NEW TASK **/
            if (self.Data.newTask != null) {
                /** NEW TASK NAME MINIMUM LENGTH 3 **/
                if (self.Data.newTask.length < 3) {
                    self.UI.newTaskInputError = true;
                    self.UI.newTaskErrorMessage = 'New Task Name Must Be More Than 2 Characters In Length';
                    miniToastr.error("New Task Name Must Be More Than 2 Characters In Length.", "Failed to Add Task");
                    self.UI.newTaskBusy = false;
                    this.$Progress.fail();
                    return false;
                    /** NEW TASK NAME MAXIMUM LENGTH 20 **/
                } else if (this.Data.newTask.length > 20) {
                    self.UI.newTaskInputError = true;
                    self.UI.newTaskErrorMessage = 'New Task Name Must Be Less Than 21 Characters In Length';
                    miniToastr.error("New Task Name Must Be Less Than 21 Characters In Length.", "Failed to Add Task");
                    self.UI.newTaskBusy = false;
                    this.$Progress.fail();
                    return false;
                } else {
                    self.UI.newTaskInputError = false;
                    self.UI.newTaskErrorMessage = null;
                    this.$Progress.increase(25);
                    return true;
                }
            } else {
                self.UI.newTaskInputError = true;
                self.UI.newTaskErrorMessage = 'New Task Name Must Not Be Empty';
                miniToastr.error("New Task Name Must Not Be Empty.", "Failed to Add Task");
                self.UI.newTaskBusy = false;
                this.$Progress.fail();
                return false;
            }
        }
    },
}
