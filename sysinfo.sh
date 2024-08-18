#!/bin/bash

while true; do
    clear

    # Sistema Operacional
    os_name=$(uname -s)
    os_version=$(uname -r)

    # Usuário Atual
    user=$(whoami)

    # Path Atual
    current_path=$(pwd)

    # Uso de CPU
    cpu_usage=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1"%"}')

    # Uso de RAM
    ram_usage=$(free -m | awk 'NR==2{printf "%.2f%%", $3*100/$2 }')

    # Endereço IP
    ip_address=$(hostname -I | awk '{print $1}')

    echo "=============================================="
    echo "               System Information             "
    echo "=============================================="
    echo "Sistema Operacional  : $os_name"
    echo "Versão do Software   : $os_version"
    echo "Usuário Atual        : $user"
    echo "Path Atual           : $current_path"
    echo "Uso de CPU           : $cpu_usage"
    echo "Uso de RAM           : $ram_usage"
    echo "Endereço IP          : $ip_address"
    echo "=============================================="

    # Aguarda 30 segundos antes de executar novamente
    sleep 30
done
