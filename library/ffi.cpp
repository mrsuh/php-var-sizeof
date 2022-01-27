#include "main/php.h"
#include "zend_types.h"
#include "zend_hash.h"
#include "zend_string.h"
#include "ffi.h"

int main() {}

int zval_sizeof(zval *zv_ptr) {
    _zend_value value = zv_ptr->value;
    switch (Z_TYPE_P(zv_ptr)) {
        case IS_UNDEF:
        case IS_NULL:
        case IS_FALSE:
        case IS_TRUE:
        case IS_LONG:
        case IS_DOUBLE:
        case IS_CONSTANT_AST:
            return sizeof(*zv_ptr);
        case IS_CALLABLE:
            return sizeof(*zv_ptr) + sizeof(*value.func);
        case IS_REFERENCE:
            return sizeof(*zv_ptr) + sizeof(*value.ref);
        case IS_RESOURCE:
            return sizeof(*zv_ptr) + sizeof(*value.res);
        case IS_STRING:
            return sizeof(*zv_ptr) + ZSTR_LEN(value.str);
        case IS_ARRAY: {
            HashTable *ht = value.arr;
            HashPosition pos;
            zval *data;
            int elementsSize = 0;

            for (
                    zend_hash_internal_pointer_reset_ex(ht, &pos);
                    (data = zend_hash_get_current_data_ex(ht, &pos)) != NULL;
                    zend_hash_move_forward_ex(ht, &pos)
                    ) {
                elementsSize += zval_sizeof(data) - sizeof(zval);
            }

            int allocSize = 0;
            if (HT_IS_PACKED(ht)) {
                allocSize = HT_PACKED_SIZE(ht);
            } else {
                allocSize = HT_SIZE(ht);
            }

            return sizeof(*zv_ptr) + sizeof(*ht) + allocSize + elementsSize;
        }

        case IS_OBJECT: {
            zend_object *object = value.obj;
            int propertiesSize = properties_sizeof(object->properties_table, object->ce->default_properties_count);

            return sizeof(*zv_ptr) + sizeof(*object) + propertiesSize;
        }
    }

    return 0;
}

int properties_sizeof(zval *srcProperty, int count) {
    int size = 0;
    if (count > 0) {
        zval *endProperty = srcProperty + count;

        do {
            size += zval_sizeof(srcProperty);
            srcProperty++;
        } while (srcProperty != endProperty);
    }

    return size;
}

int zval_class_sizeof(zval *zv_ptr) {

    if (Z_TYPE_P(zv_ptr) != IS_OBJECT) {
        return 0;
    }

    zend_object *object = zv_ptr->value.obj;

    zend_class_entry *classEntry = object->ce;

    int propertiesSize = properties_sizeof(
            classEntry->default_properties_table,
            classEntry->default_properties_count
    );

    int staticMembersSize = properties_sizeof(
            classEntry->default_static_members_table,
            classEntry->default_static_members_count
    );

    int inheritanceCacheSize = sizeof(*classEntry->inheritance_cache);

    int functionTableSize = HT_SIZE(&classEntry->function_table);
    int propertiesInfoSize = HT_SIZE(&classEntry->properties_info);
    int constantsTableSize = HT_SIZE(&classEntry->constants_table);

    return
            sizeof(*classEntry) +
            ZSTR_LEN(classEntry->name) +
            propertiesSize +
            staticMembersSize +
            inheritanceCacheSize +
            functionTableSize +
            propertiesInfoSize +
            constantsTableSize;
}

zval *get_zval_by_name(char *name) {

    HashTable *symbol_table = zend_array_dup(zend_rebuild_symbol_table());

    zend_string *key_name = zend_string_init(name, strlen(name), 0);
    zval *data = zend_hash_find(symbol_table, key_name);

    zend_string_release(key_name);
    zend_array_destroy(symbol_table);

    return data;
}

extern "C" int var_sizeof(char *name) {

    zval *data = get_zval_by_name(name);
    if (data != NULL) {
        return zval_sizeof(data);
    }

    return 0;
}

extern "C" int var_class_sizeof(char *name) {

    zval *data = get_zval_by_name(name);
    if (data != NULL) {
        return zval_class_sizeof(data);
    }

    return 0;
}
