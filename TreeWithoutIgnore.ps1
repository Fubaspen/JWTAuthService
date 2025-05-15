param (
    [string]$Path = ".",
    [string]$Indent = ""
)

function Show-Tree {
    param (
        [string]$CurrentPath,
        [string]$Indent
    )

    # Отбираем только видимые папки и файлы, игнорируем ненужное
    $items = Get-ChildItem -Path $CurrentPath -Force | Where-Object {
        $_.Name -notin @("vendor", "Tests", "var", ".idea", ".git")
    }

    $count = $items.Count
    for ($i = 0; $i -lt $count; $i++) {
        $item = $items[$i]
        $isLast = ($i -eq ($count - 1))

        if ($isLast) {
            $connector = "\-- "
            $newIndent = "$Indent    "
        } else {
            $connector = "|-- "
            $newIndent = "$Indent|   "
        }

        Write-Host "$Indent$connector$item"

        # Рекурсивно обрабатываем только директории
        if ($item.PSIsContainer) {
            Show-Tree -CurrentPath $item.FullName -Indent $newIndent
        }
    }
}

Show-Tree -CurrentPath (Resolve-Path $Path) -Indent ""
