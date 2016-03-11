# miudemo-php
A complete MVC framework implementation with code sample.

This MVC framework was created as both a learning experience of the language and to accomplish several complex requirements of an old PHP project. At the time, frameworks were lacking in the following areas:

- Easy way to integrate SQL queries into the code without ORMs or query code inside controllers.
- Good view templating systems.
- Annotation-style security.

For these reasons the framework was developped. And even though the landscape has likely changed since then, it was still a valuable learning experience.

There is four main components:

- Database engine: users write SQL queries manually in configuration files, which can then be accessed anywhere.
- Routing engine: handles all the routing process in a simple, straightforward manner.
- Stack controller execution: controller methods are constructors which create a stack of methods to execute in order. This allows a security system to completely override the rest of the code and deny access. Woks similarly to C# attributes, albeit more verbose.
- Object View system: java inspired object model for webpage development. Developers create components with only the necessary html, css and javascript files and a constructor handler. Pages can be created without a single line of html by using the components.

The framework code is in miu-framework, while the sample code is actmonitor. To see how the framework is used just look at the sample. The implementation is very straightforward. Everything was programmed using the latest coding standards for php as of 2013.
