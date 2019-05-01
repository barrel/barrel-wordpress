#!/bin/bash

####################################################################
## This create module script is for creating an empty module
## This script can be run from within the theme directory using npm (see package.json)
##
## npm run import-module -- -n=$MODULE_NAME -t="wordpress|shopify"
## 
## Assumptions:
## The module exists in https://gitlab.com/barrel/base-modules
##
####################################################################

# Renders a text based list of options that can be selected by the
# user using up, down and enter keys and returns the chosen option.
# Author: https://unix.stackexchange.com/a/415155/14014
#
#   Arguments   : list of options, maximum of 256
#                 "opt1" "opt2" ...
#   Return value: selected index (0 for opt1, 1 for opt2 ...)
function select_option {

    # little helpers for terminal print control and key input
    ESC=$( printf "\033")
    cursor_blink_on()  { printf "$ESC[?25h"; }
    cursor_blink_off() { printf "$ESC[?25l"; }
    cursor_to()        { printf "$ESC[$1;${2:-1}H"; }
    print_option()     { printf "   $1 "; }
    print_selected()   { printf "  $ESC[7m $1 $ESC[27m"; }
    get_cursor_row()   { IFS=';' read -sdR -p $'\E[6n' ROW COL; echo ${ROW#*[}; }
    key_input()        { read -s -n3 key 2>/dev/null >&2
                         if [[ $key = $ESC[A ]]; then echo up;    fi
                         if [[ $key = $ESC[B ]]; then echo down;  fi
                         if [[ $key = ""     ]]; then echo enter; fi; }

    # initially print empty new lines (scroll down if at bottom of screen)
    for opt; do printf "\n"; done

    # determine current screen position for overwriting the options
    local lastrow=`get_cursor_row`
    local startrow=$(($lastrow - $#))

    # ensure cursor and input echoing back on upon a ctrl+c during read -s
    trap "cursor_blink_on; stty echo; printf '\n'; exit" 2
    cursor_blink_off

    local selected=0
    while true; do
        # print options by overwriting the last lines
        local idx=0
        for opt; do
            cursor_to $(($startrow + $idx))
            if [ $idx -eq $selected ]; then
                print_selected "$opt"
            else
                print_option "$opt"
            fi
            ((idx++))
        done

        # user key control
        case `key_input` in
            enter) break;;
            up)    ((selected--));
                   if [ $selected -lt 0 ]; then selected=$(($# - 1)); fi;;
            down)  ((selected++));
                   if [ $selected -ge $# ]; then selected=0; fi;;
        esac
    done

    # cursor position back to normal
    cursor_to $lastrow
    printf "\n"
    cursor_blink_on

    return $selected
}

# Variables
MODULE_PATH="./modules"
MODULE_ROOT_PATH="./.base_modules"
MODULE_SRC_PATH="$MODULE_ROOT_PATH/src/modules"
REPO="git@gitlab.com:barrel/base-modules.git"

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

# handle arguments
for i in "$@"; do
case $i in
    -n=*|--name=*)
    MODULE_NAME="${i#*=}"
    shift # past argument=value
    ;;
    -t=*|--type=*)
    EXCLUDE="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "\n${BOLD}Utility Usage:${RESET} This script can be run from anywhere within the theme directory using npm:\n"
    echo "npm run create-module -- -n=MODULE_NAME -t=TEMPLATE_TYPE\n"
    echo "--\n"
    echo "${BOLD}Arguments:${RESET}"
    echo "-n | --name - Module name: -n=the-module"
    echo "-t | --type - Supports either 'wordpress' or 'shopify': -t=\"wordpress\""

    shift # past argument with no value
    exit
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    exit
    ;;
esac
done

# status
NAME_GIVEN=$(if [ -z ${MODULE_NAME+x} ]; then echo "none provided"; else echo "'$MODULE_NAME'"; fi)
LINES="${BOLD}------------------${RESET}"
echo "$LINES$LINES"
echo "${BOLD}Module Install Path :${RESET} $MODULE_ROOT_PATH${DEFAULT}"
echo "${BOLD}Module Source Path  :${RESET} $MODULE_SRC_PATH${DEFAULT}"
echo "${BOLD}Module Theme Path   :${RESET} $MODULE_PATH${DEFAULT}"
echo "${BOLD}Module Name         :${RESET} $NAME_GIVEN${DEFAULT}"
echo "$LINES$LINES"

# check if install path !exists, clone base modules
if [ ! -d "$MODULE_ROOT_PATH" ]; then
    echo "${YELLOW}Cloning to $MODULE_ROOT_PATH...${DEFAULT}"
    git clone $REPO $MODULE_ROOT_PATH
    cd $MODULE_ROOT_PATH
    git checkout develop
    cd ../
    if [[ "$?" -eq 0 ]]; then
        echo $DONE
    else
        echo "${RED}There was a problem cloning base modules.${DEFAULT}"
        exit 1
    fi
else
    cd $MODULE_ROOT_PATH
    git fetch origin && git pull origin develop
fi

## Check if module name was provided before moving on
if [ -z ${MODULE_NAME+x} ]; then
    echo "\n${BLUE}Please define a module name.${DEFAULT}\n"
    PS3="${YELLOW}Please enter your choice: ${DEFAULT}"
    options=("Enter Module Name(s)" "Select From List")
    select opt in "${options[@]}"
    do
        case $opt in
            "Enter Module Name(s)")
                echo "\n${BLUE}What is the module name?${DEFAULT}"
                read MODULE_NAME
                MODULE_NAMES=($MODULE_NAME)
                break
                ;;
            "Select From List")
                echo "Select one option using up/down keys and enter to confirm:"
                echo

                options=( $(ls -1a $MODULE_SRC_PATH | tail -n +3) )
                select_option "${options[@]}"
                choice=$?
                MODULE_NAMES=("${options[$choice]}")
                break
                ;;
            *) echo "${U1}${RED}Invalid option${DEFAULT} '$REPLY'${EL}\n"$(tput ed);;
        esac
    done
fi

# check if module found, then copy
for MODULE_NAME in $MODULE_NAMES; do
    if [ ! -d "$MODULE_PATH/$MODULE_NAME" ]; then
        if [ ! -d "$MODULE_SRC_PATH/$MODULE_NAME" ]; then
            echo "${RED}Failure! Module '$MODULE_NAME' does not exist.${DEFAULT}"
            exit 1
        else
            echo "${YELLOW}Copying $MODULE_NAME...${DEFAULT}"
            CAPTURE=$(cp -r "$MODULE_SRC_PATH/$MODULE_NAME" $MODULE_PATH > /dev/null)
            if [[ "$?" -gt 0 ]]; then
                echo "${RED}Failure!${DEFAULT}"
                echo $CAPTURE
                exit 1
            fi
        fi
        echo $DONE
    fi
done
exit