#!/bin/bash

# Terminal colors
DEFAULT=$(tput setaf 7 -T xterm)
RED=$(tput setaf 1 -T xterm)
GREEN=$(tput setaf 2 -T xterm)
YELLOW=$(tput setaf 3 -T xterm)
BLUE=$(tput setaf 4 -T xterm)
BOLD=$(tput bold -T xterm)
RESET=$(tput sgr0 -T xterm)
DIM=$(tput dim -T xterm)
# begin underline mode
SMUL=$(tput smul -T xterm)
# exit underline mode
RMUL=$(tput rmul -T xterm)
# up x lines
U1=$(tput cuu 1 -T xterm)
#EOL
EL=$(tput el -T xterm)
OK="${GREEN}ok${DEFAULT}"
DONE="${GREEN}done${DEFAULT}"
