<template>
	<div id="app">
		<ValidationObserver ref="observer" v-slot="{ valid }">
		
		<section class="section">
        <div class="container has-text-centered has-text-link is-size-4">
        	{{ $t('profile_picture') }}        
        </div>
        </section>

        <section class="section">
        <div class="tile is-ancestor">
        	<div class="tile is-6 is-vertical is-parent">

        		<div class="tile is-child">
					<div class="file">
					  <label class="file-label">
					    <ValidationProvider
					    rules="image"
					    v-slot="{ errors}"
					    name="Picture upload">
						    <input class="file-input" type="file" accept="image/png, image/jpeg"
						    @change="onFileChange">
						</ValidationProvider>
					    <span class="file-cta has-text-white" style="background-color: #7957d5;">
					      <span class="file-icon">
					        <i class="fas fa-upload"></i>
					      </span>
					      <span class="file-label">
					        {{ $t('click_upload') }}
					      </span>
					    </span>
					  </label>
			            <p class="help is-danger">{{ errors[0] }}</p>
	        			</p>
					</div>
	        	</div>

        		<div class="tile is-child">
						<b-button type="is-primary" @click="enableUserToTakePicture"
						id="enableCapture"> {{ $t("take_picture") }} </b-button>
			    </div>

				<div class="tile is-child">
		            <b-button type="is-primary" @click="validateBeforeSubmit()">
		            	{{$t("next")}}
		            </b-button>
				</div>
	    </div>

		    <div class="tile is-vertical is-parent">
				<div class="tile is-child">
					<figure class="image">
					     <canvas id="canvas1" width="315" height="180" style="position:relative; border:1px solid #000000;"></canvas>
					</figure>
				</div>
		    	<div class="tile is-child">
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
		</section>
        </ValidationObserver>
	</div>
</template>

<script>
	
import { store } from "./store.js";
import { EventBus } from "./eventBus.js";
import { ValidationObserver } from "vee-validate";

export default {

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
		};
    },

    // TODO: Add deletion of visual representation of uploaded picture on the site.

	mounted () {
		 this.player = document.getElementById('player');
		 this.canvas = document.getElementById('canvas1');
		 this.context = this.canvas.getContext('2d');

	},

    methods: {


    	// Checks the image size then sets the userPicture variable to the uploaded picture 
    	onFileChange(e) {
                const image = e.target.files[0];
                if(image.size > 3145728 ){
	                this.$buefy.toast.open({
	                    message:
	                        "The image is too large. Maximum size is 2mb.",
	                    type: "is-danger",
	                    position: "is-bottom",
	                });
                }else{
                const reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e =>{
                    this.userPicture = e.target.result;
                    console.log(this.userPicture);
                };
            }
    	},

        async validateBeforeSubmit() {

    		if(this.validateBase64(this.userPicture.substring(23))){
    				this.updateData();
    		}else{
                this.$buefy.toast.open({
                    // Couldn't think of something better.
                    message:
                        "Something went wrong. Try again.",
                    type: "is-danger",
                    position: "is-bottom",
                });
    		}
        },

        // This checks if the base64 string has only valid base64 string 
        // elements.
        validateBase64(string64) {
        	var base64regex = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/;

        	return base64regex.test(string64);
        },

        // Asks the user for permission to use their attached media and  
        // activates the video stream.
    	enableUserToTakePicture() {

    	// Specifying the media to request, along with the requirements.
		const constraints = {
			audio:false,
			video: { width: 720, height: 480 }			
		};

		// Asks the user for permission and starts the stream.
		navigator.mediaDevices.getUserMedia(constraints)
		  .then((stream) => {
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

    	},
 
        updateData() {
        	if(this.userPicture != null) {
        		store.updatePictureData(this.userPicture);
        		EventBus.$emit("moveToNextStep");
        	}else {
                this.$buefy.toast.open({
                    message:
                        "Please provide a profile picture.",
                    type: "is-danger",
                    position: "is-bottom",
                });
        	}
        },

    },
};


</script>