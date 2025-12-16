CREATE TABLE CATEGORIA (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nom_categoria VARCHAR(25) NOT NULL
);

INSERT INTO CATEGORIA (nom_categoria) VALUES ('Alimentação'), ('Vestuário'), ('Serviços'), ('Lazer');

CREATE TABLE ASSOCIADO (
    cpf_associado VARCHAR(14) PRIMARY KEY, 
    nom_associado VARCHAR(40) NOT NULL,
    dtn_associado DATE,
    end_associado VARCHAR(40),
    bai_associado VARCHAR(30),
    cep_associado VARCHAR(8),
    cid_associado VARCHAR(40),
    uf_associado CHAR(2),
    cel_associado VARCHAR(15),
    email_associado VARCHAR(50) NOT NULL UNIQUE,
    sen_associado VARCHAR(255) NOT NULL 

CREATE TABLE COMERCIO (
    cnpj_comercio VARCHAR(18) PRIMARY KEY, 
    id_categoria INT NOT NULL,
    raz_social_comercio VARCHAR(50),
    nom_fantasia_comercio VARCHAR(30),
    end_comercio VARCHAR(40),
    bai_comercio VARCHAR(30),
    cep_comercio VARCHAR(8),
    cid_comercio VARCHAR(40),
    uf_comercio CHAR(2),
    con_comercio VARCHAR(15), 
    email_comercio VARCHAR(50) NOT NULL UNIQUE,
    sen_comercio VARCHAR(255) NOT NULL, 
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIA(id_categoria)
);

CREATE TABLE CUPOM (
    num_cupom CHAR(12) PRIMARY KEY, 
    tit_cupom VARCHAR(25) NOT NULL,
    cnpj_comercio VARCHAR(18) NOT NULL,
    dta_emissao_cupom DATE NOT NULL,
    dta_inicio_cupom DATE NOT NULL,
    dta_termino_cupom DATE NOT NULL,
    per_desc_cupom DECIMAL(5,2) NOT NULL, 
    FOREIGN KEY (cnpj_comercio) REFERENCES COMERCIO(cnpj_comercio)
);

CREATE TABLE CUPOM_ASSOCIADO (
    id_cupom_associado INT AUTO_INCREMENT PRIMARY KEY,
    num_cupom CHAR(12) NOT NULL,
    cpf_associado VARCHAR(14) NOT NULL,
    dta_cupom_associado DATE NOT NULL, 
    dta_uso_cupom_associado DATE, 
    FOREIGN KEY (num_cupom) REFERENCES CUPOM(num_cupom),
    FOREIGN KEY (cpf_associado) REFERENCES ASSOCIADO(cpf_associado)
);

CREATE TABLE RECUPERACAO_SENHA (
    email_usuario VARCHAR(50) NOT NULL,
    token VARCHAR(100) NOT NULL,
    data_expiracao DATETIME NOT NULL,
    PRIMARY KEY (token)
);