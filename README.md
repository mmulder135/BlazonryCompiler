# BlazonCompiler

This package is (the start of) a semi-structured compiler for blazonry, it compiles a blazon into a SVG picture.

## Structure



## Install

Via Composer

``` bash
$ composer require BlazonCompiler/Compiler
```


## Testing

``` bash
$ composer test
```

## TODO
### Tokenizer
- [x] Recognize basic tokens
- [x] Handle comma's and spaces properly
- [x] Handle strings (unrecognized text)

### Parser
- [x] Parse single color shield
- [ ] Parse field
- [ ] Parse ordinaries (to be extended)
- [ ] Parse charges (to be extended)

### Code generator
- [x] Create SVG of shield with single color
- [ ] Generate fields
- [ ] Generate ordinaries (to be extended)
- [ ] Generate charges (to be extended)
- [ ] Generate charges on ordinaries (to be extended)

## Specifications

### Field
Level 1: simple matches - tokenizer
```
Metal = 'Or' | 'Argent'
Tincture = 'Azure' | 'Vert' | 'Gules' | 'Sable' | 'Purpure'
Fur = 'Ermine' | 'Vair'
Per = 'per'
Ordinary = 'fess' | 'bend' | 'pale' | 'chevron' | 'cross'
Division = 'quarterly'
Sinister = 'sinister'
Parted = 'party'
PartitionLine = 'engrailed', 'invected', 'embattled', 'indented', 'dancetty', 'wavy', 'nebuly'
```
Level 2: short matches - simple short matches, generalizations
```
Color = Metal | Tincture | Fur
Partition   =   Division 
            |   Per Ordinary 
```
Level 3: field - Flexible parsing that attempts to find a field declaration \
This step does not rely on simple regexes, below is for understanding
```
Field =     Color 
        |   Parted? Partition (PartitionLine | Sinister)? Color Color
```
Rules:

| Token         | Indicates need of |
| ------        | ------------------|
| Parted        | Partition         |
| Partition     | Color, Color      |
Anything that is between the first token and the point that all the needed tokens are found will become part of field.
Tokens that do not add meaning will be removed from the parse tree, these are: Comma, And, Parted.
#### Examples:
Single color:
```
Or
Azure
Gules
Ermine
```
Simple two color partition:
```
Per bend azure and gules
Per pale argent and sable
Per pale, argent and sable
Party per pale argent and sable
Quarterly azure and or
```
in a "party" coloured field, that colour or tincture is mentioned first which occupies the more important part of the escutcheon. Thus, in a field "per bend," "per chevron," or "per fess," the upper portion of the field is first referred to; in a coat "per pale," the dexter side is the more important; and in a coat "quarterly," the tinctures of the 1st and 4th quarters are given precedence of the tinctures of the 2nd and 3rd. 

Partition with special lines:
``` 
per bend sinister argent and sable
per bend engrailed argent and sable
per bend sinister engrailed argent and sable
per bend engrailed sinister argent and sable
Party per pale embattled azure and or
```
