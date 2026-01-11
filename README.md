
## Docker Setup

You can run this application using Docker and Docker Compose for a consistent development environment.

### Prerequisites
- Docker
- Docker Compose

### Running with Docker

1. **Clone the repository** (if you haven't already)
2. **Build and Start Containers:**
   ```bash
   docker-compose up -d --build
   ```
3. **Install Dependencies & Setup:**
   Run the following command to install Composer dependencies, generate the key, and run migrations inside the container:
   ```bash
   docker-compose exec app composer install
   docker-compose exec app cp .env.example .env
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   ```
   *Note: Ensure your `.env` file in the container points to the database host `db` instead of `127.0.0.1`.*

4. **Access the Application:**
   Open [http://localhost:8000](http://localhost:8000) in your browser.

5. **Stop Containers:**
   ```bash
   docker-compose down
   ```
