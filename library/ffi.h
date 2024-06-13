#include "zend_types.h"

int zval_sizeof(zval *zv_ptr);

int zval_class_sizeof(zval *zv_ptr);

int properties_sizeof(zval *srcProperty, int count);

zval *get_zval_by_name(char *name);
