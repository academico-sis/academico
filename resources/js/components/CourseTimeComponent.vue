<template>
    <div id="parent">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    {{ $t("front.Add a new course time") }}
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>
                                <select id="day" v-model="day" name="day">
                                    <option value="1">{{
                                        $t("front.Monday")
                                    }}</option>
                                    <option value="2">{{
                                        $t("front.Tuesday")
                                    }}</option>
                                    <option value="3">{{
                                        $t("front.Wednesday")
                                    }}</option>
                                    <option value="4">{{
                                        $t("front.Thursday")
                                    }}</option>
                                    <option value="5">{{
                                        $t("front.Friday")
                                    }}</option>
                                    <option value="6">{{
                                        $t("front.Saturday")
                                    }}</option>
                                    <option value="0">{{
                                        $t("front.Sunday")
                                    }}</option>
                                </select>
                            </th>

                            <th>
                                <input
                                    id="start"
                                    v-model="start"
                                    type="time"
                                    name="start"
                                />
                            </th>

                            <th>
                                <input
                                    id="end"
                                    v-model="end"
                                    type="time"
                                    name="end"
                                />
                            </th>

                            <th>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-success"
                                    @click="addTime()"
                                >
                                    <i class="la la-plus"></i>
                                </button>
                            </th>
                        </thead>

                        <tbody>
                            <tr v-for="time in times" :key="time.id">
                                <td>{{ time.day }}</td>
                                <td>{{ time.start }}</td>
                                <td>{{ time.end }}</td>
                                <td>
                                    <a @click="removeTime(time)"
                                        >(<i class="la la-times"></i>)</a
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    {{ $t("Events") }}
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>{{ $t("Start") }}</th>
                            <th>{{ $t("End") }}</th>
                        </thead>

                        <tbody>
                            <tr v-for="event in events" :key="event.id">
                                <td>{{ event.start }}</td>
                                <td>{{ event.end }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["course"],

    data() {
        return {
            times: null,
            events: null,
            loading: true,
            errored: false,
            day: null,
            start: null,
            end: null,
        };
    },

    mounted() {
        this.getTimes();
        this.getEvents();
    },

    methods: {
        getTimes() {
            axios
                .get("/coursetime/" + this.course + "/get")
                .then((response) => {
                    this.times = response.data;
                })
                .catch((error) => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        getEvents() {
            axios
                .get("/course/" + this.course + "/events/get")
                .then((response) => {
                    this.events = response.data;
                })
                .catch((error) => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => (this.loading = false));
        },

        addTime() {
            axios
                .post("/coursetime/" + this.course, {
                    day: this.day,
                    start: this.start,
                    end: this.end,
                })
                .then((response) => {
                    this.getTimes();
                    this.getEvents();
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },

        removeTime(time) {
            axios
                .delete("/coursetime/" + time.id, {
                    time,
                })
                .then((response) => {
                    this.getTimes();
                    this.getEvents();
                })
                .catch((e) => {
                    this.errors.push(e);
                });
        },
    },
};
</script>
