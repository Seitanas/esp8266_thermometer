import time
import machine
import onewire, ds18x20
import socket
import network

wlan_ssid='mywlan';
wlan_pass='mypass';
service_url='http://domain.com/esp8266'; #path to webservice
service_secret='somesecret'; #defined in web service config.php



service_url=service_url + '/update_values.php?pass=' + service_secret;
sta_if = network.WLAN(network.STA_IF)
ap_if = network.WLAN(network.AP_IF)
sta_if.active(True)
sta_if.connect(wlan_ssid, wlan_pass)
sta_if.ifconfig()
def http_get(url):
    _, _, host, path = url.split('/', 3)
    addr = socket.getaddrinfo(host, 80)[0][-1]
    s = socket.socket()
    s.connect(addr)
    s.send(bytes('GET /%s HTTP/1.0\r\nHost: %s\r\n\r\n' % (path, host), 'utf8'))
    while True:
        data = s.recv(100)
        if data:
            print(str(data, 'utf8'), end='')
        else:
            break
    s.close()

dat = machine.Pin(2)
ds = ds18x20.DS18X20(onewire.OneWire(dat))
roms = ds.scan()
print('found devices:', roms)
time.sleep(10)
while True:
    ds.convert_temp()
    for rom in roms:
        print('temperatures:', end=' ')
        value=ds.read_temp(rom)
	print(value)
	if value < 80:
	    http_get(service_url+'&value=%s' % value)
	else:
	    print ('Bad data from sensor')
    time.sleep(60)
    print()