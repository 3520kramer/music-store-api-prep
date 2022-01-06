# Music Store API Documentation

# Auth


**URL** : `music-store-api/auth/customer`

**Description** : Used to collect a Token for a registered Customer.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/auth/Admin`

**Description** : Used to collect a Token for the admin.

**Method** : `GET`

**Auth required** : NO

#
# Artists

**URL** : `music-store-api/artists/{id}`

**Description** : Get a artist by id.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/artists`

**Description** : Create a new artist.

**Method** : `POST`

**Auth required** : YES - Admin

**Body** : 

```FORM
Name
```

--

**URL** : `music-store-api/artists/{id}`

**Description** : Update a artist.

**Method** : `PUT`

**Auth required** : YES - Admin

**Body** : 
```FORM
Name, ArtistId
```
--

**URL** : `music-store-api/artists/{id}`

**Description** : Delete a artist by id.

**Method** : `DELETE`

**Auth required** : NO

--

**URL** : `music-store-api/artists`

**Description** : Get all artists.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/artists/{id}/albums`

**Description** : Get all albums related to an artist.

**Method** : `GET`

**Auth required** : NO

#
# Albums

**URL** : `music-store-api/albums/{id}`

**Description** : Get a album by id.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/albums`

**Description** : Creates a new album.

**Method** : `POST`

**Auth required** : YES - Admin

**Body** : 

```FORM
Title, ArtistId
```

--

**URL** : `music-store-api/albums/{id}`

**Description** : Update an album.

**Method** : `PUT`

**Auth required** : YES - Admin

**Body** : 
```FORM
AlbumId, Title, ArtistId
```
--

**URL** : `music-store-api/album/{id}`

**Description** : Delete am album by id.

**Method** : `DELETE`

**Auth required** : NO

--

**URL** : `music-store-api/albums`

**Description** : Get all albums.

**Method** : `GET`

**Auth required** : NO


#
# Customer

**URL** : `music-store-api/customers/{id}`

**Description** : Get a customer by id.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/customers`

**Description** : Creates a new customer.

**Method** : `POST`

**Auth required** : NO

**Body** : 

```FORM
FirstName, LastName, Company, Address, City, FirstName, State, Country, PostalCode, Phone, Fax, Email

```

--

**URL** : `music-store-api/customers/{id}`

**Description** : Update an album.

**Method** : `PUT`

**Auth required** : YES - Customer and Admin

**Body** : 
```FORM
CustomerId, FirstName, LastName, Company, Address, City, FirstName, State, Country, PostalCode, Phone, Fax, Email
```
--

**URL** : `music-store-api/customers/{id}`

**Description** : Delete a customer by id.

**Method** : `DELETE`

**Auth required** : NO

--

**URL** : `music-store-api/customers`

**Description** : Get all customers.

**Method** : `GET`

**Auth required** : NO

--

**URL** : `music-store-api/customers/{id}/invoices{id}`

**Description** : Get a specific invoice for a customer.

**Method** : `GET`

**Auth required** : NO

