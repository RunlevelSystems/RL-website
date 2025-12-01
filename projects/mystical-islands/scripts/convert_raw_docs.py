#!/usr/bin/env python3
"""Convert Atavism HTML exports into Markdown references for Mystical Islands.

The converter walks the raw documentation folders and emits Markdown under
`content/docs/mystical-islands/reference/`, mirroring the relative structure
(and renaming `index.html` files to their parent slug).

We intentionally skip WordPress specific assets, JSON endpoints, and release
archives to keep the Markdown set focused on reference content.
"""
from __future__ import annotations

import argparse
import html
import re
from dataclasses import dataclass
from html.parser import HTMLParser
from pathlib import Path
from typing import Dict, List, Optional

RAW_ROOT_DEFAULT = Path(__file__).resolve().parents[1] / "raw" / "atavism_wiki" / "unity.wiki.atavismonline.com"
OUTPUT_ROOT_DEFAULT = Path(__file__).resolve().parents[3] / "content" / "docs" / "mystical-islands" / "reference"
SKIP_NAMES = {
    "wp-content",
    "wp-json",
    "releases",
    "project",
    "wp-admin",
    "wp-includes",
    "zh-hans",
}


@dataclass
class MarkdownResult:
    content: str
    first_heading: Optional[str]


class _HTMLToMarkdown(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.parts: List[str] = []
        self.list_stack: List[Dict[str, int]] = []
        self.inline_stack: List[str] = []
        self.link_stack: List[Dict[str, str]] = []
        self.in_pre: bool = False
        self.first_heading: Optional[str] = None
        self.collect_heading: bool = False
        self.heading_buffer: List[str] = []
        self.heading_depth: int = 0
        self.skip_depth: int = 0

    # --- helpers ---------------------------------------------------------
    def _append(self, text: str) -> None:
        if text:
            self.parts.append(text)

    def _start_block(self) -> None:
        if not self.parts or self.parts[-1].endswith("\n\n"):
            return
        if self.parts[-1].endswith("\n"):
            self.parts[-1] = self.parts[-1] + "\n"
        else:
            self.parts.append("\n\n")

    # --- HTMLParser overrides -------------------------------------------
    def handle_starttag(self, tag: str, attrs: List[tuple]) -> None:
        tag = tag.lower()
        attr_map = {key: value for key, value in attrs}

        if self.skip_depth > 0:
            self.skip_depth += 1
            return

        if tag in {"script", "style", "noscript", "svg", "head"}:
            self.skip_depth = 1
            return

        if tag in {"h1", "h2", "h3", "h4", "h5", "h6"}:
            level = int(tag[1])
            self._append("\n\n" + "#" * level + " ")
            self.inline_stack.append(tag)
            self.collect_heading = True
            self.heading_buffer = []
            self.heading_depth = 1
        elif tag == "p":
            self._append("\n\n")
            self.inline_stack.append(tag)
        elif tag == "br":
            self._append("\n")
        elif tag in {"ul", "ol"}:
            self.list_stack.append({"type": tag, "index": 0})
            self.inline_stack.append(tag)
        elif tag == "li":
            indent = "  " * max(len(self.list_stack) - 1, 0)
            prefix = "- "
            if self.list_stack:
                top = self.list_stack[-1]
                if top["type"] == "ol":
                    top["index"] += 1
                    prefix = f"{top['index']}. "
                else:
                    top["index"] += 1
            self._append("\n" + indent + prefix)
            self.inline_stack.append(tag)
        elif tag == "strong" or tag == "b":
            self._append("**")
            self.inline_stack.append(tag)
        elif tag == "em" or tag == "i":
            self._append("_")
            self.inline_stack.append(tag)
        elif tag == "code":
            if self.in_pre:
                return
            self._append("`")
            self.inline_stack.append(tag)
        elif tag == "pre":
            self._append("\n\n```\n")
            self.in_pre = True
            self.inline_stack.append(tag)
        elif tag == "blockquote":
            self._append("\n\n> ")
            self.inline_stack.append(tag)
        elif tag == "a":
            href = attr_map.get("href", "")
            self.link_stack.append({"href": href, "text": ""})
            self.inline_stack.append(tag)
        elif tag == "img":
            alt = attr_map.get("alt", "")
            src = attr_map.get("src", "")
            self._append(f"![{alt}]({src})")
        elif tag == "hr":
            self._append("\n\n---\n\n")
        else:
            self.inline_stack.append(tag)
        if tag not in {"br", "img", "hr"} and tag not in {"h1", "h2", "h3", "h4", "h5", "h6"}:
            if self.collect_heading:
                self.heading_depth += 1

    def handle_endtag(self, tag: str) -> None:
        tag = tag.lower()
        if self.skip_depth > 0:
            self.skip_depth -= 1
            return
        if tag in {"h1", "h2", "h3", "h4", "h5", "h6"}:
            self._append("\n")
            if self.collect_heading and self.first_heading is None:
                heading_text = "".join(self.heading_buffer).strip()
                if heading_text:
                    self.first_heading = heading_text
        elif tag == "p":
            self._append("\n")
        elif tag == "li":
            self._append("\n")
        elif tag == "pre":
            self._append("\n```\n")
            self.in_pre = False
        elif tag == "strong" or tag == "b":
            self._append("**")
        elif tag == "em" or tag == "i":
            self._append("_")
        elif tag == "code":
            if self.in_pre:
                return
            self._append("`")
        elif tag == "blockquote":
            self._append("\n")
        elif tag == "a":
            if self.link_stack:
                payload = self.link_stack.pop()
                text = payload["text"].strip() or payload["href"]
                href = payload["href"].strip()
                if href:
                    self._append(f"[{text}]({href})")
                else:
                    self._append(text)
        if self.inline_stack:
            self.inline_stack.pop()
        if self.collect_heading:
            self.heading_depth -= 1
            if self.heading_depth <= 0:
                self.collect_heading = False
                self.heading_depth = 0

    def handle_data(self, data: str) -> None:
        if not data:
            return
        if self.skip_depth > 0:
            return
        if self.in_pre:
            self._append(data)
            return
        text = html.unescape(data)
        if not text.strip():
            if self.link_stack:
                self.link_stack[-1]["text"] += " "
            else:
                self._append(" ")
            if self.collect_heading:
                self.heading_buffer.append(" ")
            return
        text = re.sub(r"\s+", " ", text)
        if self.link_stack:
            self.link_stack[-1]["text"] += text
        else:
            self._append(text)
        if self.collect_heading:
            self.heading_buffer.append(text)

    def handle_entityref(self, name: str) -> None:
        self.handle_data(html.unescape(f"&{name};"))

    def handle_charref(self, name: str) -> None:
        if name.startswith("x") or name.startswith("X"):
            value = chr(int(name[1:], 16))
        else:
            value = chr(int(name))
        self.handle_data(value)

    def get_markdown(self) -> MarkdownResult:
        # Collapse repeated blank lines and tidy whitespace.
        raw = "".join(self.parts)
        raw = raw.replace("\r", "")
        raw = re.sub(r"\n{3,}", "\n\n", raw)
        heading_match = re.search(r"(?:^|\n)(#{1,6}\s+)", raw)
        if heading_match:
            raw = raw[heading_match.start(1):]
        raw = raw.strip() + "\n"
        return MarkdownResult(content=raw, first_heading=self.first_heading)


def convert_html_to_markdown(html_text: str) -> MarkdownResult:
    parser = _HTMLToMarkdown()
    parser.feed(html_text)
    return parser.get_markdown()


def normalise_slug(path: Path) -> str:
    slug = path.stem
    if slug.lower() == "index" and path.parent != path.parent.parent:
        slug = path.parent.name
    return slug.replace(" ", "-").lower()


def humanise_title(path: Path) -> str:
    slug = normalise_slug(path)
    pretty = slug.replace("-", " ").replace("_", " ")
    return pretty.title()


def main(
    raw_root: Path,
    output_root: Path,
    dry_run: bool = False,
    include_api: bool = False,
) -> None:
    html_files: List[Path] = []
    for entry in raw_root.rglob("*.html"):
        if any(skip in entry.parts for skip in SKIP_NAMES):
            continue
        if not include_api and "docs" in entry.parts:
            # Skip the full JavaDoc export unless explicitly requested.
            continue
        html_files.append(entry)

    html_files.sort()

    for html_path in html_files:
        relative = html_path.relative_to(raw_root)
        if relative.name == "index.html" and relative.parent == Path():
            output_rel = Path("overview.md")
        else:
            parent = relative.parent
            if relative.name == "index.html":
                output_rel = parent / f"{parent.name}.md"
            else:
                output_rel = relative.with_suffix(".md")
        output_path = output_root / output_rel

        with html_path.open("r", encoding="utf-8", errors="ignore") as source:
            html_text = source.read()
        markdown = convert_html_to_markdown(html_text)

        title = markdown.first_heading or humanise_title(output_path)
        front_matter = (
            "---\n"
            f"title: \"{title}\"\n"
            "description: \"Atavism reference import\"\n"
            "weight: 100\n"
            "---\n\n"
            "> **Admin Documentation (not shown on public WDS site).**\n\n"
        )
        output_text = front_matter + markdown.content

        if dry_run:
            print(f"[dry-run] Would write {output_path.relative_to(output_root)}")
            continue

        output_path.parent.mkdir(parents=True, exist_ok=True)
        output_path.write_text(output_text, encoding="utf-8")
        print(f"Wrote {output_path}")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Convert Atavism HTML exports to Markdown")
    parser.add_argument("--raw", type=Path, default=RAW_ROOT_DEFAULT, help="Path to raw HTML export")
    parser.add_argument("--output", type=Path, default=OUTPUT_ROOT_DEFAULT, help="Destination root for Markdown")
    parser.add_argument("--dry-run", action="store_true", help="List files without writing")
    parser.add_argument(
        "--include-api",
        action="store_true",
        help="Include the large JavaDoc style API exports",
    )
    args = parser.parse_args()

    main(args.raw, args.output, dry_run=args.dry_run, include_api=args.include_api)
