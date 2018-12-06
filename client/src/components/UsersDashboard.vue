<template>
	<div>
		<span class="help is-info"  v-if="isLoading">Loading...</span>
		<table class="table" v-else>
			<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			<template v-for="user in users">
				<tr v-bind:key="user.id">
					<td>{{ user.id }}</td>
					<td>{{ user.name }}</td>
					<td>
						<form @submit.prevent="removeUser(user)">
							<button class="button is-primary" v-bind:class="{ 'is-loading' : isLoading }">Remove</button>
						</form>
					</td>
				</tr>
			</template>
			</tbody>
		</table>
		<user-form @completed="addUser"></user-form>
	</div>
</template>

<script>
	import axios from 'axios'
	import UserForm from './UserForm.vue'

	export default {
		components: {
			UserForm
		},
		data() {
			return {
				users: {},
				isLoading: true
			}
		},
		async created () {
			try {
				const response = await axios.get('/users');
				this.users = response.data;
				this.isLoading = false;
			} catch(e) {
				// handle authentication error here
			}
		},
		methods: {
			addUser(user) {
				this.users.push(user)
			},
			removeUser(user) {

			}
		}
	}
</script>