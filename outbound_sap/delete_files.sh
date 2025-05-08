#!/bin/bash

# Directories to process
TARGET_DIRS=(
    "/var/www/html/ess/outbound_sap/MasterData_BKP"
    "/var/www/html/ess/outbound_sap/AbsenceQuota"
    "/var/www/html/ess/outbound_sap/MasterSTO"
    "/var/www/html/ess/outbound_sap/Approver"
)

# List of files to exclude from deletion (custom exceptions).
# Note: These are filenames or relative paths within each TARGET_DIR.
EXCLUDE_FILES=("index.php")

# Loop through each target directory
for TARGET_DIR in "${TARGET_DIRS[@]}"; do
    # Check if the directory exists
    if [ ! -d "$TARGET_DIR" ]; then
        echo "Warning: Directory $TARGET_DIR does not exist. Skipping..."
        continue
    fi

    echo "Processing directory: $TARGET_DIR"

    # Generate the `find` command's `-not` clauses for each file to exclude
    EXCLUDE_CLAUSES=()
    for file in "${EXCLUDE_FILES[@]}"; do
        EXCLUDE_CLAUSES+=( -not -path "$TARGET_DIR/$file" )
    done

    # Find and delete files older than 3 months, excluding specified files
    sudo find "$TARGET_DIR" -type f -mtime +90 "${EXCLUDE_CLAUSES[@]}" -exec sudo rm -f {} +

    echo "Completed processing for $TARGET_DIR."
done

echo "Deletion process completed for all specified directories."

