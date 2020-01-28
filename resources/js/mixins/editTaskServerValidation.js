export const editTaskServerValidationMixin = {
    data() {
        return {
            Data: {
                editTask: null,
            },
            UI: {
                editTaskInputError: false,
                editTaskInputErrorMessage: '',
                editTaskBusy: false,
            },
        }
    },
    methods: {
        editTaskServerValidationMixin: function (error,miniToastr,self) {
            let namedError = false;
            if (error.response !== undefined) {
                if (error.response.data !== undefined) {
                    if (error.response.data.name !== undefined) {
                        if (error.response.data.name[0] !== undefined) {
                            namedError = true;
                            miniToastr.error(error.response.data.name[0], "Error");
                            self.UI.editTaskInputError = true;
                            self.UI.editTaskInputErrorMessage = error.response.data.name[0];
                            self.UI.editTaskBusy = false;
                            this.$Progress.fail();
                        }
                    }
                }
                if (!namedError) {
                    miniToastr.error("Failed to Update Task.", "Error");
                    self.UI.editTaskInputError = true;
                    self.UI.editTaskInputErrorMessage = 'Failed to Update Task.';
                    miniToastr.error('Failed to Update Task.', "Error");
                    self.UI.editTaskBusy = false;
                    this.$Progress.fail();
                }
            } else {
                miniToastr.error("Failed to Update Task.", "Error");
                self.UI.editTaskInputError = true;
                self.UI.editTaskInputErrorMessage = 'Failed to Update Task.';
                miniToastr.error('Failed to Update Task.', "Error");
                self.UI.editTaskBusy = false;
                this.$Progress.fail();
            }
        }
    },
};
