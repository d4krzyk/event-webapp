# Event WebApp

Aplikacja do zarządzania wydarzeniami, napisana w PHP z wykorzystaniem frameworka Symfony oraz Dockera. Pozwala na rejestrację użytkowników, zarządzanie wydarzeniami, lokalizacjami, kategoriami, przypomnieniami oraz obsługę ról administracyjnych.

## Wymagania wstępne

- Docker i Docker Compose
- PHP 8.1+ (jeśli chcesz uruchamiać lokalnie bez Dockera)
- Composer
- Node.js (jeśli chcesz korzystać z assetów frontendowych)
- Symfony 6+ (zalecane: zainstalowany globalnie lub przez Composer)

## Konfiguracja środowiska

1. **Sklonuj repozytorium:**
   ```bash
   git clone <adres_repozytorium>
   cd event-webapp
   ```

2. **Skonfiguruj plik środowiskowy:**
   - Skopiuj plik `.env.example` do `.env`:
     ```bash
     cp .env.example .env
     ```
   - Uzupełnij wartości w `.env` (np. dane do bazy, MAILER_DSN, POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD). Przykład:
     ```env
     APP_ENV=dev
     DATABASE_URL="pgsql://user123:haslo123@db:5432/wtw-db?serverVersion=17&charset=utf8"
     MAILER_DSN=smtp://localhost:1025
     POSTGRES_DB=wtw-db
     POSTGRES_USER=user123
     POSTGRES_PASSWORD=haslo123
     ```

3. **Uruchom aplikację w Dockerze:**
   ```bash
   docker-compose up --build
   ```
   - Baza danych PostgreSQL zostanie utworzona automatycznie.

4. **Zainstaluj zależności PHP (Composer):**
   ```bash
   composer install
   ```
   To polecenie pobierze wszystkie wymagane paczki do folderu `vendor`.

5. **Uruchom backend Symfony lokalnie:**
   ```bash
   symfony server:start
   ```
   - Aplikacja będzie dostępna pod adresem: http://localhost:8000

6. **Wykonaj migracje bazy danych:**
   ```bash
   docker-compose exec php php bin/console doctrine:migrations:migrate
   ```
   (lub lokalnie: `php bin/console doctrine:migrations:migrate`)

## Konfiguracja mailera (obsługa wysyłki e-maili)

1. **Domyślna konfiguracja (deweloperska):**
   - W pliku `.env`:
     ```env
     MAILER_DSN=smtp://localhost:1025
     ```
   - Możesz użyć narzędzia [MailHog](https://github.com/mailhog/MailHog) lub [Mailpit](https://github.com/axllent/mailpit) do podglądu wysyłanych maili:
     ```bash
     docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog
     ```
   - Podgląd maili: http://localhost:8025

2. **Konfiguracja produkcyjna:**
   - Skonfiguruj `MAILER_DSN` zgodnie z danymi Twojego SMTP, np.:
     ```env
     MAILER_DSN=smtp://user:haslo@smtp.twojadomena.pl:587
     ```

## Konfiguracja bazy danych z Docker Compose

W pliku `docker-compose.yml` wartości środowiskowe bazy danych (POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD) są ustawione domyślnie oraz pobierane z pliku `.env`. Możesz je zmienić w pliku `.env` lub bezpośrednio w `docker-compose.yml`.

Przykład domyślnej konfiguracji:
```yaml
db:
  image: postgres:17
  environment:
    POSTGRES_DB: ${POSTGRES_DB}
    POSTGRES_USER: ${POSTGRES_USER}
    POSTGRES_PASSWORD: ${POSTGRES_PASSWORD}
  env_file:
    - .env
  ports:
    - "5432:5432"
```

Pamiętaj, aby wartości w `.env` dla POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD były zgodne z tymi w `DATABASE_URL` oraz w `docker-compose.yml`.

```env
DATABASE_URL="pgsql://user123:haslo123@db:5432/wtw-db?serverVersion=17&charset=utf8"
```

## Uruchomienie aplikacji

1. **Start aplikacji:**
   - W Dockerze: `docker-compose up`
   - Lokalnie: `symfony serve` lub `php -S localhost:8000 -t public`

2. **Dostęp do panelu administracyjnego:**
   - Po rejestracji użytkownika nadaj mu rolę `ROLE_ADMIN` w bazie danych lub przez panel admina.
   - Panel admina: http://localhost:8080/admin

3. **Tworzenie konta i logowanie:**
   - Rejestracja: http://localhost:8080/register
   - Logowanie: http://localhost:8080/login

## Przydatne komendy

- Wykonanie migracji: `php bin/console doctrine:migrations:migrate`
- Wysyłka przypomnień: `php bin/console reminder:send`
- Ustawianie dat przypomnień: `php bin/console app:set-reminder-dates`

## Automatyzacja zadań (CRON)

W projekcie znajduje się gotowy plik crontab (`docker/php/crontab`), który jest kopiowany do obrazu Dockera i uruchamia zadania automatycznie:

- Codziennie o 4:00: ustawia daty przypomnień (`app:set-reminder-dates`)
- Co godzinę: wysyła przypomnienia (`reminder:send`)

Przykładowa zawartość pliku:

```
0 4 * * * www-data php /var/www/html/bin/console app:set-reminder-dates >> /var/log/cron_reminder_dates.log 2>&1
0 * * * * www-data php /var/www/html/bin/console reminder:send >> /var/log/cron_reminder_send.log 2>&1
```

Nie musisz samodzielnie konfigurować crona – zadania uruchamiają się automatycznie w kontenerze PHP po zbudowaniu obrazu.

## Struktura bazy danych

- Tabele: user, event, category, location, participation, reminder
- Relacje: użytkownik-wydarzenie, wydarzenie-kategoria, wydarzenie-lokalizacja, uczestnictwo-wydarzenie, uczestnictwo-użytkownik, przypomnienie-wydarzenie, przypomnienie-użytkownik

## Wymagane rozszerzenia PHP

Upewnij się, że w pliku konfiguracyjnym `php.ini` twojego PHP masz włączone następujące rozszerzenia (niektóre mogą być już domyślnie aktywne, inne należy odkomentować usuwając średnik `;`):

```ini
extension=curl
extension=intl
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
```

Bez tych rozszerzeń aplikacja może nie działać poprawnie (np. obsługa bazy danych, wysyłka maili, translacje, itp.).


## Technologie

- PHP 8.1+
- Symfony 6+
- PostgreSQL
- Docker
- Twig
- Doctrine ORM
- Mailer (SMTP)

