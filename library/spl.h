#include "zend_types.h"

typedef struct _spl_array_object {
	zval              array;
	uint32_t          ht_iter;
	int               ar_flags;
	unsigned char	  nApplyCount;
	zend_function     *fptr_offset_get;
	zend_function     *fptr_offset_set;
	zend_function     *fptr_offset_has;
	zend_function     *fptr_offset_del;
	zend_function     *fptr_count;
	zend_class_entry* ce_get_iterator;
	zend_object       std;
} spl_array_object;

static inline spl_array_object *spl_array_from_obj(zend_object *obj) /* {{{ */ {
	return (spl_array_object*)((char*)(obj) - XtOffsetOf(spl_array_object, std));
}
