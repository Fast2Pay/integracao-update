x=msgbox("A integra��o est� sendo executada!" ,0, "Integra��o Fast2Pay")

do

Dim shell
Set shell = CreateObject("WScript.Shell")
shell.CurrentDirectory = "C:\wamp64\www\fast2pay\cronjob"
shell.Run "cron.bat", 0, false

wscript.sleep 5000

loop