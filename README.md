# Validator-Class
This Validate Class is for those who are looking for a validator that returns a code for every each error (Laravel/Api)

# Requirements
Add ValidationClass <br><br>
![Screenshot 2022-07-15 182325](https://user-images.githubusercontent.com/79410109/179263454-b4355e87-2d6f-4319-a56e-e1604505508b.png)

 Add ValidationException & CustomException <br><br>
![Screenshot 2022-07-15 182133](https://user-images.githubusercontent.com/79410109/179263514-10282872-de9f-4b94-b7a2-376c54f57368.png)

# Usage
For Using The ValidationClass we have to add that in our controller <br><br>
![Screenshot 2022-07-15 191630](https://user-images.githubusercontent.com/79410109/179263971-2fdc7a39-46f2-4bf9-88df-ce39481eeea2.png)

We have to do some more requirement in our controller <br>
passed the model to class <br><br>
![Screenshot 2022-07-15 191653](https://user-images.githubusercontent.com/79410109/179264722-7010a34d-5bf4-4ef5-88ce-10d069cf8f80.png)

Now is the time to config our validation_rules <br><br>
![Screenshot 2022-07-15 211654](https://user-images.githubusercontent.com/79410109/179270221-dbf127c6-fc15-46c1-8fa6-747709db0d7b.png)

valid validation keys:
1. string
2. int
3. bool
4. email
5. required
6. unqiue : ["unqiue" => false] // default is false but if it was true thats mean except himself in table(using in update stuff)
7. collection : ["collection" => ["cars", "motorcycles"]]
8. file_types : ["file" => ["types" => ["jpg", "png"]]]
9. max or min of char : ["max" => "255", "min" => "6"]
10. file_max_size : ["file" => ["max" => "5000000"]]

After configuring validation rules we have to passed the parameters to validate method <br><br>
![Screenshot 2022-07-15 212109](https://user-images.githubusercontent.com/79410109/179270693-3f174a10-be9a-4eb5-ba06-cd9e835e5fc1.png)

parameters :
1. data($request) *
2. validation_rule *
3. url_id
4. return_data(bool)
5. return_error(bool)

# Developers
* mrcrgeek(barbod_alinezhad) : ValidatorClass & ValidationException
* Github = https://github.com/mrcrgeek
* Alireza_Habibzade : CustomException
* Github = https://github.com/thePowerOfCreation21
