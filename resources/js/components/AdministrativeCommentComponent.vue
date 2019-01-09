<template>

<div class="box">
    <div class="box-header with-border">
        <div class="box-title">
            Comments
        </div>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-xs btn-primary">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    
    <div class="box-body">
        <ul>
            <li v-for="comment in comments">
                {{ comment.body }} ({{comment.created_at | moment("D MMM YY") }})
            </li>
        </ul>
<!--     @if (count($student->administrative_comments) > 0)
        @foreach($student->administrative_comments as $comment)
            <p>{{ $comment->body }} ({{ $comment->date }})</p>
        @endforeach
    @else
        <p>@lang('academico.no_comments')</p>
    @endif -->
    </div>
</div>
</template>

<script>
    
    export default {

        props: ['comments'],

        data () {
            return {
                
            }
        },

        mounted() {

        },

        methods: {

        }
    }
</script>
