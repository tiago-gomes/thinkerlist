# Episodes Management System (wip)

This system manages episodes, parts within episodes, and logs operations performed on parts. Below is the structure and functionality provided by the system.

## Database Structure

### Episodes Table
| Column      | Type       | Description            |
|-------------|------------|------------------------|
| id          | INT (PK)   | Unique episode ID.      |
| name        | VARCHAR    | Name of the episode.    |
| created_at  | TIMESTAMP  | Creation time.          |
| updated_at  | TIMESTAMP  | Last update time.       |

### Parts Table
| Column      | Type       | Description                             |
|-------------|------------|-----------------------------------------|
| id          | INT (PK)   | Unique part ID.                         |
| episode_id  | INT (FK)   | Foreign key linking to episodes.        |
| position    | INT        | Position of the part in the episode.    |
| created_at  | TIMESTAMP  | Creation time.                          |
| updated_at  | TIMESTAMP  | Last update time.                       |

### Operation Logs Table
Logs the operations (add, delete, update) performed on the parts.

| Column      | Type       | Description                                        |
|-------------|------------|----------------------------------------------------|
| id          | INT (PK)   | Unique log ID.                                     |
| operation   | VARCHAR    | Type of operation ('add', 'delete', 'update').     |
| episode_id  | INT (FK)   | Foreign key linking to episodes.                   |
| part_id     | INT (FK)   | Foreign key linking to parts.                      |
| position    | INT        | Current Position.                                  |
| timestamp   | TIMESTAMP  | Time the operation was performed.                  |
| status      | INT        | Status of the operation ('pending', 'completed').  |

## API Endpoints

### 1. Update Episode Position

**Endpoint**: `PATCH /episode`

**Payload**:
```json
{
  "episode": 1,
  "part": 2,
  "position": 3
}
```

## 2. Add New Part to Episode

**Endpoint**: `POST /episode/{id}/parts`

### Payload

```json
{
  "content": "New part content",
  "position": 0
}
```

## Actions

- Validate the input data.
- Insert the new part into the `parts` table.
- Add a log entry to the `operation_logs` table.
- Create a queue job to:
  - Process in batches of 500:
    - Update the Redis cache for parts (v1 and v2).
    - Reindex positions in MySQL.

## Tests

### Unit Tests
- Verify the part is added correctly in the database.
- Ensure the correct log entry is created in `operation_logs`.

### Integration Tests
- Verify that the Redis cache updates appropriately.
- Ensure positions are recalculated correctly in MySQL.

---

## 3. Delete Part from Episode

**Endpoint**: `DELETE /episode/{episodeId}/parts/{partId}`

### Actions
- Validate the part and episode IDs.
- Log the delete operation in the `operation_logs` table.
- Create a queue job to:
  - Process in batches of 500:
    - Update the Redis cache for parts (v1 and v2).
    - Reindex positions in MySQL.

### Tests

#### Unit Tests
- Confirm the specified part is deleted from the database.
- Ensure the correct log entry is created in `operation_logs`.

#### Integration Tests
- Verify the Redis cache is updated correctly.
- Validate that the positions of remaining parts are recalculated in MySQL.

---

## 4. Fetch Episode Parts

**Endpoint**: `GET /episode/{id}/parts`

### Actions
- Retrieve the list of parts from Redis cache, if available.
- If not present in the cache, fetch from the `parts` table and store the result in Redis for future requests.

### Tests

#### Unit Tests
- Ensure the endpoint returns the correct list of parts for a given episode.
- Validate that data is fetched correctly from either Redis or MySQL.

#### Integration Tests
- Verify the caching mechanism for performance and accuracy.
- Ensure subsequent requests are served from Redis.
- Confirm that the cache is updated correctly when new parts are added or deleted.
- Validate that the cache is cleared when the episode is updated or deleted.
- Test edge cases, such as an empty episode or a non-existent episode.

