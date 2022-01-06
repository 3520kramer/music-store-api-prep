-- get track query
select track.TrackId as trackId, track.Name as trackTitle, track.Composer as trackComposer, track.Milliseconds as trackTime, 
	track.Bytes as trackSize, track.UnitPrice as trackPrice, genre.name as trackGenre, mediatype.Name as trackMediaType,
	album.AlbumId as albumId, album.Title as albumName, artist.ArtistId as artistId, artist.Name as artistName
from track
join album using(AlbumId)
join artist using(ArtistId)
join genre using(GenreId)
join mediatype using(MediaTypeId)

-- get album query
SELECT track.TrackId AS trackId, track.Name AS trackTitle, 
  track.Composer AS trackComposer, track.Milliseconds AS trackTime, 
  track.Bytes AS trackSize, track.UnitPrice AS trackPrice, 
  genre.name AS trackGenre, mediatype.Name AS trackMediaType,
  album.AlbumId AS albumId, album.Title AS albumName, 
  artist.ArtistId AS artistId, artist.Name AS artistName
FROM track
JOIN album USING(AlbumId)
JOIN artist USING(ArtistId)
JOIN genre USING(GenreId)
JOIN mediatype USING(MediaTypeId)
WHERE albumId = 1

-- Search query
select track.TrackId as 'id', track.Name as 'trackName', artist.Name as 'artistName', album.Title as 'albumName', 'track' as type 
from track
join album using(AlbumId)
join artist using(ArtistId) 
where track.Name like 'metal%'
UNION
select artist.ArtistId as 'id', null as 'trackName', artist.Name as 'artistName', null as 'albumName', 'artist' as type 
from artist 
where artist.Name like 'metal%'
UNION
select album.AlbumId as 'id', null as 'trackName', artist.Name as 'artistName', album.Title as 'albumName', 'album' as type 
from album 
join artist using(ArtistId)
where album.Title like 'metal%';

-- Create invoice/order
BEGIN;
INSERT INTO `chinook_abridged`.`invoice`
	(`CustomerId`, `InvoiceDate`, `BillingAddress`, `BillingCity`,
		`BillingState`, `BillingCountry`, `BillingPostalCode`, `Total`)
VALUES
(1, NOW(), 'address', 'city', 'state','country',
1234, 10);

SELECT @order_id := LAST_INSERT_ID();

INSERT INTO `chinook_abridged`.`invoiceline`
(`InvoiceId`, `TrackId`, `UnitPrice`, `Quantity`)
VALUES
(@order_id, 1, 0.99, 1);
COMMIT;