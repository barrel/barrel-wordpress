#!/bin/bash

# Terminal colors
DEFAULT=$(tput setaf 7 -T xterm)
RED=$(tput setaf 1 -T xterm)
GREEN=$(tput setaf 2 -T xterm)
YELLOW=$(tput setaf 3 -T xterm)
BLUE=$(tput setaf 4 -T xterm)
BOLD=$(tput bold -T xterm)
NORMAL=$(tput sgr0 -T xterm)
OK="${GREEN}ok${DEFAULT}"
DONE="${GREEN}done${DEFAULT}"
