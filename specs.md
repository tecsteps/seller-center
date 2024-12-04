
# Owner - Product Types & Attributes
- [x] As an owner, I can CRUD product types. Each product type includes a name (e.g., "Book").
- [x] As an owner, I can define product attributes per product types. Each attribute has a name (e.g., "material"), a type, a form field, a set of validators and options (for selects). 


| Attribute Type | Compatible FilamentPHP Form Field Types             |
|----------------|-----------------------------------------------------|
| **Text**       | `TextInput`, `Textarea`                            |
| **Boolean**    | `Checkbox`, `Toggle`                               |
| **Number**     | `TextInput` (with number validation)               |
| **Select**     | `Select`                                           |
| **URL**        | `TextInput` (with URL validation)                  |
| **Color**      | `ColorPicker`                                      |

Field type and Validators:

| Form Field       | Validators Mentioned in Docs                              |
|-------------------|----------------------------------------------------------|
| **TextInput**     | Required, Email, URL, Min (character length), Max (character length), Regex, String |
| **Textarea**      | Required, Min (character length), Max (character length), String |ls
| **Checkbox**      | Required, Boolean                                        |
| **Toggle**        | Required, Boolean                                        |
| **Select**        | Required, Enum, In (specific values), NotIn (excluded values) |
| **ColorPicker**   | Required, Hexadecimal Color Code                         |
| **Repeater**      | Array, Min (items), Max (items), Required                |
| **CheckboxList**  | Array, Required                                          |
| **KeyValue**      | Array (with key-value format validation), Required       |

TODO: 
- attribute options must be translatable


## Product Type Mapping
When a seller sets a seller-product to "active", then:
- [ ] ... a golden product is created or selected (if EAN or other non-merchant specific article number matches).
- [ ] ... the product-type is determined via AI
- [ ] ... the attributes (of the seller) are mapped to the attributes of the selected product-type via AI. Attributes may be split or merged during this step (e.g. dimensions). Attributes, that cannot be mapped, will be saved to an unmapped attributes field in the golden record


- [ ] ... all textual content is translated into the owner's configured locales
- [ ] If there is already a golden record that is related to a seller-product then ....
- [ ] If there is already a golden record that is not yet related to a seller-product, but has the same EAN then ....
- [ ] TODO Variant creation

# Owner - Golden Products
- [x] As an Owner, I can see a list of all Golden Products. I can also see the number of related seller-products (from different sellers that sell the same product).
- [ ] As an owner, I see the selected images for each golden product
- [ ] As an owner, I see the stock levels of the golden product (accross sellers)
- [ ] As an owner, there is a status badge for "new" and "updated"
- [ ] As an owner, I can see the golden product data in all configured languages
- [ ] As an owner, I can see golden product form based on the defined product types and their attributes in all configured languages, side-by-side with the seller's data.
- [ ] As an owner, I can change any data of the golden record.
- [ ] As an owner, I can choose which image set is used for my shop.
- [ ] As an owner, I can set a status on the golden product, so it's published to my shop.
- [ ] TODO status


TODO:
- Variant - localizable flag is ignored 