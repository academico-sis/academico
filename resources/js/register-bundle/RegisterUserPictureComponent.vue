<template>
	<div id="app">
		<ValidationObserver ref="observer" v-slot="{ valid }">

		<section class="section">
        <div class="container has-text-centered has-text-link is-size-4">
            <p>{{ $t('profile_picture') }}</p>

            <b-button v-if="!picturemandatory && !userPicture" type="is-primary" @click="validateBeforeSubmit()">
                {{$t("Skip without adding a picture")}}
            </b-button>
        </div>
        </section>

        <section class="section">
        <div class="tile is-ancestor">
        	<div class="tile is-6 is-vertical is-parent">

        		<div class="tile is-child">
                    <b-field class="file is-primary" :class="{'has-name': !!userPicture}">
                        <b-upload v-model="userPicture" class="file-label" accept="image/png, image/jpeg">
                            <span class="file-cta">
                                <b-icon class="file-icon" icon="upload"></b-icon>
                                <span class="file-label">{{ $t('click_upload') }}</span>
                            </span>
                            <span class="file-name" v-if="userPicture">
                                {{ userPicture.name }}
                            </span>
                        </b-upload>
                    </b-field>
	        	</div>

        		<div class="tile is-child">
						<b-button type="is-primary" @click="enableUserToTakePicture"
						id="enableCapture"> {{ $t("take_picture") }} </b-button>
			    </div>

	        </div>

            <div class="tile is-vertical is-parent">
                <div class="tile is-child" v-show="showPreview">
                    <figure class="image">
                         <canvas id="canvas1" width="315" height="180" style="position:relative; border:1px solid #000000;"></canvas>
                    </figure>
                </div>
                <div class="tile is-child" v-show="!showPreview">
                        <video id="player" autoplay
                        style="position: relative; border:1px solid #000000;"
                        width="320" height="250"></video>
                </div>
                <div class="tile is-child mt-20">
                    <div id="captureButton" style="display: none;">
                        <b-button type="is-primary" @click="takeUserPicture">{{ $t("snap_picture") }}</b-button>
                    </div>
                </div>
            </div>
		</div>

        <div class="has-text-centered has-text-link is-size-4">
            <b-button v-if="userPicture" type="is-primary" @click="validateBeforeSubmit()">
                {{$t("next")}}
            </b-button>
        </div>

		</section>
        </ValidationObserver>
	</div>
</template>

<script>

import { store } from "./store.js";
import { EventBus } from "./eventBus.js";
import { ValidationObserver } from "vee-validate";

export default {
    props: ['picturemandatory'],

    components: {
        ValidationObserver,
    },

    data() {
	    	return{
	    		errors: [],
				player: null,
				canvas: null,
				context: null,
	            userPicture: null,
                showPreview: false,
		};
    },

    // TODO: Add deletion of visual representation of uploaded picture on the site.

	mounted () {
		 this.player = document.getElementById('player');
		 this.canvas = document.getElementById('canvas1');
		 this.context = this.canvas.getContext('2d');

	},

    methods: {

        async validateBeforeSubmit() {
            this.updateData();
        },

        // Asks the user for permission to use their attached media and
        // activates the video stream.
    	enableUserToTakePicture() {
            this.showPreview = false;

            // Specifying the media to request, along with the requirements.
            const constraints = {
                audio:false,
                video: { width: 720, height: 480 }
            };

            // Asks the user for permission and starts the stream.
            navigator.mediaDevices.getUserMedia(constraints)
              .then(stream => {
                this.player.srcObject = stream;
              });

             // Shows the capture button
             document.getElementById('captureButton').style.display = "block";
    	},

    	// Takes a snapshot of the users video stream and displays it in the
    	// canvas element and saves the image in the userPicture data // variable
    	takeUserPicture() {
            const constraints = {
                audio:false,
                video: { width: 720, height: 480 }
            };

            this.context = this.canvas.getContext('2d');

            // Draws the image to the canvas
            this.context.drawImage(this.player, 0, 0, this.canvas.width, this.canvas.height);
            document.getElementById('captureButton').style.display = "none";

            var userPictureCapture = this.canvas.toDataURL('image/jpeg', 1.0);

            var that = this;

            // Saves the image
            that.userPicture = userPictureCapture;

            // Cuts the stream
            this.player.srcObject.getVideoTracks().forEach(track => track.stop());

            this.showPreview = true;
    	},

        updateData() {
        	if(this.userPicture != null) {
        		store.updatePictureData(this.userPicture);
        	}

            EventBus.$emit("moveToNextStep");
        },

    },
};


</script>
