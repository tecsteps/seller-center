## About the project

This repository represents a new open source Seller Center. A Seller Center is a SaaS application that runs side-by-side to the online shop and is integrated via API. It allows sellers to manage their products, stocks, and prices, while also processing orders from the shop. If you’ve ever sold something on platforms like Amazon or eBay, you’re already familiar with the concept.

Despite its importance for every marketplace, most existing e-commerce platforms lack this functionality, leaving shop operators to rely on expensive commercial solutions like Mirakl, Marketplacer or Arcadier.

Surprisingly, there’s no strong open-source alternative available...

This project is built in public and will be free to use. I will regularly document my progress from the initial implementation to the release of the first version of the project. I aim to share insights, challenges, and solutions along the way, providing a transparent view of building an open-source Seller Center. Stay tuned as I transform this idea into a reality.

Follow me me on LinkedIn to get updates: 
- https://www.linkedin.com/in/fabian-wesner/

or find a list of all articles here: https://github.com/FabianWesner/seller-center/wiki

## Install Guide

This is a regular Laravel & FilamentPHP application. You can install it like any other Laravel application. Just clone the repository and run `composer run dev`.

URLs
* Shop Operators: http://localhost:8000/operator/ (Use `operator@tecsteps.com` as username and password)
* Sellers: http://localhost:8000/seller/ (Use `seller@tecsteps.com` as username and password)

## User Guide

There is no fully blown documentation yet. The idea is pretty simple:

* Shop operators can register and configure their shop (set categories, currencies, product-types, manage their sellers, etc.)
* Sellers can also register and apply for a shop. Then they can import their products, manage stocks, prices, etc.

The Seller Center is a multi-tenant application. Operators and Sellers are tenants. Each tenant has its own data and set of users (with permissions at some point).

## Imprint / Impressum

https://github.com/tecsteps/ossc/wiki/Imprint-Impressum