
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


## Product Type Mapping
When a seller sets a seller-product to "active", then:
- [x] ... a golden product is created or selected (if EAN or other non-merchant specific article number matches).
- [x] ... the product-type is determined via AI
- [ ] ... the attributes (of the seller) are mapped to the attributes of the selected product-type via AI. Attributes may be split or merged during this step (e.g. dimensions). Attributes, that cannot be mapped, will be saved to an unmapped attributes field in the golden record
- [x] ... all textual content is translated into the owner's configured locales

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

 
# Variant creation
- [ ] TODO

TODO:
- Variant - localizable flag is ignored 


Flow (Traditinal Marketplace)
- Sellers upload their products and variants (any language)
- Golden Product is created (based on the seller's data and translated to the configured locales)
- Seller products are linked to the golden product
- Seller images are copied over to the golden product
- Golden product has no own price or stock, because the customer is selecting the seller in the shop before checkout

How the product master data of the golden product is created. Options:
- The given data from the seller is used as a base for the golden product.
- The product data is retrieved from an external data provider (like Icecat, or GS1). If such a source is available, this often results in a higher quality.

How & when a golden product is created:
- You might want to create your catalog (~ golden products) upfront and let sellers link their offers to it. This way you have full control over your catalog, but restrict the number of products.
- You might want to create the golden products on the fly, when a seller uploads a product. This requires a review and rating process to ensure qualityl
- There are also mixed approaches, where you create golden products on the fly based on seller's data and then improve/enrich them via a review process.


# TODO
* Multi-selcts are not possible with current schema. Needed?
* Change of product-type requires a re-creation of the golden product
* Rename of non-translatable attributes needs to be saved differently

Challenge:
Die übersetzten Options-Value, die jeweils zu einem Value gehören müssen gleichzeitig gewechselt werden, allerdings gibt es Klammer. Man könnte das localized value noch aus der Tablle rausziehen in ein product_type_attribute_option_value_localizeds. 

  ProductTypeAttributeOptionValue:
    name: string (e.g. skinny)


  ProductTypeAttributeOptionValueLocalized:
    value
    localeId