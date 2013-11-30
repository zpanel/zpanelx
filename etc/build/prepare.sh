#!/bin/bash
clear
# ZPanel Enviroment Configuration Tool for *NIX based systems.
# Written by Bobby Allen, 19/02/2012

echo "ZPanel Enviroment Configuration Tool"
echo "===================================="
echo ""
echo "If you need help, please visit our forums: http://forums.zpanelcp.com/"
echo ""
echo "Creating folder structure.."
mkdir -p /etc/zpanel/{configs,panel,docs}
mkdir -p /var/zpanel/{hostdata,logs,backups,temp}
mkdir -p /var/zpanel/hostdata/zadmin/public_html
echo "Complete!"

# is param passed? 
if [ -n "$1" ]; then
 ## we have a param passed : lets match it up
    VALID_OSS=('ubuntu11.10' 'ubuntu12.04' 'centos6.2' 'centos6.3' 'windows')
    OS_FOLDER=('ubuntu_11_10' 'ubuntu_12_04' 'centos_6_2' 'centos_6_3' 'ms_windows')
    let key=0
    FOUND=false
    for os in ${VALID_OSS[@]}; do
        ## does param match an option ?
        if [ ${os} == "$1" ]; then
            echo "Copying ZPanel files into place.."
            cp -R ../../* /etc/zpanel/panel/
            echo "Complete!"
            echo "Copying application configuration files for ${os}.."
            cp -R config_packs/${OS_FOLDER[key]}/* /etc/zpanel/configs
            echo "Complete!"
            FOUND=true
        fi
        let "key += 1"
    done
    if [ $FOUND == "false" ]
        then 
            echo "ERROR $1 not : found  OPTIONS ARE ${VALID_OSS[@]}, exiting" 
            exit
    fi
    ## else - continue
fi

############### MANUALLY DONE ############################
# NAME_OF_PACK=ubuntu_11_10
#echo "Copying ZPanel files into place.."
#cp -R ../../* /etc/zpanel/panel/ 
#echo "Complete!"
# echo "Copying application configuration files.."
# cp -R -v config_packs/${NAME_OF_PACK}/* /etc/zpanel/configs
# echo "Complete!"
###########################################################
echo "Setting permissions.."
chmod -R 777 /etc/zpanel/ /var/zpanel/
echo "Complete!"
echo "Registering 'zppy' client.."
ln -s /etc/zpanel/panel/bin/zppy /usr/bin/zppy
chmod +x /usr/bin/zppy
ln -s /etc/zpanel/panel/bin/setso /usr/bin/setso
chmod +x /usr/bin/setso
ln -s /etc/zpanel/panel/bin/setzadmin /usr/bin/setzadmin
chmod +x /usr/bin/setzadmin
echo "Complete!"
echo ""
echo ""
echo "The Zpanel directories have now been created in /etc/zpanel and /var/zpanel"
echo ""
exit
