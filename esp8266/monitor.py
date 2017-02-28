#ESP8266 thermometer
#Tadas Ustinavičius
#config:
wlan_ssid='ssid'
wlan_pass='pw'
service_url='http://domain.com/'
service_secret='secret'
GPIOpin=2 #pin connected to DS18x20

import time
import machine
import onewire, ds18x20
import socket
import network
service_url=service_url + 'update_values.php?pass=' + service_secret
sta_if = network.WLAN(network.STA_IF)
ap_if = network.WLAN(network.AP_IF)
sta_if.active(True)
sta_if.connect(wlan_ssid, wlan_pass)
sta_if.ifconfig()
def reboot_sequence(point):
    print ('Got exception at: %s. Rebooting' % point)
    time.sleep(5)
    machine.reset()
def http_get(url):
    _, _, host, path = url.split('/', 3)
    try:
        addr = socket.getaddrinfo(host, 80)[0][-1]
        s = socket.socket()
        s.connect(addr)
        s.send(bytes('GET /%s HTTP/1.0\r\nHost: %s\r\nUser-Agent: https://github.com/Seitanas/esp8266_thermometer\r\n\r\n' % (path, host), 'utf8'))
        while True:
            data = s.recv(100)
            if data:
                print(str(data, 'utf8'), end='')
            else:
                break
        s.close()
    except:
        reboot_sequence('http_get')
try:
    dat = machine.Pin(GPIOpin)
    ds = ds18x20.DS18X20(onewire.OneWire(dat))
    roms = ds.scan()
    print('found devices:', roms)
except:
    reboot_sequence("ds.scan");
time.sleep(7)
while True:
    print ('Starting new read')
    try:
        ds.convert_temp()
    except:
        reboot_sequence('convert_temp')
    for rom in roms:
        print('temperatures:', end=' ')
        try:
            value=ds.read_temp(rom)
            print(value)
            if value < 80:#sensor returns 85 if read fails
                http_get(service_url+'&value=%s' % value)
            else:
                print ('Bad data from sensor')
        except:
            reboot_sequence('read_temp')
    time.sleep(60)
    print()