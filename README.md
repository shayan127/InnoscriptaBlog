# Laravel News Aggregator API

## Project Overview
This is a backend project designed to fetch news data from multiple sources and provide it as an API.

## Installation Steps
1. Change Directory:
   ```sh
   cd backend
   ```
2. Run Docker Compose:
   ```sh
   docker-compose up -d --build
   ```
3. Wait for 10 minutes to allow data to be fetched.
4. Access the application via the local port (Please wait a few minutes to access the content):
   ```
   http://localhost:8181/api/documentation
   ```

## Technologies Used

### **Swagger**
Swagger is used for API documentation, making it easier to test and understand API endpoints.
![Swagger UI](./Swagger.png)

### **Controllers**
- **UserController**: Handles user authentication, token management, and user preferences.
- **BlogController**: Manages blog search, creation, update, and deletion.
- **MetadataController**: Handles metadata retrieval, category modification, and deletion.

### **Blog Search Algorithm**
In the blog search method:
- User preferences such as author, category, and source are checked and applied to the final results if available.
- Users can search for text in titles or filter results by date, category, and source.
- Search results also incorporate user preferences.
- Newer articles have higher priority in search results.
- Articles from the past week have even higher priority.

### **JWT Authentication**
Stateless authentication using token for secure authentication.

To obtain a JWT token, send a `POST` request to the authentication endpoint with the following credentials:
```
POST /api/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "123456"
}
```

### **Elasticsearch**
A powerful search engine used for:
- Handling large volumes of news articles.
- Providing fast and efficient search functionality.
- Filtering results based on user preferences.

Elasticsearch is accessible at:
```
http://localhost:9200
```

### **Redis**
Used for:
- Managing Laravel job queues efficiently.

### **Nginx**
Used as a reverse proxy to serve the Laravel application efficiently.

### **MySQL**
Used for storing structured data such as users, news articles, and metadata.

## Design Patterns and Principles

### **Strategy Pattern**
Used for implementing different strategies to fetch data from multiple news sources.
- Example files:
    - `backend/app/Services/News/AbstractNewsStrategy.php`
    - `backend/app/Services/News/NYT/NYTStrategy.php`
    - `backend/app/Services/News/NewsAPI/NewsAPIStrategy.php`
    - `backend/app/Services/News/Guardian/GuardianStrategy.php`

### **Adapter Pattern**
Used to convert different API responses into a standardized internal format.
- Example files:
    - `backend/app/Services/News/NYT/NYTAdapter.php`
    - `backend/app/Services/News/NewsAPI/NewsAPIAdapter.php`
    - `backend/app/Services/News/Guardian/GuardianAdapter.php`

### **Factory Pattern**
Used for creating instances of different news fetchers dynamically.
- Example files:
    - `backend/app/Services/News/NewsFactory.php`

## Future Improvements
- Implementing an S3 or MinIO storage solution for storing news images.
- Creating smaller job tasks for fetching data incrementally.
- Writing test cases.
- Separating authors and sources into a dedicated pivot table.
- Adding more news sources.
- Implementing retry logic for failed API requests.
- Using Horizon for better queue management.
- Using Sentry for logging and error tracking.

---
This README provides a high-level overview of the project.

Shayan 2025-02-26