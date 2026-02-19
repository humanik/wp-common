# Commit

Create a commit with a meaningful message.

## Steps

1. Run `composer phpcbf` to auto-fix coding standard issues.
2. Run `composer phpcs`, `composer phpstan`, and `composer test`. Collect all errors from all three commands. If there are any errors or failures, report all of them to the user and do not proceed with the commit.
5. Run `git diff --staged` and `git diff` to see all changes (staged and unstaged).
6. Run `git status` to see untracked and modified files.
7. Stage all relevant changed files using `git add` with specific file paths (do NOT use `git add -A` or `git add .`). Do not stage files that contain secrets or credentials.
8. Analyze the diff and write a concise commit message that describes **what** changed and **why**. Use imperative mood (e.g. "Add ...", "Fix ...", "Update ..."). Keep the subject line under 72 characters. Do not add 'Co-Authored-By: ...'
9. Create the commit.
10. Report the commit message to the user.
