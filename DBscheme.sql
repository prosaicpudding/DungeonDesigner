CREATE TABLE Items (
ItemID INT NOT NULL AUTO_INCREMENT,
ItemName VARCHAR(64) NOT NULL UNIQUE,
ItemDescription VARCHAR(500),
ItemUnique BOOLEAN,
ItemValue INT,
PRIMARY KEY(ItemID));

insert into items values(0,"Shabby Clothes",
"These clothes have obviously seen some use. I hope that stain isn\'t blood...",
false,10);
insert into items values(0,"Broken Sword",
"Looks like this sword snapped in half at the hilt. Maybe I can sell it for scrap?",
false,7);
insert into items values(0,"Mysterious Glimmering Orb",
"This shiny orb could be valuable... Or cursed.",
false,35);
insert into items values(0,"Cracked Red Gemstone",
"This gemstone looks like it could be valuable, despite a small crack.",
false,100);

CREATE TABLE Armor (
  ArmorID INT NOT NULL AUTO_INCREMENT,
  ArmorDescription VARCHAR(500) NULL,
  ArmorName VARCHAR(64) NOT NULL,
  ArmorConstitution INT NULL,
  ArmorWillpower INT NULL,
  PRIMARY KEY (ArmorID));
  
insert into armor values(0,
"Sure it has a few holes, but at least it fits more or less.",
"Basic Hide Armor",3,0);

insert into armor values(0,
"These may not be s stylish as your fancy silk robes, 
but they do seem to marginally improve spells nonetheless.",
"Basic Robes",0,3);
  
  CREATE TABLE Weapons (
  WeaponID INT NOT NULL AUTO_INCREMENT,
  WeaponDescription VARCHAR(500) NULL,
  WeaponName VARCHAR(64) NOT NULL,
  WeaponStrength INT NOT NULL,
  WeaponIntelligence INT NOT NULL,
  PRIMARY KEY (WeaponID));
  
insert into weapons values(0,
"Hey look, a big stick! This will probably make my spells stronger.",
"Basic Staff",0,3);
insert into weapons values(0,
"Good for stabbing things.",
"Basic Sword",2,1);
insert into weapons values(0,
"It's a little dull...",
"Basic Dagger",1,2);
insert into weapons values(0,
"Is this just a logging axe? Well, it still looks dangerous enough...",
"Basic Axe",3,0);

  
  CREATE TABLE Rooms (
  RoomID INT NOT NULL AUTO_INCREMENT,
  RoomDescription varchar(500) ,
  isEntryPoint BOOLEAN NOT NULL DEFAULT false,
  RoomCompleteDescription varchar(500),
  PRIMARY KEY (RoomID));
  
  insert into rooms values(0,"The air is smelly, but nothing in here looks too deadly. 
  Perhaps this is a good place for a new adventurer to explore.","false");
  
  CREATE TABLE RoomLeadsTo (
  Room1ID INT NOT NULL,
  Room2ID INT NOT NULL,
  DoorDescription VARCHAR(150) NOT NULL DEFAULT 'A simple door.',
  PRIMARY KEY (Room1ID, Room2ID),

  FOREIGN KEY (Room1ID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE,

  FOREIGN KEY (Room2ID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
  CREATE TABLE Users (
  UserID INT NOT NULL AUTO_INCREMENT,
  Username VARCHAR(64) NOT NULL UNIQUE,
  Password VARCHAR(64) NOT NULL,
  Level INT NOT NULL DEFAULT 1,
  Gold INT NOT NULL DEFAULT 0,
  Health INT NOT NULL,
  Experience INT NOT NULL,
  Location INT DEFAULT NULL,
  Armor INT NOT NULL DEFAULT 0,
  Weapon INT NOT NULL DEFAULT 0,
  Resilience INT NOT NULL DEFAULT 0,
  Perseverance INT NOT NULL DEFAULT 0,
  
  PRIMARY KEY (UserID),
  
  FOREIGN KEY (Armor) REFERENCES Armor (ArmorID)
  ON DELETE RESTRICT ON UPDATE CASCADE,
	
  FOREIGN KEY (Weapon) REFERENCES Weapons (WeaponID)
  ON DELETE RESTRICT ON UPDATE CASCADE,
    
  FOREIGN KEY (Location) REFERENCES Rooms (RoomID));
  
  CREATE TABLE Completed (
  UserID INT NOT NULL,
  RoomID INT NOT NULL,
  PRIMARY KEY (UserID, RoomID),

  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE,

  FOREIGN KEY (RoomID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
  CREATE TABLE RoomMadeBy (
  RoomID INT,
  CreatorID INT,
  PRIMARY KEY(RoomID),
  
  FOREIGN KEY (RoomID) REFERENCES Rooms (RoomID),

  FOREIGN KEY (CreatorID) REFERENCES Users (UserID));
  
  INSERT INTO Rooms VALUES(0,'You are at your party camp. It is a cool night, and the bonfire crackles welcomingly. 
  While here you can trade with the travelling merchant, and switch out moves.');
  UPDATE Rooms SET RoomID=0 WHERE RoomID=1;
  
  INSERT INTO Armor VALUES(0,'Well, it isn\'t glamourous, but at least you\'re not naked...','Starter Rags',0,0);
  UPDATE Armor SET ArmorID=0 WHERE ArmorName='Starter Rags';
  
  INSERT INTO Weapons VALUES(0,'','Bare Fists',0,0);
  UPDATE Weapons SET WeaponID=0 WHERE WeaponName='Bare Fists';
  
  CREATE TABLE ArmorAtCamp(
  UserID INT NOT NULL,
  ArmorID INT NOT NULL,
  Amount INT NOT NULL,
  PRIMARY KEY (UserID, ArmorID),
  
  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE,
  
  FOREIGN KEY (ArmorID) REFERENCES Armor (ArmorID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
  CREATE TABLE WeaponsAtCamp(
  UserID INT NOT NULL,
  WeaponID INT NOT NULL,
  Amount INT NOT NULL,
  PRIMARY KEY (UserID, WeaponID),
  
  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE,
  
  FOREIGN KEY (WeaponID) REFERENCES Weapons (WeaponID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
  CREATE TABLE UserInventory (
  UserID INT NOT NULL,
  ItemID INT NOT NULL,
  Amount INT NOT NULL,
  PRIMARY KEY (UserID, ItemID),

  FOREIGN KEY (ItemID) REFERENCES Items (ItemID)
  ON DELETE CASCADE ON UPDATE CASCADE,
	
  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE);
 
 CREATE TABLE Moves(
  MoveID INT NOT NULL AUTO_INCREMENT,
  MoveName VARCHAR(45) NOT NULL UNIQUE,
  StrengthMultiplier FLOAT NOT NULL,
  IntelligenceMultiplier FLOAT NOT NULL,
  BaseDamage INT NOT NULL,
  PRIMARY KEY (MoveID));
  
  insert into moves values(0,'Kick',.2,0,2);
  insert into moves values(0,'Electrified Strike',.1,.1,2);
  insert into moves values(0,'Fireball',0,.2,2);
  insert into moves values(0,'Wild Swing',2,0,2);
  insert into moves values(0,'Parry and Riposte',.5,.5,4);
  insert into moves values(0,'Greater Fireball',0,2,2);
  insert into moves values(0,'Bite',0,0,1);
  
  CREATE TABLE UserKnowsMove(
  UserID INT NOT NULL,
  MoveID INT NOT NULL,
  PRIMARY KEY (MoveID, UserID),
 
  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE,
 
  FOREIGN KEY (MoveID) REFERENCES Moves (MoveID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
 CREATE TABLE Monsters(
  MonsterID INT NOT NULL AUTO_INCREMENT,
  MonsterName VARCHAR(45) NOT NULL,
  Move1 INT NOT NULL DEFAULT 1,
  Move1Probability TINYINT NOT NULL,
  Move2 INT,
  Move2Probability TINYINT NULL,
  Move3 INT,
  Move3Probability TINYINT,
  PRIMARY KEY (MonsterID),

  FOREIGN KEY (Move1) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE,
  
  FOREIGN KEY (Move2) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE,

  FOREIGN KEY (Move3) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE);
  
  CREATE TABLE MonsterClasses (
  MonsterClassID INT NOT NULL AUTO_INCREMENT,
  MonsterClassName VARCHAR(45) NOT NULL,
  MonsterStrength INT NOT NULL,
  MonsterIntellignece INT NOT NULL,
  MonsterWillpower INT NOT NULL,
  MonsterConstitution INT NOT NULL,
  ClassMadeBy INT,
  ChanceToStrike TINYINT NOT NULL DEFAULT 50,
  PRIMARY KEY (MonsterClassID),

  FOREIGN KEY (ClassMadeBy) REFERENCES Users (UserID)
  ON DELETE SET NULL ON UPDATE CASCADE);
  
  CREATE TABLE RoomHasMonster (
  RoomID INT NOT NULL,
  ClassID INT NOT NULL,
  MonsterID INT NOT NULL,
  Amount INT NOT NULL DEFAULT 1,
  PRIMARY KEY (RoomID, ClassID, MonsterID),

  FOREIGN KEY (RoomID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE,
  
  FOREIGN KEY (MonsterID) REFERENCES Monsters (MonsterID)
  ON DELETE CASCADE ON UPDATE CASCADE,
 
  FOREIGN KEY (ClassID) REFERENCES MonsterClasses (MonsterClassID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
  CREATE TABLE RoomHasItem (
  RoomID INT NOT NULL,
  ItemID INT NOT NULL,
  Amount INT NOT NULL DEFAULT 1,
  PRIMARY KEY (RoomID, ItemID),

  FOREIGN KEY (RoomID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE,
 
  FOREIGN KEY (ItemID) REFERENCES Items (ItemID)
  ON DELETE CASCADE ON UPDATE CASCADE);

  CREATE TABLE MovesEquipped (
  UserID INT NOT NULL UNIQUE,
  Move1ID INT NOT NULL DEFAULT 1,
  Move2ID INT NOT NULL DEFAULT 2,
  Move3ID INT NOT NULL DEFAULT 3,
  
  PRIMARY KEY (UserID),
  
  FOREIGN KEY (Move1ID) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE,
  
  FOREIGN KEY (Move2ID) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE,
  
  FOREIGN KEY (Move3ID) REFERENCES Moves (MoveID)
  ON DELETE RESTRICT ON UPDATE CASCADE);
  
  CREATE TABLE Favorites(
  UserID INT NOT NULL,
  RoomID INT NOT NULL,
  PRIMARY KEY (RoomID, UserID),
 
  FOREIGN KEY (UserID) REFERENCES Users (UserID)
  ON DELETE CASCADE ON UPDATE CASCADE,
 
  FOREIGN KEY (RoomID) REFERENCES Rooms (RoomID)
  ON DELETE CASCADE ON UPDATE CASCADE);
  
CREATE TABLE CurrentInstanceMonsters (
	InstanceID INT NOT NULL AUTO_INCREMENT,
	MonsterID INT NOT NULL,
	ClassID INT NOT NULL,
	Health INT NOT NULL,
	ChallengeRating INT NOT NULL,
	Location INT NOT NULL,
	UserID INT NOT NULL,
	PRIMARY KEY (InstanceID),
	
	FOREIGN KEY (UserID) REFERENCES Users (UserID)
	ON DELETE CASCADE ON UPDATE CASCADE,
	
	FOREIGN KEY (MonsterID) REFERENCES Monsters (MonsterID)
    ON DELETE CASCADE ON UPDATE RESTRICT,
 
    FOREIGN KEY (ClassID) REFERENCES MonsterClasses (MonsterClassID)
    ON DELETE CASCADE ON UPDATE RESTRICT,
	
	FOREIGN KEY (Location) REFERENCES Rooms (RoomID)
  );
  CREATE TABLE CurrentInstanceloot (
	ItemID INT NOT NULL,
	Location INT NOT NULL,
	UserID INT NOT NULL,
	Amount INT NOT NULL DEFAULT 1,
	PRIMARY KEY(ItemID, Location, UserID),
	
	FOREIGN KEY (UserID) REFERENCES Users (UserID)
	ON DELETE CASCADE ON UPDATE CASCADE,

	FOREIGN KEY (ItemID) REFERENCES Items (ItemID)
	ON DELETE CASCADE ON UPDATE CASCADE,
	
	FOREIGN KEY (Location) REFERENCES Rooms (RoomID)
	);
  
  insert into weaponsatcamp values(3,1,5);
  insert into weaponsatcamp values(1,3,1);
  insert into weaponsatcamp values(2,2,2);
  insert into weaponsatcamp values(2,3,1);
  insert into weaponsatcamp values(2,1,4);

  insert into armoratcamp values(2,2,1);
  insert into armoratcamp values(2,1,2);

  insert into userinventory values(2,3,2);
  insert into userinventory values(2,1,1);
  insert into roommadeby values(2,2);