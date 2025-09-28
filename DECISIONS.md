# Technical Decisions

**Note:** I chose to use Anthropic's Claude AI models to assist with implementation and architectural decisions throughout this project.

## Architecture

**Laravel API + Vue SPA**
- Clean separation between backend and frontend
- API-first approach for potential mobile apps
- Modern development workflow

**Queue-based processing**
- Redis for job queues
- Prevents timeouts on large files
- Real-time progress tracking
- Job retry capability

## Database Design

Simple structure with two tables:
- `contacts` - stores contact data with unique email constraint
- `import_summaries` - tracks import statistics and status

## CSV Processing

**Automatic separator detection**
- Supports comma, semicolon, and tab separators
- Detects best separator by column count
- Handles different CSV formats automatically

**Column mapping**
- Flexible column name detection
- Maps variations like "name"/"full_name"/"contact_name"
- Case-insensitive matching

**Data validation**
- Email format validation
- Multiple date format support
- Duplicate prevention by email
- Graceful error handling per row

## Frontend Architecture

**Component-based Vue 3**
- No state management library (simple enough without Vuex)
- Parent-child communication via props/events
- Centralized API service

**Progress tracking**
- Polling every 2 seconds (simpler than WebSockets)
- Real-time updates without persistent connections

## Performance

**Memory efficient processing**
- Stream-based CSV reading
- Batch progress updates every 100 rows
- Automatic file cleanup
- Database indexing on email column

**Frontend optimization**
- Tailwind for small CSS bundle
- Component-based architecture
- Pagination for large contact lists

## Development Choices

**Docker with Laravel Sail**
- Consistent development environment
- Includes all services (MySQL, Redis)
- One-command setup

**Error handling**
- Multiple validation layers
- Detailed logging
- User-friendly error messages
- Graceful degradation

## Alternatives Considered

- WebSockets for progress (rejected - polling simpler)
- NoSQL database (rejected - relational data fits SQL)
- Server-side rendering (rejected - SPA better for this use case)
- GraphQL (rejected - REST sufficient for CRUD)

## Future Improvements

- Multiple file format support (Excel, JSON)
- Advanced duplicate detection algorithms
- Bulk contact operations
- Export functionality
- API rate limiting