#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: bateau
#------------------------------------------------------------

CREATE TABLE bateau(
        MMSI       Varchar (9) NOT NULL ,
        VesselName Varchar (32) NOT NULL ,
        Length     Float NOT NULL ,
        Width      Float NOT NULL ,
        Draft      Float NOT NULL
	,CONSTRAINT bateau_PK PRIMARY KEY (MMSI)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: etat
#------------------------------------------------------------

CREATE TABLE etat(
        id   Int  Auto_increment  NOT NULL ,
        etat Int NOT NULL,
	nom Varchar (100) NOT NULL
	,CONSTRAINT etat_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: position
#------------------------------------------------------------

CREATE TABLE position (
        id           Int  Auto_increment  NOT NULL ,
        BaseDateTime Datetime NOT NULL ,
        LAT          Double NOT NULL ,
        LON          Double NOT NULL ,
        SOG          Float NOT NULL ,
        COG          Float NOT NULL ,
        Heading      Float NOT NULL ,
        Etat         Int NOT NULL ,
        MMSI         Varchar (9) NOT NULL ,
	CONSTRAINT position_PK PRIMARY KEY (id)

	,CONSTRAINT position_bateau_FK FOREIGN KEY (MMSI) REFERENCES bateau(MMSI)
)ENGINE=InnoDB;

