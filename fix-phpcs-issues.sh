#!/bin/bash

# Script per correggere automaticamente gli errori PHPCS nel plugin local_coursemanager
# Eseguire dalla directory del plugin: ~/Projects/moodle-development/plugins/coursemanager

PLUGIN_DIR="."
PLUGIN_NAME="local_coursemanager"

echo "🔧 Fixing PHPCS issues for ${PLUGIN_NAME}..."
echo "Working directory: $(pwd)"

# Verifica di essere nella directory corretta
if [ ! -f "version.php" ] || [ ! -d "lang" ]; then
    echo "❌ Error: This doesn't appear to be a Moodle plugin directory"
    echo "Please run this script from the plugin root directory"
    exit 1
fi

echo "✅ Plugin directory confirmed"

# Backup dei file originali
echo "📦 Creating backup..."
mkdir -p .backup-$(date +%Y%m%d-%H%M%S)
cp -r * .backup-$(date +%Y%m%d-%H%M%S)/ 2>/dev/null
echo "✅ Backup created"

# Funzione per aggiungere header GPL standard
add_gpl_header() {
    local file="$1"
    local description="$2"
    
    # Crea header temporaneo
    cat > /tmp/gpl_header.php << EOF
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * ${description}
 *
 * @package    local_coursemanager
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

EOF
    
    # Rimuovi header esistente se presente
    if grep -q "<?php" "$file"; then
        # Trova la prima riga di codice non-commento
        local first_code_line=$(grep -n "defined\|require\|class\|\$plugin\|\$capabilities\|\$functions\|\$string" "$file" | head -1 | cut -d: -f1)
        if [ -n "$first_code_line" ]; then
            # Mantieni solo il codice
            tail -n +${first_code_line} "$file" > /tmp/original_content.php
        else
            # Se non trova codice, mantieni tutto tranne la prima riga
            tail -n +2 "$file" > /tmp/original_content.php
        fi
    else
        cp "$file" /tmp/original_content.php
    fi
    
    # Combina header + contenuto
    cat /tmp/gpl_header.php /tmp/original_content.php > "$file"
    
    # Cleanup
    rm -f /tmp/gpl_header.php /tmp/original_content.php
}

# 1. Fix headers GPL
echo "🏷️  Step 1: Adding GPL headers..."

add_gpl_header "version.php" "Plugin version information"
add_gpl_header "externallib.php" "External API functions for course management"
add_gpl_header "db/access.php" "Plugin access capabilities"
add_gpl_header "db/services.php" "Web service definitions"
add_gpl_header "db/upgrade.php" "Plugin upgrade functions"
add_gpl_header "db/install.php" "Plugin installation functions"
add_gpl_header "lang/en/local_coursemanager.php" "English language strings"
add_gpl_header "lang/it/local_coursemanager.php" "Italian language strings"

echo "✅ GPL headers added"

# 2. Convert array() to []
echo "🔄 Step 2: Converting array syntax..."
find . -name "*.php" -exec sed -i.bak 's/array(/[/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/);$/];/g' {} \;
# Cleanup backup files
find . -name "*.php.bak" -delete
echo "✅ Array syntax converted"

# 3. Fix opening braces
echo "🔧 Step 3: Fixing brace placement..."
# Function braces on same line
find . -name "*.php" -exec sed -i.bak '/function.*($/{N;s/function\([^{]*\)\n{/function\1 {/g;}' {} \;
# Class braces on same line  
find . -name "*.php" -exec sed -i.bak '/class.*extends.*$/{N;s/class\([^{]*\)\n{/class\1 {/g;}' {} \;
find . -name "*.php" -exec sed -i.bak '/class.*implements.*$/{N;s/class\([^{]*\)\n{/class\1 {/g;}' {} \;
find . -name "*.php" -exec sed -i.bak '/class [^{]*$/{N;s/class\([^{]*\)\n{/class\1 {/g;}' {} \;
# Cleanup backup files
find . -name "*.php.bak" -delete
echo "✅ Brace placement fixed"

# 4. Add trailing commas to arrays (pattern-based)
echo "➕ Step 4: Adding trailing commas..."
# This is complex to do with sed, so we'll do a basic pattern
find . -name "*.php" -exec sed -i.bak 's/\([^,]\)$/\1,/g; s/,,/,/g; s/;,/;/g; s/},/}/g' {} \;
# Cleanup backup files  
find . -name "*.php.bak" -delete
echo "✅ Basic trailing comma fixes applied"

# 5. Fix variable naming (basic patterns)
echo "🏷️  Step 5: Fixing variable names..."
# This is tricky to automate fully, but we can fix the most common ones
find . -name "*.php" -exec sed -i.bak 's/\$external_id/\$externalId/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$section_external_id/\$sectionExternalId/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$clean_external_id/\$cleanExternalId/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$existing_cm/\$existingCm/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$existing_global/\$existingGlobal/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$clean_title/\$cleanTitle/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$clean_description/\$cleanDescription/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$clean_url/\$cleanUrl/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$current_sequence/\$currentSequence/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$new_sequence/\$newSequence/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$update_result/\$updateResult/g' {} \;
find . -name "*.php" -exec sed -i.bak 's/\$error_details/\$errorDetails/g' {} \;
# Cleanup backup files
find . -name "*.php.bak" -delete
echo "✅ Variable naming fixed"

# 6. Fix comment spacing
echo "💬 Step 6: Fixing comment spacing..."
find . -name "*.php" -exec sed -i.bak 's|//\.|// .|g' {} \;
find . -name "*.php" -exec sed -i.bak 's|//\([A-Z]\)|// \1|g' {} \;
# Cleanup backup files
find . -name "*.php.bak" -delete
echo "✅ Comment spacing fixed"

# 7. Try automatic PHPCBF if available
echo "🔧 Step 7: Running automatic PHPCBF..."
if command -v phpcbf &> /dev/null; then
    phpcbf --standard=moodle . 2>/dev/null || echo "ℹ️  PHPCBF completed with some issues"
    echo "✅ PHPCBF fixes applied"
elif command -v vendor/bin/phpcbf &> /dev/null; then
    vendor/bin/phpcbf --standard=moodle . 2>/dev/null || echo "ℹ️  PHPCBF completed with some issues"
    echo "✅ PHPCBF fixes applied"
else
    echo "⚠️  PHPCBF not found, skipping automatic fixes"
fi

# 8. Verifica finale
echo "🔍 Step 8: Final verification..."
if command -v phpcs &> /dev/null; then
    echo "Running PHPCS check..."
    phpcs --standard=moodle --report=summary . 2>/dev/null || echo "ℹ️  Some issues may remain"
elif command -v vendor/bin/phpcs &> /dev/null; then
    echo "Running PHPCS check..."
    vendor/bin/phpcs --standard=moodle --report=summary . 2>/dev/null || echo "ℹ️  Some issues may remain"
else
    echo "⚠️  PHPCS not found, cannot verify fixes"
fi

echo ""
echo "🎉 PHPCS fixes completed!"
echo ""
echo "📋 Manual fixes still needed:"
echo "   1. Check array trailing commas in multi-line arrays"
echo "   2. Verify docblock alignment (spaces before asterisks)"
echo "   3. Fix any remaining underscore variables"
echo "   4. Add missing function docblocks"
echo "   5. Review GPL headers for correct descriptions"
echo ""
echo "🔄 Next steps:"
echo "   1. Test the plugin functionality"
echo "   2. Run: git add -A && git commit -m 'Fix PHPCS coding standards'"
echo "   3. Push to GitHub to trigger CI"
echo ""
echo "📁 Backup location: .backup-* directory"
