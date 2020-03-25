<template>
  <div>
    <div class="container" v-if="loading">loading...</div>

    <div class="container" v-if="!loading">
      <b-card :header="'Welcome, ' + account.name" class="mt-3">
        <b-card-text>
          <div>
            Account: <code>{{ account.id }}</code>
          </div>
          <div>
            Balance:
            <code
              >{{ account.currency === "usd" ? "$" : "€"
              }}{{ account.balance }}</code
            >
          </div>
        </b-card-text>
        <b-button size="sm" variant="success" @click="show = !show"
          >New payment</b-button
        >

        <b-button
          class="float-right"
          variant="danger"
          size="sm"
          nuxt-link
          to="/"
          >Logout</b-button
        >
      </b-card>

      <b-card class="mt-3" header="New Payment" v-show="show">
        <b-form @submit.prevent="onSubmit">
          <b-form-group id="input-group-1" label="To:" label-for="input-1">
            <b-form-input
              id="input-1"
              size="sm"
              v-model="payment.to"
              type="number"
              required
              placeholder="Destination ID"
            ></b-form-input>
          </b-form-group>

          <b-form-group id="input-group-2" label="Amount:" label-for="input-2">
            <b-input-group prepend="$" size="sm">
              <b-form-input
                id="input-2"
                v-model="payment.amount"
                type="number"
                required
                placeholder="Amount"
              ></b-form-input>
            </b-input-group>
          </b-form-group>

          <b-form-group id="input-group-3" label="Details:" label-for="input-3">
            <b-form-input
              id="input-3"
              size="sm"
              v-model="payment.details"
              required
              placeholder="Payment details"
            ></b-form-input>
          </b-form-group>

          <b-button type="submit" size="sm" variant="primary">Submit</b-button>
        </b-form>
      </b-card>

      <b-card class="mt-3" header="Payment History">
        <b-table striped hover :items="transactions"></b-table>
      </b-card>
    </div>
  </div>
</template>

<script lang="ts">
import axios from "axios";
import Vue from "vue";

export default {
  data() {
    return {
      show: false,
      payment: {},

      account: null,
      transactions: [],

      loading: true
    };
  },

  async mounted() {
    this.refreshData();
  },

  methods: {
    async refreshData() {
      try {
        await this.fetchAccount();
      } catch (err) {
        console.error(err);

        window.location = "/";

        return false;
      }

      this.fetchTransactions();
    },

    async fetchAccount() {
      return new Promise((resolve, reject) => {
        axios
          .get(`http://localhost:8000/api/accounts/${this.$route.params.id}`)
          .then((response) => {
            if (!response.data.length) {
              reject('Invalid account!');
            } else {
              this.account = response.data[0];

              if (this.account && this.transactions) {
                this.loading = false;
              }

              resolve();
            }
          });
      });
    },

    fetchTransactions() {
      axios
        .get(
          `http://localhost:8000/api/accounts/${
            this.$route.params.id
          }/transactions`
        )
        .then((response) => {
          this.transactions.splice(0);

          for (let transaction of response.data) {
            transaction.amount =
              (this.account.currency === "usd" ? "$" : "€") +
              transaction.amount;

            if (this.account.id != transaction.to) {
              transaction.amount = "-" + transaction.amount;
            }

            this.transactions.push(transaction);
          }

          if (this.account && this.transactions) {
            this.loading = false;
          }
        });
    },

    onSubmit() {
      axios.post(
        `http://localhost:8000/api/accounts/${
          this.$route.params.id
        }/transactions`,

        this.payment
      )
      .then((response) => {
        this.refreshData();
      });

      this.payment = {};
      this.show = false;
    }
  }
};
</script>
