export const addTaskServerValidationMixin = {
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
        addTaskServerValidationMixin: function (error,miniToastr,self) {
            let namedError = false;
            if (error.response !== undefined) {
                if (error.response.data !== undefined) {
                    if (error.response.data.name !== undefined) {
                        if (error.response.data.name[0] !== undefined) {
                            namedError = true;
                            miniToastr.error(error.response.data.name[0], "Error");
                            self.UI.newTaskInputError = true;
                            self.UI.newTaskErrorMessage = error.response.data.name[0];
                            this.$Progress.fail();
                        }
                    }
                }
                if (!namedError) {
                    miniToastr.error("Failed to Add Task.", "Error");
                    self.UI.newTaskInputError = true;
                    self.UI.newTaskErrorMessage = 'Failed to Add Task.';
                    this.$Progress.fail();
                }
            } else {
                miniToastr.error("Failed to Add Task.", "Error");
                self.UI.newTaskInputError = true;
                self.UI.newTaskErrorMessage = 'Failed to Add Task.';
                this.$Progress.fail();
            }
        }
    },
};
