# webkiosk
a web based kiosk and electronic guestbook

Kiosk HOW-TO
---

Overview
----
The idea here is to create a PC that runs a web browser and a web browser only... or run a video, or run a slide show, etc using the following pricipals.

1. Computer operating system should be lightweight, secure, and up to date. This means that the hardware requirements are low (perhaps running on something as small as a raspberry pi.
1. Task should be "unkillable" or start, and restart automatically
1. Keyboard and mouse should be limited to not allow for unintended user actions

Steps
----

Start with a minimal operating system install
Debian Mini ISO is the start here.

Configure Virtual Machine Image to boot debian mini iso

hostname:
kiosk

domain name - leave blank

no proxy, default debian mirror

set root password (for simplicity, I typically make root password and standard user password the same ... YMMV)

Set the timezone to your timezone

Use the entire disk / all files in one partition


Continue to let the base system install


Once that is complete, un-select everything but the SSH server as the only thing to install (we'll remove this later, but it'll make our lives easier to finish the install)


Install GRUB to the master boot record.


Device for boot loader: /dev/sda


Continue the instalation allowing it to reboot. (you may need to remove the virtual CDROM from the virtual machine)


log in with the user "kiosk" and the password you assigned to it.


issue the following commands
su
(ENTER PASSWORD FOR ROOT USER)
Optional: add kiosk user to sudoer … or don’t
```shell
apt-get update
apt-get install sudo
adduser kiosk sudo
exit
exit
(LOG BACK IN WITH kiosk USER)
```

(YOU MAY WANT TO TAKE A SNAPSHOT HERE SO THAT YOU CAN EASILY RESTORE IF YOU MESS SOMETHING UP)

```shell
wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add -
cd /etc/apt/sources.list.d/
nano google.list
```

add the following line to google.list
```
deb http://dl.google.com/linux/chrome/deb/ stable main
```
save the file and exit

install google chrome stable
```shell
apt-get update
apt-get install google-chrome-stable
```

next get the following ...
```
apt-get install xprintidle psmisc xdotool xorg ratpoison nodm numlockx
nano /etc/default/nodm
```
(CHANGE the following:)
```
NODM_ENABLED=true
NODM_USER=kiosk
```
(NEXT WE'll REBOOT THE VIRTUAL MACHINE TO TEST IF OUR NEW 'DESKTOP ENVIRONMENT IS WORKING')
sudo shutdown -r now
(YOU SHOULD NOW SEE WHAT IS A BLANK SCREEN ... PRESS "ctrl t" AND THEN PRESS "c" TO LAUNCH A CONSOLE WINDOW ... THIS IS THE RATPOISON WINDOW MANAGER'S ESCAPE KEY FOLLOWED BY THE COMMAND TO LAUNCH THE CONSOLE)
(AT THIS POINT, YOU MAY WANT TO SWITCH TO A SSH CLIENT BE ABLE TO EASILY COPY AND PASTE DATE INTO THE SYSTEM)


(CREATE THE FOLLOWING FILES WITH THE FOLLOWING CONTENTS)


.xmodmaprc file contents:

```
pointer = 1 2 32 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 3
keycode 67 = 32
keycode 68 = 32
keycode 69 = 32
keycode 70 = 32
keycode 71 = 32
keycode 72 = 32
keycode 73 = 32
keycode 74 = 32
keycode 75 = 32
keycode 76 = 32
keycode 95 = 32
keycode 96 = 32
keycode 0x25 = 32
keycode 0x69 = 32
keycode 0x40 = 32
keycode 0x6c = 32
keycode 0xcd = 32
keycode 0x85 = 32
keycode 0x86 = 32
keycode 0xce = 32
keycode 0xcf = 32
clear control
remove control = Control_L Control_R
```

start_kiosk.sh file contents:
```
#!/bin/bash

xset s off
xset s noblank
xset -dpms

#/usr/bin/xmodmap -e "pointer = 1 2 32 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 3"

xmodmap ~/.xmodmaprc

numlockx on

#chromium-browser

while true; do
        /sbin/ifdown wlan0 &
        wait
        #/sbin/ifup wlan0 &
        #wait
        #chromium --app-shell-host-window-bounds=1920x1080 --proxy-server="127.0.0.1:8888" --app=http://localhost --incognito
        #chromium --app-shell-host-window-bounds=1440x900 --app=https://university-of-dayton.culturalspot.org/exhibit/representations-of-the-flight-to-egypt-on-stamps/OAJSsoZqg$
        google-chrome --app=https://flyers.udayton.edu/search/j --incognito
        /sbin/ifdown wlan0 &
        wait
done
#END start_kiosk.sh file
```
set the file to be exec
```
chmod +x ./start_kiosk
```

check_idle.sh file contents:
```
#!/bin/bash
export DISPLAY=:0.0;

#while true; do

#7 minutes in milliseconds = 420000
#5 minutes in milliseconds = 300000
#1 minute in milliseconds = 60000

if [ `xprintidle` -gt 300000 ]; then
        #       debug
        #xprintidle | tee -a idle.txt
        #echo "idle!"
        
        xdotool mousemove 100 300; xdotool mousemove 300 100;
        killall chrome
        #       debug
        #| tee -a idle.txt
fi

sleep 3;
#done
# end check_idle.sh file
```

chmod +x check_idle.sh


.ratpoisonrc file contents:
```
exec /home/kiosk/start_kiosk.sh
# END .ratpoisonrc file
```

set the cron entry from the kiosk users login
```
$ crontab -e
```

```
crontab entry:
* * * * *       ~/check_idle.sh
```

(OPTIONALLY REMOVE SSH SERVER)
```shell
# apt-get remove openssh-server
# apt-get autoremove
```

(REBOOT)
sudo shutdown -r now


Install optional local http server with PHP
----

This will allow us to serve local http pages, and do some form processing 

```shell
# apt-get install lighttpd php5-cgi
# lighty-enable-mod fastcgi
# lighty-enable-mod fastcgi-php
# service lighttpd force-reload
```

For added security, bind the http server to localhost only
```shell
# nano /etc/lighttpd/lighttpd.conf
```
add the following line ...
```
server.bind = "127.0.0.1"
```

Load php files to the location :
```
/var/www/html
```

