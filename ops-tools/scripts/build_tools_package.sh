#!/usr/bin/env bash
# =============================================================================
# build_tools_package.sh - Create downloadable tools package
# =============================================================================
# Creates wds-tools.zip containing all operational scripts for deployment.
# Run this script after making changes to any tools.
#
# Usage:
#   ./build_tools_package.sh
#
# Output:
#   ops-tools/wds-tools.zip - Ready for download from core.iaregamer.com
# =============================================================================

set -euo pipefail

# Get the script's directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OPS_TOOLS_DIR="$(dirname "$SCRIPT_DIR")"
OUTPUT_ZIP="${OPS_TOOLS_DIR}/wds-tools.zip"

echo "Building WDS Tools Package..."
echo "Source: ${OPS_TOOLS_DIR}"
echo "Output: ${OUTPUT_ZIP}"

# Remove old zip if exists
if [ -f "${OUTPUT_ZIP}" ]; then
    rm "${OUTPUT_ZIP}"
    echo "Removed old package"
fi

# Create the zip file
cd "${OPS_TOOLS_DIR}"
zip -r "${OUTPUT_ZIP}" \
    scripts/*.sh \
    tools/INSTRUCTIONS.txt \
    tools/servers.txt \
    www/status/*.php \
    Bootstrap-GameServerHost.ps1 \
    -x "*.git*" \
    -x "*__pycache__*"

echo ""
echo "Package created successfully!"
echo ""
echo "Contents:"
unzip -l "${OUTPUT_ZIP}"
echo ""
echo "Deploy with:"
echo "  wget -O /tmp/wds-tools.zip https://core.iaregamer.com/ops-tools/wds-tools.zip"
echo "  unzip -o /tmp/wds-tools.zip -d /home/gameserver/tools/"
