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
              >{{ currency_symbol
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
        <b-form @submit="onSubmit">
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
            <b-input-group v-bind:prepend=currency_symbol size="sm">
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
      <div class="alert alert-danger mt-3" role="alert"  v-if="error">
        {{ error_message }}
      </div>
      <div class="alert alert-success mt-3" role="success"  v-if="success">
        {{ success_message }}
      </div>
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
      transactions: null,

      loading: true,
      currency_symbol: '',
      
      error: false,
      error_message:'We had an error ',

      success: false,
      success_message:'Transaction was successful '
    };
  },
  watch: {
    error_message: function (val) {
      setTimeout(() => {
        this.error = false
        this.error_message = ''
      },4000)
    },
    success: function (val) {
      setTimeout(() => {
        this.success = false
      },4000)
    }
  },
  mounted() {
    const that = this;

    axios
      .get(`${process.env.apiURL}/accounts/${this.$route.params.id}`)
      .then(function(response) {
        if (!response.data.length) {
          window.location = "/";
        } else {
          that.account = response.data[0];

          if (that.account && that.transactions) {
            that.loading = false;
          }
        }
      }).then(that.getTransactions(that))
      .then(function (){
        that.getCurrencySymbol(that.account)
      });
  },

  methods: {
    onSubmit(evt) {
      var that = this;

      evt.preventDefault();

      axios.post(
        `${process.env.apiURL}/accounts/${
          this.$route.params.id
        }/transactions`,

        this.payment
      ).then( () => {
        that.success = true
      }).catch( (error) => {
        
        if (typeof error.response.data.error !== 'undefined') 
          var message= error.response.data.error

        if (typeof error.response.data.details !== 'undefined') 
          var message= error.response.data.details[0]
        
        if (typeof error.response.data.to !== 'undefined') 
          var message= error.response.data.to[0]

        that.error = true
        that.error_message = message
      });

      that.payment = {};
      that.show = false;

      // update items
      setTimeout(() => {
        axios
          .get(`${process.env.apiURL}/accounts/${this.$route.params.id}`)
          .then(function(response) {
            if (!response.data.length) {
              window.location = "/";
            } else {
              that.account = response.data[0];
            }
          });

        axios
          .get(
            `${process.env.apiURL}/accounts/${
              that.$route.params.id
            }/transactions`
          )
          .then(function(response) {
            that["transactions"] = response.data;

            var transactions = [];
            for (let i = 0; i < that.transactions.length; i++) {
              that.transactions[i].amount =
                (that.account.currency === "USD" ? "$" : "€") +
                that.transactions[i].amount;

              if (that.account.id != that.transactions[i].to) {
                that.transactions[i].amount = "-" + that.transactions[i].amount;
              }

              transactions.push(that.transactions[i]);
            }

            that.transactions = transactions;
          });
      }, 400);
    },
    getTransactions(that) {
          axios
      .get(
        `${process.env.apiURL}/accounts/${
          that.$route.params.id
        }/transactions`
      )
      .then(function(response) {
        that["transactions"] = response.data;

        var transactions = [];
        for (let i = 0; i < that.transactions.length; i++) {

          if (that.account.id != that.transactions[i].to) {
            that.transactions[i].amount = "-" + that.transactions[i].amount;
          }

          transactions.push(that.transactions[i]);
        }

        that.transactions = transactions;

        if (that.account && that.transactions) {
          that.loading = false;
        }
      });
    },
    getCurrencySymbol(account) {
      var that = this;
      axios
      .get(
        `${process.env.apiURL}/currencies`
      )
      .then(function(response) {
        for (let el of response.data) {
          if(account.currency_id==el.id){
            that.currency_symbol = el.symbol+' '
            break
          }
        }
      });
    }

  }
};
</script>
