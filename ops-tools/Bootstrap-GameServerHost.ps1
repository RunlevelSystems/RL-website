<# 
Bootstrap-GameServerHost.ps1
- Local payload first; if a file is missing it tries to download via downloads.json mapping.
- Writes detailed log + end-of-run summary (installed / not installed + reasons).

EXAMPLES
  powershell -ExecutionPolicy Bypass -File .\Bootstrap-GameServerHost.ps1 `
    -PayloadRoot \\filesvr\share\deps -InstallFileZilla

  powershell -ExecutionPolicy Bypass -File .\Bootstrap-GameServerHost.ps1 `
    -ZipSource \\filesvr\share\deps.zip -DownloadMapJson \\filesvr\share\downloads.json -InstallFileZilla
#>

param(
  [string]$PayloadRoot,
  [string]$ZipSource,
  [string]$DownloadMapJson,     # optional mapping file (see example above). If omitted, script looks for downloads.json in payload root.
  [switch]$InstallFileZilla,
  [int]$OpenSSHPort = 12322
)

$ErrorActionPreference = 'Stop'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

# ------------------ logging & workspace ------------------
$WorkRoot = 'C:\Temp\bootstrap'
$dlDir    = Join-Path $WorkRoot 'download'
$unpack   = Join-Path $WorkRoot 'payload'
$logDir   = 'C:\Logs'
$logFile  = Join-Path $logDir 'bootstrap.log'
$summaryCsv = Join-Path $logDir 'bootstrap-summary.csv'
$SuccessCodes = @(0,1638,3010)
$global:RebootFlag = $false
$InstallResults = New-Object System.Collections.Generic.List[object]

New-Item -ItemType Directory -Force -Path $dlDir,$unpack,$logDir | Out-Null
function Log($m){ "{0} {1}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $m | Tee-Object -FilePath $logFile -Append }

# ------------------ get payload ------------------
if ([string]::IsNullOrWhiteSpace($PayloadRoot)) {
  if ([string]::IsNullOrWhiteSpace($ZipSource)) { throw "Provide -PayloadRoot or -ZipSource." }
  $zipLocal = Join-Path $dlDir 'payload.zip'
  if ($ZipSource -match '^(https?)://') { Invoke-WebRequest -Uri $ZipSource -OutFile $zipLocal } else { Copy-Item $ZipSource $zipLocal -Force }
  if (Test-Path $unpack) { Remove-Item -Recurse -Force $unpack }
  Expand-Archive -LiteralPath $zipLocal -DestinationPath $unpack -Force
  $PayloadRoot = $unpack
}
Log "Using payload: $PayloadRoot"

# ------------------ load download map ------------------
if (-not $DownloadMapJson) {
  $cand = Join-Path $PayloadRoot 'downloads.json'
  if (Test-Path $cand) { $DownloadMapJson = $cand }
}
$DownloadMap = @{}
if ($DownloadMapJson -and (Test-Path $DownloadMapJson)) {
  try { $DownloadMap = Get-Content $DownloadMapJson -Raw | ConvertFrom-Json } catch { Log "WARN: failed to parse downloads.json ($DownloadMapJson): $($_.Exception.Message)" }
}

# ------------------ helpers ------------------
function Result([string]$Name,[string]$Id,[string]$Action,[string]$Source,[bool]$Installed,[int]$ExitCode,[string]$Notes){
  $InstallResults.Add([pscustomobject]@{
    Name=$Name; Id=$Id; Action=$Action; Source=$Source; Installed=$Installed; ExitCode=$ExitCode; Notes=$Notes
  })
}

function FindLocal([string]$relFolder,[string]$pattern){
  $p = Join-Path $PayloadRoot $relFolder
  if (!(Test-Path $p)) { return $null }
  Get-ChildItem -Path $p -File -Filter $pattern -ErrorAction SilentlyContinue | Select-Object -First 1
}

function TryDownload([string]$Id,[string]$TargetPath){
  $url = $DownloadMap.$Id
  if (-not $url) { return $false }
  try {
    Log "Downloading [$Id] from $url"
    Invoke-WebRequest -Uri $url -OutFile $TargetPath -UseBasicParsing
    return (Test-Path $TargetPath)
  } catch {
    Log "ERROR: download failed for [$Id]: $($_.Exception.Message)"
    return $false
  }
}

function RunInstaller([string]$Name,[string]$Id,[string]$exe,[string]$args,[string]$sourceNote){
  if (!(Test-Path $exe)) { Result $Name $Id 'install' $sourceNote $false (-1) "missing EXE"; throw "Missing $Name at $exe" }
  Log "Installing $Name => $exe $args"
  $p = Start-Process -FilePath $exe -ArgumentList $args -Wait -Passthru -NoNewWindow
  $code = $p.ExitCode
  if ($SuccessCodes -contains $code) {
    if ($code -eq 3010) { $global:RebootFlag = $true }
    Result $Name $Id 'install' $sourceNote $true $code "ok"
    Log "$Name: OK (exit $code)"
  } else {
    Result $Name $Id 'install' $sourceNote $false $code "installer exit $code"
    Log "$Name: FAILED (exit $code)"
  }
}

# ------------------ package list ------------------
# Each package defines: Id, Name, RelFolder, Pattern, Args, DetectScript(optional)
$Packages = @(
  @{ Id='vc2008_x64';   Name='VC++ 2008 x64';   Rel='vc2008\x64';       Pat='vcredist*.exe'; Args='/q /norestart' },
  @{ Id='vc2008_x86';   Name='VC++ 2008 x86';   Rel='vc2008\x86';       Pat='vcredist*.exe'; Args='/q /norestart' },

  @{ Id='vc2010_x64';   Name='VC++ 2010 x64';   Rel='vc2010\x64';       Pat='vcredist*.exe'; Args='/quiet /norestart' },
  @{ Id='vc2010_x86';   Name='VC++ 2010 x86';   Rel='vc2010\x86';       Pat='vcredist*.exe'; Args='/quiet /norestart' },

  @{ Id='vc2012_x64';   Name='VC++ 2012 x64';   Rel='vc2012\x64';       Pat='vcredist*.exe'; Args='/quiet /norestart' },
  @{ Id='vc2012_x86';   Name='VC++ 2012 x86';   Rel='vc2012\x86';       Pat='vcredist*.exe'; Args='/quiet /norestart' },

  @{ Id='vc2013_x64';   Name='VC++ 2013 x64';   Rel='vc2013\x64';       Pat='vcredist*.exe'; Args='/quiet /norestart' },
  @{ Id='vc2013_x86';   Name='VC++ 2013 x86';   Rel='vc2013\x86';       Pat='vcredist*.exe'; Args='/quiet /norestart' },

  @{ Id='vc15_22_x64';  Name='VC++ 2015–2022 x64'; Rel='vc2015-2022\x64'; Pat='vc_redist*.x64*.exe'; Args='/install /quiet /norestart' },
  @{ Id='vc15_22_x86';  Name='VC++ 2015–2022 x86'; Rel='vc2015-2022\x86'; Pat='vc_redist*.x86*.exe'; Args='/install /quiet /norestart' },

  @{ Id='dotnet47';     Name='.NET Framework 4.7'; Rel='dotnet';        Pat='NDP47-*.exe';   Args='/q /norestart';
     Detect = { 
       $r = (Get-ItemProperty -Path 'HKLM:\SOFTWARE\Microsoft\NET Framework Setup\NDP\v4\Full' -ErrorAction SilentlyContinue).Release
       if ($r -and [int]$r -ge 460798) { return $true } else { return $false }
     }
  },

  # DirectX (June 2010) — either provide the full redist EXE in downloads.json or supply the extracted folder with DXSETUP.exe.
  @{ Id='directx_june2010'; Name='DirectX (June 2010)'; Rel='directx'; Pat='DXSETUP.exe'; Args='/silent' }
)

# ------------------ install sequence ------------------
foreach ($pkg in $Packages) {
  $id   = $pkg.Id
  $name = $pkg.Name
  $rel  = $pkg.Rel
  $pat  = $pkg.Pat
  $args = $pkg.Args
  $detect = $pkg.Detect

  try {
    if ($detect) {
      $already = & $detect
      if ($already) {
        Log "$name: already present (skipping)"
        Result $name $id 'detect' 'system' $true 0 "already installed"
        continue
      }
    }

    $local = FindLocal $rel $pat
    if ($local) {
      RunInstaller $name $id $local.FullName $args "local"
      continue
    }

    # attempt download if mapping exists
    $targetName = if ($pat -eq 'DXSETUP.exe') { 'DXSETUP.exe' } else { "$id.exe" }
    $target = Join-Path $dlDir $targetName

    $downloaded = TryDownload $id $target
    if ($downloaded) {
      RunInstaller $name $id $target $args "download"
    } else {
      # Special case: DirectX — some folks map the redist EXE, which needs extraction.
      if ($id -eq 'directx_june2010' -and $DownloadMap.directx_june2010) {
        $dxZip = Join-Path $dlDir 'dxredist.exe'
        if (TryDownload 'directx_june2010' $dxZip) {
          Log "Extracting DirectX redist…"
          Start-Process $dxZip -ArgumentList "/Q /T:`"$dlDir\dx`"" -Wait
          $dxSetupPath = Join-Path $dlDir 'dx\DXSETUP.exe'
          if (Test-Path $dxSetupPath) {
            RunInstaller $name $id $dxSetupPath $args "download(extracted)"
            continue
          }
        }
      }
      Log "MISSING: $name — no local file and no/failed download."
      Result $name $id 'install' 'missing' $false (-1) "no file and no/failed download"
    }
  }
  catch {
    Log "ERROR installing $name: $($_.Exception.Message)"
    if (-not ($InstallResults | Where-Object {$_.Id -eq $id})) {
      Result $name $id 'install' 'error' $false (-2) $_.Exception.Message
    }
  }
}

# ------------------ FileZilla (optional) ------------------
$FZSService = "FileZilla Server"
$FZSInstaller = Get-ChildItem -Path (Join-Path $PayloadRoot 'filezilla') -File -ErrorAction SilentlyContinue | Where-Object {$_.Name -match '\.(msi|exe)$'} | Select-Object -First 1
$FZSProgramData   = "$env:ProgramData\filezilla-server"
$FZSSystemProfile = "C:\Windows\System32\config\systemprofile\AppData\Local\filezilla-server"
$FZSConfigDir     = Join-Path $PayloadRoot 'filezilla-config'

function Install-FileZilla{
  if ($FZSInstaller) {
    Log "Installing FileZilla Server…"
    if ($FZSInstaller.Extension -eq '.msi') {
      $p = Start-Process msiexec.exe -ArgumentList "/i `"$($FZSInstaller.FullName)`" /quiet /norestart" -Wait -Passthru
      $ok = $SuccessCodes -contains $p.ExitCode
      Result "FileZilla Server" "filezilla" "install" "local" $ok $p.ExitCode (if($ok){"ok"}else{"exit $($p.ExitCode)"})
    } else {
      $p = Start-Process $FZSInstaller.FullName -ArgumentList "/S" -Wait -Passthru
      $ok = $SuccessCodes -contains $p.ExitCode
      Result "FileZilla Server" "filezilla" "install" "local" $ok $p.ExitCode (if($ok){"ok"}else{"exit $($p.ExitCode)"})
    }
  } elseif ($DownloadMap.filezilla) {
    $msi = Join-Path $dlDir 'filezilla.msi'
    if (TryDownload 'filezilla' $msi) {
      Log "Installing FileZilla Server (downloaded)…"
      $p = Start-Process msiexec.exe -ArgumentList "/i `"$msi`" /quiet /norestart" -Wait -Passthru
      $ok = $SuccessCodes -contains $p.ExitCode
      Result "FileZilla Server" "filezilla" "install" "download" $ok $p.ExitCode (if($ok){"ok"}else{"exit $($p.ExitCode)"})
    } else {
      Log "MISSING: FileZilla installer"
      Result "FileZilla Server" "filezilla" "install" "missing" $false (-1) "no file and no/failed download"
    }
  } else {
    Log "FileZilla installer not found and no download URL"
    Result "FileZilla Server" "filezilla" "install" "missing" $false (-1) "no file and no/failed download"
  }
}

function Configure-FileZilla{
  if (!(Get-Service -Name $FZSService -ErrorAction SilentlyContinue)) {
    Log "FileZilla service not present (skip config)"
    Result "FileZilla Config" "filezilla_cfg" "config" "n/a" $false 0 "service missing"
    return
  }
  if (!(Test-Path $FZSConfigDir)) {
    Log "filezilla-config folder not found (skip config)"
    Result "FileZilla Config" "filezilla_cfg" "config" "n/a" $false 0 "config folder missing"
    return
  }

  try {
    Stop-Service $FZSService -Force
    New-Item -ItemType Directory -Path $FZSProgramData,$FZSSystemProfile -Force | Out-Null
    $legacy = Join-Path $FZSConfigDir 'Filezilla Server.xml'
    $new    = Join-Path $FZSConfigDir 'settings.xml'
    $crt    = Join-Path $FZSConfigDir 'TLScertificate.crt'
    foreach ($dest in @($FZSSystemProfile,$FZSProgramData)){
      if (Test-Path $legacy) { Copy-Item $legacy (Join-Path $dest 'Filezilla Server.xml') -Force }
      if (Test-Path $new)    { Copy-Item $new    (Join-Path $dest 'settings.xml')       -Force }
      if (Test-Path $crt)    { Copy-Item $crt    (Join-Path $dest 'TLScertificate.crt') -Force }
    }
    Start-Service $FZSService
    Log "FileZilla configured"
    Result "FileZilla Config" "filezilla_cfg" "config" "local" $true 0 "applied"
  } catch {
    Log "ERROR configuring FileZilla: $($_.Exception.Message)"
    Result "FileZilla Config" "filezilla_cfg" "config" "local" $false (-2) $_.Exception.Message
  }
}

if ($InstallFileZilla) {
  Install-FileZilla
  Configure-FileZilla
} else {
  Log "FileZilla install skipped"
  Result "FileZilla Server" "filezilla" "install" "skipped" $false 0 "skipped by switch"
}

# ------------------ firewall rules ------------------
Log "Configuring Windows Firewall…"
function FW($name,$proto,$ports){
  try {
    New-NetFirewallRule -DisplayName $name -Direction Inbound -Action Allow -Protocol $proto -LocalPort $ports -Profile Any -ErrorAction SilentlyContinue | Out-Null
    Result "Firewall: $name" "fw_$($name -replace '\s','_')" "firewall" "system" $true 0 "ok"
  } catch {
    Result "Firewall: $name" "fw_$($name -replace '\s','_')" "firewall" "system" $false (-2) $_.Exception.Message
  }
}
FW "FTP 21 TCP" TCP 21
FW "FTP Passive 50000-51000 TCP" TCP "50000-51000"
FW "Games 2000-12000 TCP" TCP "2000-12000"
FW "Games 2000-12000 UDP" UDP "2000-12000"
FW "GSP 12679 TCP" TCP 12679

# ------------------ OpenSSH ------------------
Log "Installing & configuring OpenSSH (port $OpenSSHPort)…"
try {
  Add-WindowsCapability -Online -Name OpenSSH.Server~~~~0.0.1.0 | Out-Null
  Set-Service -Name sshd -StartupType Automatic
  if ((Get-Service sshd).Status -ne 'Running') { Start-Service sshd }
  $cfg = "C:\ProgramData\ssh\sshd_config"
  if (!(Test-Path $cfg)) { Start-Sleep -Seconds 2 }
  if (Test-Path $cfg) {
    (Get-Content $cfg) -replace '^\s*#?\s*Port\s+\d+','Port ' + $OpenSSHPort | Set-Content $cfg -Encoding ascii
    Restart-Service sshd
  }
  if (Get-NetFirewallRule -Name "OpenSSH-Server-In-TCP" -ErrorAction SilentlyContinue) { Disable-NetFirewallRule -Name "OpenSSH-Server-In-TCP" | Out-Null }
  New-NetFirewallRule -Name "OpenSSH-$OpenSSHPort" -DisplayName "OpenSSH Server (TCP $OpenSSHPort)" -Direction Inbound -Protocol TCP -LocalPort $OpenSSHPort -Action Allow -Profile Any -ErrorAction SilentlyContinue | Out-Null
  Result "OpenSSH Server" "openssh" "install+config" "system" $true 0 "ok"
} catch {
  Log "ERROR installing/configuring OpenSSH: $($_.Exception.Message)"
  Result "OpenSSH Server" "openssh" "install+config" "system" $false (-2) $_.Exception.Message
}

# ------------------ END: Summary ------------------
# Write CSV summary + echo human-readable table
$InstallResults | Sort-Object Name | Export-Csv -NoTypeInformation -Encoding UTF8 $summaryCsv
Log "Summary written to $summaryCsv"

Write-Host "`n==== INSTALL SUMMARY ====" -ForegroundColor Cyan
$InstallResults |
  Select-Object Name,Installed,Source,ExitCode,Notes |
  Sort-Object Name |
  Format-Table -AutoSize

if ($global:RebootFlag) {
  Write-Host "`nNOTE: One or more components requested a reboot." -ForegroundColor Yellow
  Log "One or more components returned 3010 (reboot recommended)."
}
