<!-- Developed by World Domination Software LLC -->
---
title: "External Packages"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Overview

Mystical Islands supplements core Atavism with a curated set of third-party packages. This page tracks what we include, the license state, and integration notes.

| Package | Purpose | License | Notes |
| --- | --- | --- | --- |
| **UMA 2** | Avatar customization | MIT | Integrated for player characters and armor displays |
| **Odin Inspector** | Editor tooling | Paid (studio license) | Enables dynamic inspectors in custom Atavism windows |
| **Amplify Shader** | Stylized water/sky | Paid | Used for ocean rendering and magical effects |
| **Crest Ocean** | Advanced wave simulation | Paid | Enabled only in high-tier graphics mode |
| **DoTween Pro** | UI animation | Paid | Ship deck UI transitions, quest pop-ups |
| **Behavior Designer** | AI behavior trees | Paid | Controls complex NPC AI encounters |

## Import Workflow

1. Verify license status in the WDS asset ledger.
2. Download from the official source (Unity Asset Store, vendor portal, or internal stash).
3. Import into Unity per vendor instructions, usually via `.unitypackage`.
4. Run `Mystical Islands → Validate External Packages` to ensure editor scripts apply our settings.

## Version Control Strategy

- Store imported assets in Git LFS where possible to keep repo size manageable.
- For assets with restrictive licenses, keep them in the secure `assets/` bucket and use the `fetch_assets.sh` helper script to pull during build setup.
- Document applied patches under `Assets/MysticalIslands/Documentation/Patches/` for future upgrades.

## Atavism Integration Notes

- UMA meshes plug directly into the `Equipment Displays` defined in the Atavism Editor.
- Behavior Designer states map to Atavism mob types; ensure state names align with database entries (e.g., `MobType = "MI_IslandGuard"`).
- Shader updates may require tweaks to Atavism's lighting scripts; test on both Windows and Linux clients.

## Update Cadence

- Review and update external packages quarterly or whenever Atavism releases a major update.
- Run automated regression tests in the QA pipeline to catch asset conflicts.
- Announce significant upgrades in the weekly production sync meeting.

## Compliance

- Maintain proof-of-purchase for all paid assets in the finance SharePoint.
- Respect redistribution limitations—client builds bundle runtime assets, but editor-only tools remain internal.
- Remove unused packages promptly to avoid accidental license violations.


