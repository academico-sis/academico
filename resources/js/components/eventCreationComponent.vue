<template>
<div class="row">
  <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Créer une ou des nouvelle(s) classe(s)
            </div>
            
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Répéter les :</label>
                        <div class="col-md-10 col-form-label">
                          <div v-for="day in days" :key="day.day" class="form-check form-check-inline mr-1">
                            <input v-model="day.selected" class="form-check-input ml-3" :id="day.day" type="checkbox" value="L">
                            <label class="form-check-label" :for="day.day">{{ day.day }}</label>
                          </div>
                        </div>
                      </div>

                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="start_date">du</label>
                        <input class="form-control" type="date" id="start_date" v-model="startdate">
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="end_date">au</label>
                        <input class="form-control" type="date" id="end_date" v-model="enddate">
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" type="text" id="name" v-model="name">
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-form-label" for="teacher">Teacher</label>
                        <select class="form-control" id="teacher" name="teacher" v-model="teacher">
                        <option value="null">No teacher yet</option>
                        <option v-for="teacher in this.teachers" :value="teacher.id">{{ teacher.name }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="col-md-3 col-form-label" for="room">Room</label>
                        <select class="form-control" id="room" name="room" v-model="room">
                        <option value="null">No room yet</option>
                        <option v-for="room in this.rooms" :value="room.id">{{ room.name }}</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Les classes suivantes seront créées :
            </div>
            
            <div class="card-body">
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ['teachers', 'rooms'],
    data() {
        return {
            days: [
                {selected: false, day: 'L'},
                {selected: false, day: 'M'},
                {selected: false, day: 'X'},
                {selected: false, day: 'J'},
                {selected: false, day: 'V'},
                {selected: false, day: 'S'},
            ],
            teacher: null,
            room: null,
            startdate: null,
            enddate: null,
            name: null,
        }
    },
    computed: {
    	selectedDays: function() {
      	return this.days.filter(function(day) {
        	return day.selected;
        }).map(function(day) {
        	return day.day;
        });
      },

      createdEvents: function()
      {
          var current = start
          while (current < end) {
              current ++
              
          }
      }
      
}
}
</script>

<style>

</style>