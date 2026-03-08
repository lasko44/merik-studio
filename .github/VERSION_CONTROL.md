# Version Control & Release Guide

This document describes the branching strategy, commit conventions, and release process for Merik CMS.

## Table of Contents

- [Branching Strategy](#branching-strategy)
- [Commit Conventions](#commit-conventions)
- [Versioning](#versioning)
- [Release Process](#release-process)
- [Workflows](#workflows)
- [Quick Reference](#quick-reference)

---

## Branching Strategy

We follow a simplified Git Flow model with automatic cascade merges.

```
main (production)
  ↑
  │ PR (requires tests + review)
  │
develop (integration)
  ↑
  │ PR
  │
feature/* | fix/* | chore/* (work branches)
```

### Branch Types

| Branch | Purpose | Protected | Deploys To |
|--------|---------|-----------|------------|
| `main` | Production-ready code | Yes | Production |
| `develop` | Integration branch | Yes | Staging |
| `feature/*` | New features | No | - |
| `fix/*` | Bug fixes | No | - |
| `hotfix/*` | Urgent production fixes | No | - |
| `chore/*` | Maintenance tasks | No | - |

### Branch Rules

**main**
- All commits must come through PRs
- Requires passing tests (80% coverage on new code)
- Requires code review approval
- Triggers automatic release on merge
- Cascades changes to `develop` automatically

**develop**
- All commits must come through PRs
- Requires passing tests
- Integration testing branch

### Cascade Merges

When changes are pushed to `main`, they automatically cascade down to `develop`:

```
main ──push──> cascade-merge workflow ──merge──> develop
```

If conflicts occur, a PR is created for manual resolution.

---

## Commit Conventions

We use [Conventional Commits](https://www.conventionalcommits.org/) for clear history and automatic versioning.

### Format

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types

| Type | Description | Version Bump |
|------|-------------|--------------|
| `feat` | New feature | Minor (1.x.0) |
| `fix` | Bug fix | Patch (1.0.x) |
| `perf` | Performance improvement | Patch |
| `docs` | Documentation only | None |
| `style` | Code style (formatting) | None |
| `refactor` | Code refactoring | None |
| `test` | Adding tests | None |
| `chore` | Maintenance tasks | None |
| `ci` | CI/CD changes | None |
| `build` | Build system changes | None |
| `revert` | Revert previous commit | Depends |

### Breaking Changes

For breaking changes, add `!` after the type or include `BREAKING CHANGE:` in the footer:

```bash
# Option 1: Using !
feat!: remove deprecated API endpoint

# Option 2: Using footer
feat: change authentication flow

BREAKING CHANGE: JWT tokens now expire after 1 hour instead of 24 hours
```

Breaking changes trigger a **major** version bump (x.0.0).

### Examples

```bash
# Feature
git commit -m "feat(auth): add user invitation system"

# Bug fix
git commit -m "fix(pages): resolve duplicate slug generation"

# With scope
git commit -m "feat(blog): add category filtering"

# Breaking change
git commit -m "feat(api)!: change response format for page endpoints"

# Multiple lines
git commit -m "fix(checkout): handle failed payments correctly

Previously, failed payments would leave the cart in an invalid state.
This fix ensures the cart is restored on payment failure.

Closes #123"
```

---

## Versioning

We follow [Semantic Versioning](https://semver.org/) (SemVer).

### Format

```
MAJOR.MINOR.PATCH[-PRERELEASE]

Examples:
1.0.0        # Stable release
1.2.3        # Stable with features and fixes
2.0.0-beta.1 # Beta prerelease
2.0.0-rc.1   # Release candidate
```

### Version Bumps

| Change Type | Example | Trigger |
|-------------|---------|---------|
| **Major** | 1.0.0 → 2.0.0 | Breaking changes (`feat!:`, `BREAKING CHANGE`) |
| **Minor** | 1.0.0 → 1.1.0 | New features (`feat:`) |
| **Patch** | 1.0.0 → 1.0.1 | Bug fixes (`fix:`, `perf:`) |

### Version File

The current version is stored in `version.json`:

```json
{
    "version": "1.0.0",
    "release_date": "2026-03-08",
    "minimum_php": "8.2",
    "minimum_laravel": "12.0"
}
```

This file is automatically updated by the release workflow.

---

## Release Process

### Automatic Releases (Recommended)

Releases are created automatically when commits are pushed to `main`:

1. **Create feature branch**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/my-feature
   ```

2. **Make changes and commit**
   ```bash
   git add .
   git commit -m "feat(scope): add new feature"
   ```

3. **Push and create PR to develop**
   ```bash
   git push -u origin feature/my-feature
   gh pr create --base develop
   ```

4. **After PR is merged to develop, create PR to main**
   ```bash
   gh pr create --base main --head develop
   ```

5. **When PR is merged to main**:
   - Tests run automatically
   - Version is bumped based on commits
   - `version.json` is updated
   - Git tag is created
   - GitHub Release is published
   - Changes cascade to `develop`

### Manual Releases

For special releases (beta, rc, hotfix):

```bash
# Create and push tag
git tag -a v1.0.0-beta.1 -m "Beta release"
git push origin v1.0.0-beta.1

# Create GitHub release
gh release create v1.0.0-beta.1 \
  --title "v1.0.0-beta.1" \
  --prerelease \
  --notes "Beta release notes here"
```

### Hotfix Process

For urgent production fixes:

```bash
# 1. Create hotfix branch from main
git checkout main
git pull origin main
git checkout -b hotfix/critical-bug

# 2. Make fix and commit
git commit -m "fix(auth): resolve critical login issue"

# 3. Create PR directly to main
gh pr create --base main

# 4. After merge, changes auto-cascade to develop
```

---

## Workflows

### GitHub Actions

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| `tests.yml` | Push/PR to main, develop | Run test suite with coverage |
| `release.yml` | Push to main | Create automatic releases |
| `commitlint.yml` | PR to main, develop | Validate commit messages |
| `cascade-merge.yml` | Push to main | Sync main → develop |

### Test Requirements

- **Backend**: PHPUnit/Pest tests must pass
- **Frontend**: Vitest tests must pass
- **Coverage**: 80% minimum on new code

### Release Workflow Flow

```
Push to main
     │
     ▼
┌─────────────┐
│ Run Tests   │
└─────────────┘
     │ pass
     ▼
┌─────────────────────┐
│ Analyze Commits     │
│ (feat, fix, etc.)   │
└─────────────────────┘
     │
     ▼
┌─────────────────────┐
│ Calculate Version   │
│ (major/minor/patch) │
└─────────────────────┘
     │
     ▼
┌─────────────────────┐
│ Update version.json │
└─────────────────────┘
     │
     ▼
┌─────────────────────┐
│ Create Git Tag      │
└─────────────────────┘
     │
     ▼
┌─────────────────────┐
│ Publish Release     │
│ (with changelog)    │
└─────────────────────┘
     │
     ▼
┌─────────────────────┐
│ Cascade to develop  │
└─────────────────────┘
```

---

## Quick Reference

### Daily Development

```bash
# Start new feature
git checkout develop && git pull
git checkout -b feature/my-feature

# Commit changes
git commit -m "feat(scope): description"

# Push and create PR
git push -u origin feature/my-feature
gh pr create --base develop
```

### Commit Message Cheatsheet

```bash
feat(auth): add password reset       # New feature → minor bump
fix(ui): correct button alignment    # Bug fix → patch bump
feat!: redesign API                  # Breaking → major bump
docs: update README                  # Docs → no release
chore: update dependencies           # Maintenance → no release
```

### Checking Version

```bash
# Current version
cat version.json | jq .version

# Latest release
gh release list --limit 1

# All tags
git tag --list 'v*' --sort=-version:refname
```

### Creating Prerelease

```bash
# Beta
git tag -a v2.0.0-beta.1 -m "Beta 1"
git push origin v2.0.0-beta.1
gh release create v2.0.0-beta.1 --prerelease

# Release candidate
git tag -a v2.0.0-rc.1 -m "Release candidate 1"
git push origin v2.0.0-rc.1
gh release create v2.0.0-rc.1 --prerelease
```

---

## Related Documentation

- [Commit Convention Details](.github/COMMIT_CONVENTION.md)
- [Pull Request Template](.github/PULL_REQUEST_TEMPLATE.md)
- [Conventional Commits Spec](https://www.conventionalcommits.org/)
- [Semantic Versioning Spec](https://semver.org/)
