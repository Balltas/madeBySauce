

<p align="center"><img src="https://www.madebysauce.com/addons/shared_addons/themes/madebysauce/img/sauce.svg" width="400"></p>

## Technical Challenge

1. Set up an environment. 
    - PHP version - 7.1.9
    - Apache - 2.4.27
    - mySql - 5.7.19
    
2. clone this repo and run - **composer install**
3. Setup a database and change .env accordingly
4. Run migrations - **php artisan migrate**
5. Run **php artisan currencies:import**

## Some notes
- You can switch currency exchange provider by changing config/currency.php. 
I noticed that some currencies has the same name but different code provided by different parties.
The currency exchange rate also differs. As long as its only a test I didn't take it to consideration.

- Because currency rates updates once per day, its good to cash it to database.
So I created migrations and made import service. Created artisan command for it. 
It also can be sheduled daily. 

- Floatrates had very good data format, so it was easy to import data. Fxexchange wasn't
so conveniente so I had to use regulax expresions to extract data from xml.

- On the job interview I kept in mind that you asked about service containers. So I injected
Specific service class based on config/currency.php selected provider that both of them shared the same 
interface. Check also App\Providers\CurrencyExchangeServiceProvider.

- As far as security goes, There is room for improvement because I used only simple validator
when api is called. I have also used some checks when the data is imported form XML.

- For a simplicity, simpleXML was used for xml parsing as the files are not big and its sufficient.
But I didnt scale the task in that way, that enother xml parser would be replaced.

This is only part 1 - The API endpoint

## Technical Challenge Part 2

- For vue.js compilation I was using npm - 6.12.0