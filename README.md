# Finansų apskaitos sistema

PHP bei Laravel karkaso pagrindu sukurta finansų apskaitos sistema asmeninių išlaidų ir pajamų sekimui.

---

Reikalavimai:

- PHP 8.1+
- XAMPP (naudojamas htdocs aplankas)
- MySQL
- Composer
- Node.js + NPM

---

Projekto paleidimas (lokaliai su XAMPP):

1. Parsisiųsk arba įkelk projektą į `htdocs`:

   C:\xampp\htdocs

2. Sukurk `.env` failą:

   cp .env.example .env

3. Nurodyk savo duomenų bazės prisijungimus `.env` faile:

   DB_DATABASE=finansu_apskaita  
   DB_USERNAME=root  
   DB_PASSWORD=

4. Sukurk duomenų bazę per phpMyAdmin:

   Atsidaryk http://localhost/phpmyadmin ir sukurk naują DB: `finansu_apskaita`

5. Įdiek Composer:

   composer install

6. Sugeneruok aplikacijos raktą:

   php artisan key:generate

7. Paleisk migracijas:

   php artisan migrate

8. Įrašyk bandomuosius duomenis (jei reikia):

   php artisan db:seed --class=DummyTransactionSeeder

---

Front-end paruošimas:

1. Įdiek npm:

   npm install

2. Paleisk Vite su `npm run dev`:

   npm run dev

---

Prisijungimas:

Sistema palaiko vartotojų registraciją ir prisijungimą.  
