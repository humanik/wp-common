---
name: commit-staged
description: Write a commit message for staged changes and commit
---

# Commit Staged

Generate a commit message for the currently staged changes and create the commit.

## Steps

1. Run `git diff --cached` to review the staged changes.
2. Run `git log --oneline -10` to see recent commit message style.
3. Write a concise commit message that:
   - Summarizes the nature of the changes (e.g. add, update, fix, refactor).
   - Focuses on the "why" rather than the "what".
   - Follows the existing commit message style from the repository.
4. Create the commit using a HEREDOC:
   ```bash
   git commit -m "$(cat <<'EOF'
   Commit message here.

   Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
   EOF
   )"
   ```
5. Run `git status` to verify the commit succeeded.
6. Report the commit to the user.
