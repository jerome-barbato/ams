<template>
	<form @submit.prevent="onSubmit">
		<span class="help is-danger" v-text="errors"></span>

		<div class="field">
			<div class="control">
				<input class="input" type="text" placeholder="enter militant first name..." v-model="firstName" @keydown="errors = ''">
				<input class="input" type="text" placeholder="enter militant last name..." v-model="lastName" @keydown="errors = ''">
				<input class="input" type="text" placeholder="enter militant email..." v-model="email" @keydown="errors = ''">
			</div>
		</div>

		<button class="button is-primary" v-bind:class="{ 'is-loading' : isLoading }">Add Militant</button>
	</form>
</template>

<script>
	import axios from 'axios'

	export default {
		data() {
			return {
				firstName: '',
				lastName: '',
				email: '',
				errors: '',
				isLoading: false
			}
		},
		methods: {
			onSubmit() {
				this.isLoading = true;
				this.postMilitant();
			},
			async postMilitant() {
				axios.post('/militants', this.$data)
					.then(response => {
						this.firstName = '';
						this.isLoading = false;
						this.$emit('completed', response.data)
					})
					.catch(error => {
						// handle authentication and validation errors here
						this.errors = error.response.data.errors;
						this.isLoading = false;
					})
			}
		}
	}
</script>