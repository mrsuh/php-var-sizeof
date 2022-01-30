UNAME := $(shell uname -s)
HFOLDERS := -I php-src -I php-src/Zend -I php-src/main -I php-src/TSRM -I php-src/ext/spl
COMMAND := @echo Invalid operation system: $(UNAME)

ifeq ($(DEBUG), 1)
	DEBUGFLAG := -D DEBUG
else
	DEBUGFLAG :=
endif

ifeq ($(UNAME), Linux)
	COMMAND := g++ -O3 -fPIC -shared $(HFOLDERS) $(DEBUGFLAG) -o library/ffi_linux.so library/ffi.cpp
endif

ifeq ($(UNAME), Darwin)
	COMMAND := clang -shared -undefined dynamic_lookup $(HFOLDERS) $(DEBUGFLAG) -o library/ffi_darwin.dylib library/ffi.cpp
endif

all:
	$(COMMAND)
