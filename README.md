# Gender Classifier API

## Project Description

The Gender Classifier API is a simple RESTful web service built with Laravel 12. It takes a person's name as input and predicts their gender by making a request to the external [genderize.io](https://genderize.io/) API. 

In addition to returning the predicted gender and the probability (confidence score), the API determines whether the prediction is highly confident based on the probability (`>= 0.7`) and sample size (`>= 100`).

## Setup Instructions

To get the project up and running on your local machine, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <your-repository-url>
   cd gender-classifier
   ```

2. **Install dependencies:**
   Ensure you have PHP and Composer installed, then run:
   ```bash
   composer install
   ```

3. **Set up the environment file:**
   Copy the `.env.example` file to create your local environment setting file.
   ```bash
   cp .env.example .env
   ```
   
4. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

5. **Start the local development server:**
   ```bash
   php artisan serve
   ```
   Your application will now be running at `http://127.0.0.1:8000`.

*(Optional)* Since this API only has one endpoint and no database is required by default, you do not need to run migrations.

---

## API Endpoints

### Classify Gender

Predicts the gender based on a given name string.

- **URL:** `/api/classify`
- **Method:** `GET`
- **Query Parameters:**
  - `name` (required, string): The single name you want to classify. Formats with numbers or non-string inputs will get an error response.

#### Success Response (200 OK)

**Example Request:**
```
GET /api/classify?name=luc
```

**Example Response:**
```json
{
    "status": "success",
    "data": {
        "name": "luc",
        "gender": "male",
        "probability": 0.99,
        "sample_size": 25000,
        "is_confident": true,
        "processed_at": "2026-04-12T23:55:00+00:00"
    }
}
```

#### Error Handling

The API returns appropriate HTTP status codes based on validation or external API errors:

- **400 Bad Request**: If the `name` parameter is missing or empty.
- **422 Unprocessable Entity**: If the `name` provided is invalid (e.g., numeric).
- **502 Bad Gateway**: If there's an error reaching the external genderize.io service.
- **500 Internal Server Error**: If any other unexpected error occurs.
