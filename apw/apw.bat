cd "D:\Program Files\MySQL\MySQL Server 5.0\bin"

mysqlimport.exe --local --user=root --password=ifl@b --delete --fields-terminated-by="," --fields-enclosed-by="'" --fields-escaped-by="'" --lines-terminated-by="\r\n" oilfiltersonline D:\inetpub\viarttest\apw\apw.csv 