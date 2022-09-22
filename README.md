# wwwBiblioteka

Refactor Ideas

- further adjust folders structure (public, config, tests - with split to unit, integration)
- further adjustment of tests folder structure (as in MyDram... folders) and namespace = src namespace
- ISBN API translate image to isbn // maybe later as this comes with lot of data privacy concerns (what user will photo, metadata of picture such as location and fact that I would need to use some external api to translate photo to barcode/isbn)
- Unit testing - write mocks etc. for low level classes, do I necessarily need dependency injections?.
- Unit testing - write unit tests missing for low level classes
- Unit testing - thing how to and if needed write unit tests for high level classes (router)
- make uml diagrams (class and one showing how classes run methods)
- MCV - adjust current Controller to Router
- MVC - write microcontrollers / call them this way / explore the idea
- FE/Catalog: Nice looking dedicated error page
- Factories, Collections and Dependency injection? (e.g. for DataAccess in Catalog business classes)
- Where to validate user inputs in backend, business classes, router, controller, data access? I think in Business class format and in Router/Controller if they were set at all; Then in Business Class throw Validator Exception and in Router Catch It; Remove from Data Access anyway
- Interface for DataAccess?
- More interfaces?
- Patterns - consider using static class/methods Factory to create different objects (e.g. User, UserLookup etc. could be part of Factory)
- Patterns - draw UML diagrams for different areas, review if properly implemented (e.g. many code duplication in catalog classes and data access classes)
- Patterns - consider using Interfaces and then Abstraction based on the Interfaces
- Exceptions - review used exception classes, adjust
- Exceptions - think where to capture what type of exceptions (MVC/Routing)
- Review variable namings (use general attribute names in commonly used functions, specific names for specific purposes)
- Review catalog structure (e.g. no classes in main class catalog)
- Consider general framework classes (like Collection or CollectionIterator)
- Review PHP installation (put all in PHP folder incl. PHPUnit and local Composer installation, create public folder readable for user)
- Use dynamic folder assignments
- Use constants/application parameters where comments today (consider the fastest access - constant or static class or else)
- Patterns - should I better design Catalog Title class/abstract/interface (and supporting classes) so they can be reused for other type of titles?
- Database - consider deleting Authors, Categories, Publishers on DB level (after change on those or title tables, to remove unused records)
- Better Error Handling (when catching exception it may be required to log an error + handle errors by logging (if not by default php) and redirecting user to some base page/plain html)?