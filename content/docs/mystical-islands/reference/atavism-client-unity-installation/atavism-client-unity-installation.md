<!-- Developed by Core Loop Development LLC -->
---
title: "Atavism Client Unity Installation"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public Core Loop site).**
> GSP is a heavily customized fork of OGP maintained by Core Loop.

## Supported Platforms

- **Operating Systems:** Windows 11, macOS 13, Ubuntu 22.04 (via Unity Hub)
- **Unity Version:** 2021.3.32f1 LTS
- **Atavism Package:** `AtavismUnity_10.7_wds.unitypackage`

## Installation Steps

1. **Install Unity Hub**
	- Download from `https://unity.com/download`. Sign in with the Core Loop Unity ID (see `ops-tools/credentials/vault`) if you need seat access.

2. **Add the LTS Editor**
	- In Unity Hub, click **Installs → Add**, choose `2021.3.32f1`, and enable the **Android Build Support** checkbox only if you work on mobile builds. WebGL is required for lightweight previews.

3. **Retrieve the Atavism Package**
	- Download `AtavismUnity_10.7_wds.unitypackage` from the secured SharePoint folder.
	- Verify checksum using `sha256sum`. The expected hash is published in `ops-tools/atavism/checksums.txt`.

4. **Import into Unity Project**
	- Open the Mystical Islands client project located in the private repo (`MysticalIslands/Client`).
	- In Unity, select **Assets → Import Package → Custom Package…** and point to the downloaded file.
	- Accept the default import list; deselect demo scenes to keep the project lean.

5. **Apply Mystical Islands Overrides**
	- After import, run the menu item `Mystical Islands → Apply Atavism Overrides`. This copies UI prefabs, shader settings, and scriptable objects tailored for Mystical Islands on top of the Atavism base.

## Post-Install Configuration

1. **Server Connection**
	- Edit `Assets/MysticalIslands/Config/ServerSettings.asset` to point to the current staging proxy hostname and port.
2. **Authentication**
	- Confirm the Atavism authentication keys under `Assets/AtavismUnity/Resources/Atavism/` match the environment (staging vs production).
3. **Asset Bundles**
	- Rebuild bundles via `Atavism Editor → Build Asset Bundles` after adding new models or UI assets.

## Validation Checklist

- [ ] No console errors after import.
- [ ] Login screen connects to staging realm.
- [ ] Starter island scene loads with Mystical Islands UI.
- [ ] Asset bundle generation succeeds.

## Maintenance

- When Atavism releases an update, compare the diff against `AtavismUnity_10.7_wds.unitypackage` and re-apply the Mystical Islands override script.
- Keep `Assets/MysticalIslands/Documentation/UnitySetup.md` synchronized with changes made here.
- Notify QA via `#mi-builds` before pushing large updates to source control.


