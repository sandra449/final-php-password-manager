# UML Class Diagram

```mermaid
classDiagram
    class Database {
        -host: string
        -db_name: string
        -username: string
        -password: string
        +conn: PDO
        +getConnection(): PDO
    }

    class User {
        -conn: PDO
        -table_name: string
        +id: int
        +username: string
        -password: string
        -encryption_key: string
        +__construct(db: PDO)
        +create(username: string, password: string): bool
        +login(username: string, password: string): bool
        +getEncryptionKey(): string
        -generateEncryptionKey(password: string): string
        -encryptKey(key: string, password: string): string
        -decryptKey(encrypted_key: string, password: string): string
    }

    class PasswordGenerator {
        -lowercase: string
        -uppercase: string
        -numbers: string
        -special: string
        +generate(length: int, lowercase_count: int, uppercase_count: int, numbers_count: int, special_count: int): string
        +generateByPercentage(length: int, lowercase_percent: int, uppercase_percent: int, numbers_percent: int, special_percent: int): string
        -getRandomChars(charset: string, count: int): array
    }

    class PasswordStorage {
        -conn: PDO
        -table_name: string
        -user: User
        +__construct(db: PDO, user: User)
        +store(website_name: string, password: string): bool
        +getAll(): array
        -encrypt(password: string): string
        -decrypt(encrypted_data: string): string
    }

    Database <-- User : uses
    Database <-- PasswordStorage : uses
    User <-- PasswordStorage : uses
```

## Class Relationships

1. **Database Class**
   - Core database connection management
   - Used by both User and PasswordStorage classes

2. **User Class**
   - Handles user authentication and encryption key management
   - Depends on Database class for data persistence
   - Used by PasswordStorage for encryption operations

3. **PasswordGenerator Class**
   - Independent utility class for password generation
   - No dependencies on other classes

4. **PasswordStorage Class**
   - Manages password storage and retrieval
   - Depends on Database class for data persistence
   - Depends on User class for encryption key access 