create table Estado (
    ID_estado int PRIMARY KEY,
    Orden int,
    Nombre char(60)
);
 
create table Usuario (
    ID_usuario int PRIMARY KEY,
    Nombre char(50),
    Usuario char(50),
    Contraseña char(50)
);
 
create table Origen (
    ID_origen int PRIMARY KEY,
    Ciudad char(100),
    Cobertura char(100)
);
 
create table Destino (
    ID_destino int PRIMARY KEY,
    Ciudad char(100),
    Cobertura char(100)
);
 
create table Tienda (
    ID_tienda int PRIMARY KEY,
    No_orden int,
    Nombre char(150),
    Host char(255)
);
 
create table Cabeceras (
    ID_cabecera int PRIMARY KEY,
    costo_envio decimal(10,2),
    costo_manejo decimal(10,2),
    ID_origen int NOT NULL,
    ID_destino int NOT NULL,
    FOREIGN KEY (ID_origen) REFERENCES Origen(ID_origen),
    FOREIGN KEY (ID_destino) REFERENCES Destino(ID_destino)
);
 
create table Envio (
    No_guia char(20) PRIMARY KEY,
    Fecha DATE,
    Hora TIME,
    Costo_total decimal(10,2),
    Destinatario char(150),
    Fecha_entrega DATE,
    ID_estado int NOT NULL,
    ID_cabecera int NOT NULL,
    ID_tienda int NOT NULL,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado),
    FOREIGN KEY (ID_cabecera) REFERENCES Cabeceras(ID_cabecera),
    FOREIGN KEY (ID_tienda) REFERENCES Tienda(ID_tienda)
);
 
create table Seguimiento (
    ID_seguimiento int PRIMARY KEY,
    Observacion char(200),
    Hora TIME,
    Fecha DATE,
    No_guia char(20) NOT NULL,
    ID_estado int NOT NULL,
    ID_usuario int NOT NULL,
    FOREIGN KEY (No_guia) REFERENCES Envio(No_guia) ON DELETE CASCADE,
    FOREIGN KEY (ID_estado) REFERENCES Estado(ID_estado),
    FOREIGN KEY (ID_usuario) REFERENCES Usuario(ID_usuario)
);