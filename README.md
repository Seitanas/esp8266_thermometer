# ESP8266 thermometer.

Digital thermometer based on ESP 8266 (ESP-01) module.  
Temperature data is read from DS18B20 sensor.  

ESP module sends data via WIFI to remote web server.  
  
  
Demo can be viewed on: http://esp.ring.lt/

Requirements:  
WEB: PHP MySQL  
ESP8266: Micropython firmware  
  
  
Since project is aimed to 512K flash ESP module, which is too small to have internal flash filesystem, you will have to compile monitor into firmware:  
Follow steps described in https://github.com/adafruit/esp8266-micropython-vagrant to create your build environment.  
  
In your build environment:  
  
  
    cd micropython
    git submodule update --init
    make -C mpy-cross
    cd esp8266
  
overwrite `esp8266_512k.ld` with `esp8266/build_config/esp8266_512k.ld`  
overwrite `modules/_boot.py` with `esp8266/build_config/_boot.py`  
copy `esp8266/monitor.py'` to `scripts/`  
configure `scripts/monitor.py`  
  
    make 512k
    cp ./build/firmware-combined.bin /vagrant/
  
  
On host machine (esp8266 should be connected to it in flash mode):  
  
    esptool.py --port YOURPORT erase_flash
    esptool.py --port YOURPORT --baud 460800 write_flash --flash_size=detect 0 PATH_TO_YOUR_VAGRANT_VM\firmware-combined.bin
  
  
  
On ESP modules with flash > 512KB, just rename `monitor.py` to `main.py` and push it to internal file system