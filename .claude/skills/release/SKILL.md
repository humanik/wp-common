---
name: release
description: Tag with the next version and push to origin
---

# Release

Tag with the next version and push to origin.

## Arguments

- `type` (required): The release type — `major`, `minor`, or `patch`.
  - If the user does not provide this argument, ask them to choose before proceeding.

## Steps

1. Determine the next version tag:
   - Run `git tag --sort=-v:refname | head -1` to get the latest tag.
   - Based on the `type` argument, increment the appropriate segment:
     - `major`: `v1.2.3` -> `v2.0.0`
     - `minor`: `v1.2.3` -> `v1.3.0`
     - `patch`: `v1.2.3` -> `v1.2.4`
2. Create the new tag: `git tag <new_version>`.
3. Push the commit and tag to origin: `git push origin && git push origin <new_version>`.
4. Report the new tag to the user.
