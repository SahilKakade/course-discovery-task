# Oxford Course Discovery Task

A premium course discovery system built for Oxford International.

## Quick Setup
1. **Theme**: Clone this repo into your `wp-content/themes/` folder.
2. **ACF**: Install the Advanced Custom Fields plugin.
3. **Import**: Go to **ACF > Tools** and import the `acf-export.json` file included in this repo.
4. **Permalinks**: Ensure your Permalinks are set to "Post name" in WordPress Settings.

## Technical Implementation
- **Custom Post Types**: Courses, Instructors, and Providers.
- **WP_Query Logic**: Implements `meta_query` with `LIKE` comparisons for relational ACF Post Objects.
- **Frontend**: Custom PHP template (`page-35.php`) using a mobile-responsive CSS Grid.
- **Accessibility**: ARIA labels and semantic HTML for screen readers.
