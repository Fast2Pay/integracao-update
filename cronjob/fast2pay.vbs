do

Dim shell
Set shell = CreateObject("WScript.Shell")
shell.CurrentDirectory = "C:\wamp64\www\fast2pay\cronjob"
shell.Run "cron.bat", 0, false

wscript.sleep 5000

loop