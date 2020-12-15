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
- [ ] Handle strings (unrecognized text)

### Parser
- [ ] Parse single color shield
- [ ] Parse field
- [ ] Parse ordinaries (to be extended)
- [ ] Parse charges (to be extended)

### Code generator
- [ ] Create SVG of shield with single color
- [ ] Generate fields
- [ ] Generate ordinaries (to be extended)
- [ ] Generate charges (to be extended)
- [ ] Generate charges on ordinaries (to be extended)
