<template>
<div>
    <div v-if="this.teacher" class="col-md-6">
                        <p>The following classes are registered with a different teacher</p>
                        
    </div>
</div>
</template>



<script>

    export default {
        props: ['data', 'teacherPatchRoute', 'roomPatchRoute'],
          
        data() {
            return {
                showRoomDifferences: true,
                showTeacherDifferences: true,
            };
        },

        mounted() {

        },

        methods: {
            syncEvents()
                {
                    this.syncEventsRoom();
                    this.syncEventsTeacher();
                },
        
            syncEventsTeacher()
            {
                axios
                    .patch(this.teacherPatchRoute)
                    .then(function (response) {
                        new PNotify({
                            title: "Operation successful",
                            text: "The events have been updated.",
                            type: "success"
                            });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
        
                syncEventsRoom()
                {        
                    axios
                        .patch(this.roomPatchRoute)
                        .then(function (response) {
                            new PNotify({
                                title: "Operation successful",
                                text: "The events have been updated.",
                                type: "success"
                                });
                         })
                        .catch(function (error) {
                            console.log(error);
                        });
                }

        }
    }
    
</script>
