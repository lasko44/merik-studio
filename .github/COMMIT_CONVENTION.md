# Commit Convention

This project uses [Conventional Commits](https://www.conventionalcommits.org/) for automatic semantic versioning.

## Commit Message Format

```
<type>(<scope>): <description>

[optional body]

[optional footer(s)]
```

## Types

| Type | Description | Version Bump |
|------|-------------|--------------|
| `feat` | A new feature | Minor (0.X.0) |
| `fix` | A bug fix | Patch (0.0.X) |
| `perf` | Performance improvement | Patch (0.0.X) |
| `docs` | Documentation only | No release |
| `style` | Code style (formatting, etc) | No release |
| `refactor` | Code refactoring | No release |
| `test` | Adding/updating tests | No release |
| `chore` | Maintenance tasks | No release |
| `ci` | CI/CD changes | No release |
| `build` | Build system changes | No release |

## Breaking Changes

For breaking changes that require a **major** version bump, add `!` after the type:

```
feat!: remove deprecated API endpoints
```

Or include `BREAKING CHANGE:` in the footer:

```
feat: restructure user authentication

BREAKING CHANGE: JWT tokens now expire after 1 hour instead of 24 hours
```

## Examples

### Feature (Minor Release)
```
feat: add user invitation system

Allow admins to invite users via email with pre-assigned roles.
```

### Bug Fix (Patch Release)
```
fix(auth): prevent session timeout on remember me
```

### Breaking Change (Major Release)
```
feat!: change API response format

BREAKING CHANGE: All API endpoints now return data wrapped in a `data` key.
```

### No Release
```
docs: update README with installation instructions

chore: update dependencies

test: add unit tests for InvitationService
```

## Scopes (Optional)

Use scopes to specify what part of the codebase changed:

- `auth` - Authentication
- `users` - User management
- `pages` - Page builder
- `blog` - Blog system
- `products` - Products/shop
- `api` - API endpoints
- `ui` - User interface
- `db` - Database/migrations

## Tips

1. Keep the first line under 72 characters
2. Use imperative mood ("add" not "added")
3. Don't capitalize the first letter
4. No period at the end of the subject line
5. Separate subject from body with a blank line
