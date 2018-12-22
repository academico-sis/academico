<template>

                    <form action="/courses" method="post">
                        
                        <div class="input-group">
                            <label for="period">{{ ucfirst(trans_choice('academico.periods', 1)) }}</label>
                            <select v-model="period" name="period" id="period">
                                    @foreach ($periods as $period)
                                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                                    @endforeach
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="start">{{ ucfirst(trans_choice('academico.start', 1)) }}</label>
                            <input type="date" id="start" value={{ $current_period->start }}>
                        </div>

                        <div class="input-group">
                            <label for="end">{{ ucfirst(trans_choice('academico.end', 1)) }}</label>
                            <input type="date" id="end" value={{ $current_period->end }}>
                        </div>

                        <div class="input-group">
                            <label for="rythms">{{ ucfirst(trans_choice('academico.rythms', 1)) }}</label>
                            <select name="rythm" id="rythm">
                                @foreach ($rythms as $rythm)
                                    <option value="{{ $rythm->id }}">{{ $rythm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="levels">{{ ucfirst(trans_choice('academico.levels', 1)) }}</label>
                            <select name="level" id="level">
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="name">{{ ucfirst(trans_choice('academico.name', 1)) }}</label>
                            <input type="text" id="name">
                        </div>

                        <div class="input-group">
                            <label for="volume">{{ ucfirst(trans_choice('academico.volume', 1)) }}</label>
                            <input type="numeric" step="0.5" id="volume">
                        </div>

                        <div class="input-group">
                            <label for="price">{{ ucfirst(trans_choice('academico.price', 1)) }}</label>
                            <input type="numeric" step="0.01" id="price">
                        </div>

                        <div class="input-group">
                            <button type="submit">@lang_u('academico.validate')</button>
                        </div>
                    </form>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
