# Episodes Management System 

This system manages episodes and the parts within them. It provides functionality for creating, updating, deleting, and reindexing parts within episodes, with enhanced data integrity through the use of pessimistic locking. The system ensures that episodes and parts are locked during modifications to prevent concurrent updates or race conditions.

Below is the structure and functionality provided by the system:

## Episode Management: 

- Verifies the existence of episodes before any operation involving parts.

## Part Management: 

- Handles the creation, updating, deletion, and reindexing of parts within episodes. The system uses pessimistic locking to ensure that parts are not modified by other transactions during these operations.

## Cache Updates: 

- Episode caches are updated automatically after any change in parts.

## Pessimistic Locking: 

- Ensures that rows are locked when creating, updating, deleting, or reindexing parts to prevent conflicts and guarantee data consistency.


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

## API Endpoints

### 1. Update Episode Position

**Endpoint**: `PATCH  /episodes/parts`

**Payload**:
```json
{
    'part_id' : 1
    'episode_id' :2,
    'position' : 1,
    'new_position' : 3,
}
```

## 2. Add New Part to Episode

**Endpoint**: `POST /episodes/parts`

### Payload

```json
{
    'episode_id' :2,
    'position' : 1,
}
```

## 3. Delete Part from Episode

**Endpoint**: `DELETE /episodes/parts`

### Payload

```json
{
    'part_id' : 1
    'episode_id' :2,
    'position' : 1,
}
```

## 4. Fetch Episode Parts

**Endpoint**: `GET /episode/{id}/parts`

### 5. duplicate episode

**Endpoint**: `POST  /episodes/{episodeId}`
