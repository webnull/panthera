Deployment - Create
===================

## Creating Classes

Use `deploy Create/Class A/B/C` to create a class "C" in /Classes/A/B/
Directories are created automatically.
Tool generates also namespace and example code.

Template can be modified in "Schema/CodeGenerator/Class.phps" file.

[![asciicast](https://asciinema.org/a/ec5mip3t4l3ymutg1wcj6asvq.png)](https://asciinema.org/a/ec5mip3t4l3ymutg1wcj6asvq)

## Creating packages and controllers

To create a package "Test":

```bash
deploy Create/Package Test
```

Creating a "MyTestController" controller inside of a "Test" package previously created:

```bash
deploy Create/Controller MyTestController --package Test --route /example,[:myVariable]
```

