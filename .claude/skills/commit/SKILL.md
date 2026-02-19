# Commit

Create a commit with a meaningful message.

## Steps

1. Run `git diff --staged` and `git diff` to see all changes (staged and unstaged).
2. Run `git status` to see untracked and modified files.
3. Stage all relevant changed files using `git add` with specific file paths (do NOT use `git add -A` or `git add .`). Do not stage files that contain secrets or credentials.
4. Analyze the diff and write a concise commit message that describes **what** changed and **why**. Use imperative mood (e.g. "Add ...", "Fix ...", "Update ..."). Keep the subject line under 72 characters. Do not add 'Co-Authored-By: ...'
5. Create the commit.
6. Report the commit message to the user.
