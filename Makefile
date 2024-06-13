OS := $(shell uname -s)
PLATFORM := $(shell uname -m)
HFOLDERS := -I php-src -I php-src/Zend -I php-src/main -I php-src/TSRM -I php-src/ext/spl
COMMAND := @echo Invalid operation system: $(OS)

ifeq ($(DEBUG), 1)
	DEBUGFLAG := -D DEBUG
else
	DEBUGFLAG :=
endif

ifeq ($(OS), Linux)
	COMMAND := g++ -O3 -fPIC -shared $(HFOLDERS) $(DEBUGFLAG) -o library/ffi_linux_$(PLATFORM).so library/ffi.cpp
endif

ifeq ($(OS), Darwin)
	COMMAND := clang -shared -undefined dynamic_lookup $(HFOLDERS) $(DEBUGFLAG) -o library/ffi_darwin_$(PLATFORM).dylib library/ffi.cpp
endif

all:
	$(COMMAND)
