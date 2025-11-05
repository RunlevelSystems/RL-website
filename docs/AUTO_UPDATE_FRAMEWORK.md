# Auto-Update Framework for Project Status

## Overview
This document outlines a potential framework for automatically updating project status information from GitHub repositories without manual intervention. This is **NOT YET IMPLEMENTED** but serves as a reference for future development.

## Proposed Architecture

### GitHub Integration
- Use GitHub API or GitHub Webhooks to monitor repository activity
- Track commits, pull requests, issues, and milestones
- Extract progress metrics from repository data

### Status Tracking Methods

#### Option 1: GitHub Projects API
- Utilize GitHub Projects (project boards) to track goals and progress
- Map project columns to progress percentages
- Track completed vs. total items

#### Option 2: Milestone-Based Tracking
- Define milestones in each repository
- Calculate progress based on open/closed issues in milestones
- Display percentage completion on website

#### Option 3: Custom Metadata Files
- Store a `.wds-status.json` file in each repository root
- Format:
```json
{
  "version": "1.0",
  "goals": [
    {
      "name": "Core Engine",
      "progress": 25,
      "target": 100,
      "description": "Game engine development"
    },
    {
      "name": "Galaxy Generation",
      "progress": 15,
      "target": 100,
      "description": "Procedural galaxy systems"
    }
  ],
  "milestones": [
    {
      "name": "Prototype Alpha",
      "date": "Q2 2026",
      "status": "in_progress"
    }
  ]
}
```

### Update Mechanism

#### Automated Sync
- **Webhook Approach**: GitHub webhooks trigger updates when repository changes
- **Scheduled Polling**: Cron job runs every X hours to fetch latest data
- **Manual Trigger**: Admin dashboard button to force refresh

#### Caching Strategy
- Cache repository data for 6-24 hours to reduce API calls
- Store in Redis or similar fast cache
- Fall back to database if cache unavailable

### Implementation Technologies

#### Backend Options
1. **Node.js + Octokit** (GitHub's official SDK)
2. **Python + PyGithub**
3. **PHP + GitHub API** (matches current stack)

#### Required Components
- GitHub Personal Access Token (with appropriate scopes)
- Database table for cached repository status
- Admin interface for configuration
- Rate limiting to stay within GitHub API limits (5000 requests/hour for authenticated)

### Security Considerations
- Store GitHub tokens securely (environment variables, not in code)
- Limit API token permissions to read-only
- Validate webhook signatures if using webhooks
- Implement rate limiting on update endpoint
- Only allow admin users to trigger manual updates

### Configuration Storage
Create a `project_sync_config` table:
```sql
CREATE TABLE project_sync_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_slug VARCHAR(100) UNIQUE,
    github_owner VARCHAR(100),
    github_repo VARCHAR(100),
    sync_enabled BOOLEAN DEFAULT true,
    last_sync TIMESTAMP,
    sync_interval INT DEFAULT 3600, -- seconds
    data_source ENUM('projects', 'milestones', 'custom_file') DEFAULT 'custom_file',
    custom_file_path VARCHAR(255) DEFAULT '.wds-status.json'
);
```

### Display Integration
- Update progress bars automatically from repository data
- Show last updated timestamp
- Display commit activity graphs
- Link directly to GitHub issues/projects

## Existing Frameworks

### Jenkins/CI Integration
- Some projects use Jenkins or CI/CD for builds
- Could extract build status and test coverage metrics
- Display on project pages

### GitHub Actions
- Track workflow runs and deployment status
- Show build/test success rates
- Display deployment frequency

## Future Enhancements
- Real-time updates via WebSocket when repository changes
- Community contribution metrics (PRs, issues, contributors)
- Automated changelog generation from commits
- Integration with project management tools (Jira, Trello)

## Manual Override
Always allow manual editing of project status for:
- Private repositories
- Projects in early planning stages
- Custom messaging and announcements

## Current Status: **NOT IMPLEMENTED**
This is a planning document only. All project progress is currently **manual** and should be updated by editing the project page files directly.

## Next Steps for Implementation
1. Choose data source method (recommend custom JSON files)
2. Set up GitHub API authentication
3. Create database schema for cached data
4. Build admin interface for configuration
5. Implement sync service/cron job
6. Update project page templates to read from database
7. Test with one project before rolling out to all

---
*Last Updated: 2025-11-05*
*Status: Planning/Documentation Only*
