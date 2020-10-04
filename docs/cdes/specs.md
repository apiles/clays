# Clays Descriptor Format

`.cdes` is Clays Descriptor Format.

This file format is used to describe a cluster. It aims to be both human and machine readable.

## 1. Header

`.cdes` file starts with header:

```cdes
clays.descriptor.version 1
```

Currently only version 1 is supported.

## 2. Syntax

The syntax of cdes is very simple.

It use indents to determine blocks.

## 3. Keywords

Only 5 keywords are present:

`clays.descriptor.version`, `cluster`, `group`, `server` and `.comment`.

### 3.1 cluster

Use cluster to define a cluster:

```cdes
cluster CLUSTER_NAME
    .comment Then you can define groups here.
```

Note the usage of `.comment`. It can only be put in other blocks.

In standard coding style, it should be made first.

You can always use `cdesfmt` to format your code.

### 3.2 group

Use group to define a group:

```cdes
cluster CLUSTER_NAME
    group GROUP_NAME
        .comment Then you can define servers here.
```

Generally, it's of the same usage as cluster.

### 3.3 server

Use server to define a server:

```cdes
cluster CLUSTER_NAME
    group GROUP_NAME
        server SERVER_NAME SERVER_ADDRESS
            .comment The following are properties of the server
            key1 value1
            key2 ["this","is","a","list",{"in":"json"}]
            bool true
            int1 123
            int2 232
            obj1 {"this":"is","an":"object"}
```
