# Laravel Nuxt App

Hi, here is a summary of the changes and improvements I made to this **Laravel Nuxt Bank Project**. 


## Validation

I am using Laravel **Validation** .

## Refactoring

- Routing with Controller's functions
- Model Creation 
- Changing hardcoded uri's with dotenv variables at the **Front-end**

## Testing

Feature Testing on API endpoints

## Improvements

- **Currency** table migration, seed and endpoint
- Every account has a base currency
- Every transaction has converted amount to **receiver's currency**
- GET transactions currency return amounts converted to the account id. For example :
> api/accounts/1/transactions	// Will get response with $ converted amounts
>
> api/accounts/2/transactions	// Will get response with € or £
- I have include three currencies - with hardcoded values. Maybe in the future a worker and a external api will help with the exchange rates
	- Currency symbols in api responses, at the Frond-end (history and create transaction)
	- **UX** Success and error messages
	-  Http Status Codes


Contact me : stefanoscoder \at| gmail com
