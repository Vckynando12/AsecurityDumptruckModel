Set WshShell = CreateObject("WScript.Shell") 
WshShell.Run "C:\path\to\run_schedule.bat", 0, False
Set WshShell = Nothing
