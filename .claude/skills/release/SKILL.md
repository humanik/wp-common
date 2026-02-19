# Release

Tag with the next patch version and push to origin.

## Steps

1. Determine the next patch version tag:
   - Run `git tag --sort=-v:refname | head -1` to get the latest tag.
   - Increment the patch number (e.g. `v1.0.7` -> `v1.0.8`).
2. Create the new tag: `git tag <new_version>`.
3. Push the commit and tag to origin: `git push origin && git push origin <new_version>`.
4. Report the new tag to the user.
