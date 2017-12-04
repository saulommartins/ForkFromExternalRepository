/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* VersÃ£o 1.91.5
*/
CREATE SEQUENCE tcepb.nota_fiscal_nro_sequencial_seq;

CREATE TABLE tcepb.nota_fiscal (
    cod_nota                INTEGER     NOT NULL,
    cod_nota_liquidacao     INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    nro_nota                INTEGER     NOT NULL,
    nro_serie               VARCHAR     NOT NULL,
    data_emissao            DATE        NOT NULL,
    nro_sequencial          INTEGER     NOT NULL DEFAULT nextval('tcepb.nota_fiscal_nro_sequencial_seq'),
    CONSTRAINT pk_nota_fiscal           PRIMARY KEY                  (cod_nota,cod_nota_liquidacao,cod_entidade,exercicio),
    CONSTRAINT fk_nota_fiscal_nota_liquidacao_2 FOREIGN KEY (cod_nota_liquidacao,cod_entidade,exercicio) REFERENCES empenho.nota_liquidacao (cod_nota,cod_entidade,exercicio)

);

GRANT ALL ON tcepb.nota_fiscal          TO GROUP urbem;


INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2471
          , 365
          , 'FMManterNotasFiscais.php'
          , 'incluir'
          , 20
          , 'Unidade Gestora'
          , 'Incluir Notas Fiscais'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2472
          , 365
          , 'FLManterNotasFiscais.php'
          , 'alterar'
          , 22
          , 'Unidade Gestora'
          , 'Alterar Notas Fiscais'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2473
          , 365
          , 'FLManterNotasFiscais.php'
          , 'excluir'
          , 22
          , 'Unidade Gestora'
          , 'Excluir Notas Fiscais'
          );

----------------
-- Ticket #14637
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2479
          , 365
          , 'FMManterFontePagadoraContaBancaria.php'
          , 'configurar'
          , 81
          , ''
          , 'Relacionar Conta Bancária com Fonte Pagadora'
          );

    CREATE FUNCTION fn_atualiza_tcepb() RETURNS BOOLEAN AS $$
    DECLARE
        boExists BOOLEAN := FALSE;
    
    BEGIN
        SELECT true
          INTO boExists
          FROM pg_class
    INNER JOIN pg_attribute
            ON pg_attribute.attrelid = pg_class.oid
         WHERE pg_class.relname = 'tipo_origem_recurso'
           AND pg_attribute.attname = 'exercicio'
           AND pg_attribute.attnum > 0;

    IF(boExists IS NULL) THEN
        boExists := FALSE;
    END IF;

    IF(boExists IS FALSE) THEN
        ALTER TABLE tcepb.recurso DROP CONSTRAINT fk_recurso_2;
        ALTER TABLE tcepb.tipo_origem_recurso DROP CONSTRAINT pk_tipo_origem_recurso;
        ALTER TABLE tcepb.tipo_origem_recurso ADD COLUMN exercicio CHAR(4) NULL;
        UPDATE tcepb.tipo_origem_recurso SET exercicio = '2009';
        ALTER TABLE tcepb.tipo_origem_recurso ADD CONSTRAINT pk_tipo_origem_recurso PRIMARY KEY (cod_tipo, exercicio);
        ALTER TABLE tcepb.tipo_origem_recurso ADD CONSTRAINT fk_recurso_1  FOREIGN KEY (cod_tipo, exercicio) REFERENCES tcepb.tipo_origem_recurso(cod_tipo, exercicio);
    END IF;

    RETURN boExists;    

    END;
    $$
    LANGUAGE 'plpgsql';

    SELECT fn_atualiza_tcepb();

    DROP FUNCTION fn_atualiza_tcepb();


CREATE TABLE tcepb.relacao_conta_corrente_fonte_pagadora (
    cod_banco                     INTEGER                   NOT NULL, 
    cod_agencia                   INTEGER                   NOT NULL,
    cod_conta_corrente            INTEGER                   NOT NULL,
    cod_tipo                      INTEGER                   NOT NULL,
    exercicio                     CHAR(4)                   NOT NULL,
    CONSTRAINT pk_relacao_conta_corrente_fonte_pagadora     PRIMARY KEY                             (cod_banco, cod_agencia, cod_conta_corrente, cod_tipo, exercicio),
    CONSTRAINT fk_relacao_conta_corrente_fonte_pagadora_1   FOREIGN KEY                             (cod_banco, cod_agencia, cod_conta_corrente)
                                                            REFERENCES monetario.conta_corrente     (cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_relacao_conta_corrente_fonte_pagadora_2   FOREIGN KEY                             (cod_tipo, exercicio)
                                                            REFERENCES tcepb.tipo_origem_recurso    (cod_tipo, exercicio)
); 

GRANT ALL ON tcepb.relacao_conta_corrente_fonte_pagadora TO GROUP urbem;

----------------
-- Ticket #14736
----------------
INSERT INTO tcepb.elemento_tribunal VALUES('01.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('03.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('04.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('05.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('06.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('07.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('08.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('09.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('10.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('11.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('12.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('13.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('14.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('15.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('16.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('17.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('18.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('19.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('20.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('21.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('22.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('23.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('24.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('25.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('26.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('27.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('28.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.01','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.02','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.03','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.04','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.05','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.06','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.07','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.08','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.09','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.10','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.11','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.12','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.13','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.14','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.15','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.16','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.17','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.18','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('30.19','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('32.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('33.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('34.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('35.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.20','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.21','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.22','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.23','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.24','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.25','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.26','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.27','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.28','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.29','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.30','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.31','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.32','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.33','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.34','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.35','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.36','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.37','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('36.38','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('37.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('38.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.39','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.40','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.41','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.42','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.43','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.44','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.45','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.46','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.47','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.48','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.49','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.50','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.51','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.52','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.53','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.54','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.55','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.56','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.57','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.58','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.59','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.60','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('39.61','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('41.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('42.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('43.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('45.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('46.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('47.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('48.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('49.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('51.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.62','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.63','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.64','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.65','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.66','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.67','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.68','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.69','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.70','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.71','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.72','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.73','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.74','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.75','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.76','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.77','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.78','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.79','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('52.80','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('61.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('62.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('63.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('64.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('65.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('66.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('67.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('71.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('72.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('73.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('74.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('75.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('76.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('77.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('81.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('91.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('92.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('93.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('94.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('95.99','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('96.99','2009');

----------------
-- Ticket #14771
----------------
INSERT INTO tcepb.elemento_tribunal VALUES('92.81','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('92.82','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('92.83','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('92.84','2009');
INSERT INTO tcepb.elemento_tribunal VALUES('92.85','2009');

ALTER TABLE tcepb.elemento_tribunal ADD COLUMN descricao VARCHAR(90);

UPDATE tcepb.elemento_tribunal SET descricao = 'SEM SUBELEMENTO'  WHERE exercicio = '2009';

UPDATE tcepb.elemento_tribunal SET descricao = 'COMBUSTÍVEIS E LUBRIFICANTES AUTOMOTIVOS'                                          WHERE estrutural = '30.01' AND exercicio = '2009'; 
UPDATE tcepb.elemento_tribunal SET descricao = 'GÊNEROS DE ALIMENTAÇÃO'                                                            WHERE estrutural = '30.02' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL FARMACOLÓGICO'                                                            WHERE estrutural = '30.03' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL ODONTOLÓGICO'                                                             WHERE estrutural = '30.04' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL EDUCATIVO E ESPORTIVO'                                                    WHERE estrutural = '30.05' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL PARA FESTIVIDADES E HOMENAGENS'                                           WHERE estrutural = '30.06' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL DE EXPEDIENTE'                                                            WHERE estrutural = '30.07' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL DE PROCESSAMENTO DE DADOS'                                                WHERE estrutural = '30.08' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'UNIFORMES, TECIDOS E AVIAMENTOS'                                                   WHERE estrutural = '30.09' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL PARA MANUTENÇÃO DE BENS IMÓVEIS'                                          WHERE estrutural = '30.10' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL PARA MANUTENÇÃO DE BENS MÓVEIS'                                           WHERE estrutural = '30.11' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL HOSPITALAR'                                                               WHERE estrutural = '30.12' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL PARA MANUTENÇÃO DE VEÍCULOS'                                              WHERE estrutural = '30.13' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL PARA REABILITAÇÃO PROFISSIONAL'                                           WHERE estrutural = '30.14' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL BIBLIOGRÁFICO NÃO IMOBILIZÁVEL'                                           WHERE estrutural = '30.15' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'AQUISIÇÃO DE SOFTWARES DE BASE'                                                    WHERE estrutural = '30.16' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'BILHETES DE PASSAGEM'                                                              WHERE estrutural = '30.17' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MATERIAL DE CONSUMO - PAGTO ANTECIPADO'                                            WHERE estrutural = '30.18' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS MATERIAIS DE CONSUMO'                                                       WHERE estrutural = '30.19' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS TÉCNICOS PROFISSIONAIS'                                                   WHERE estrutural = '36.20' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'ESTAGIÁRIOS'                                                                       WHERE estrutural = '36.21' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'BOLSA DE INICIAÇÃO AO TRABALHO'                                                    WHERE estrutural = '36.22' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'PRÓ-LABORE A CONSULTORES EVENTUAIS'                                                WHERE estrutural = '36.23' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'CONFERÊNCIAS E EXPOSIÇÕES'                                                         WHERE estrutural = '36.24' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LOCAÇÃO DE IMÓVEIS'                                                                WHERE estrutural = '36.25' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LOCAÇÃO DE BENS MÓVEIS E INTANGÍVEIS'                                              WHERE estrutural = '36.26' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE EQUIPAMENTOS'                                          WHERE estrutural = '36.27' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE VEÍCULOS'                                              WHERE estrutural = '36.28' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE BENS MÓVEIS DE OUTRAS NATUREZAS'                       WHERE estrutural = '36.29' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE BENS IMÓVEIS'                                          WHERE estrutural = '36.30' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'FORNECIMENTO DE ALIMENTAÇÃO'                                                       WHERE estrutural = '36.31' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE LIMPEZA E CONSERVAÇÃO'                                                 WHERE estrutural = '36.32' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE COMUNICAÇÃO EM GERAL'                                                  WHERE estrutural = '36.33' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS MÉDICOS E ODONTOLÓGICOS'                                                  WHERE estrutural = '36.34' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇO DE APOIO ADMINISTRATIVO, TÉCNICO E OPERACIONAL'                            WHERE estrutural = '36.35' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE ÁUDIO, VÍDEO E FOTO'                                                   WHERE estrutural = '36.36' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS SERVIÇOS DE TERCEIROS PFPAGTO ANTECIPADO'                                   WHERE estrutural = '36.37' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS SERVIÇOS DE PESSOA FÍSICA'                                                  WHERE estrutural = '36.38' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'ASSINATURAS DE PERIÓDICOS E ANUIDADES'                                             WHERE estrutural = '39.39' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO DE SOFTWARE'                                                            WHERE estrutural = '39.40' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LOCAÇÃO DE IMÓVEIS'                                                                WHERE estrutural = '39.41' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LOCAÇÃO DE SOFTWARES'                                                              WHERE estrutural = '39.42' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LOCAÇÃO DE MÁQUINAS E EQUIPAMENTOS'                                                WHERE estrutural = '39.43' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO. DE BENS IMÓVEIS'                                         WHERE estrutural = '39.44' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE MÁQUINAS E EQUIPAMENTOS'                               WHERE estrutural = '39.45' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE VEÍCULOS'                                              WHERE estrutural = '39.46' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MANUTENÇÃO E CONSERVAÇÃO DE ESTRADAS E VIAS'                                       WHERE estrutural = '39.47' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'FESTIVIDADES E HOMENAGENS'                                                         WHERE estrutural = '39.48' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE ENERGIA ELÉTRICA'                                                      WHERE estrutural = '39.49' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE ÁGUA E ESGOTO'                                                         WHERE estrutural = '39.50' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇO MÉDICO, HOSPITAL, ODONTOLÓGICO E LABORATORIAIS'                            WHERE estrutural = '39.51' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE ASSISTÊNCIA SOCIAL'                                                    WHERE estrutural = '39.52' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE TELECOMUNICAÇÕES'                                                      WHERE estrutural = '39.53' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE APOIO AO ENSINO'                                                       WHERE estrutural = '39.54' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'VIGILÂNCIA OSTENSIVA'                                                              WHERE estrutural = '39.55' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'LIMPEZA E CONSERVAÇÃO'                                                             WHERE estrutural = '39.56' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS BANCÁRIOS'                                                                WHERE estrutural = '39.57' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SERVIÇOS DE PUBLICIDADE E PROPAGANDA'                                              WHERE estrutural = '39.58' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'AQUISIÇÃO DE SOFTWARES DE APLICAÇÃO'                                               WHERE estrutural = '39.59' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS SERVIÇOS DE TERCEIROS PJ- PAGTO ANTECIPADO'                                 WHERE estrutural = '39.60' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS SERVIÇOS DE TERCEIROS, PESSOA JURÍDICA'                                     WHERE estrutural = '39.61' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'APARELHOS E EQUIPAMENTOS DE COMUNICAÇÃO'                                           WHERE estrutural = '52.62' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'APARELHOS, EQUIPAMENTOS, UTENSÍLIOS MÉDICOODONTOLÓGICO, LABORATORIAL E HOSPITALAR' WHERE estrutural = '52.63' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'APARELHOS E UTENSÍLIOS DOMÉSTICOS'                                                 WHERE estrutural = '52.64' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'COLEÇÕES E MATERIAIS BIBLIOGRÁFICOS'                                               WHERE estrutural = '52.65' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'INSTRUMENTOS MUSICAIS E ARTÍSTICOS'                                                WHERE estrutural = '52.66' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MÁQUINAS E EQUIPAM. DE NATUREZA INDUSTRIAL'                                        WHERE estrutural = '52.67' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MÁQUINAS E EQUIPAMENTOS ENERGÉTICOS'                                               WHERE estrutural = '52.68' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MÁQUINAS, UTENSÍLIOS E EQUIPAMENTOS DIVERSOS'                                      WHERE estrutural = '52.69' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'EQUIPAMENTOS DE PROCESSAMENTO DE DADOS'                                            WHERE estrutural = '52.70' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'EQUIPAMENTOS E UTENSÍLIOS HIDRÁULICOS E ELÉTRICOS'                                 WHERE estrutural = '52.71' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MÁQUINAS E EQUIPAMENTOS AGRÍCOLAS E RODOVIÁRIOS'                                   WHERE estrutural = '52.72' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'MOBILIÁRIO EM GERAL'                                                               WHERE estrutural = '52.73' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OBRAS DE ARTE E PEÇAS PARA MUSEU'                                                  WHERE estrutural = '52.74' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'SEMOVENTES E EQUIPAMENTOS DE MONTARIA'                                             WHERE estrutural = '52.75' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'PEÇAS NÃO INCORPORÁVEIS A IMÓVEIS'                                                 WHERE estrutural = '52.76' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'VEÍCULOS DE TRAÇÃO MECÂNICA'                                                       WHERE estrutural = '52.77' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'ACESSÓRIOS PARA AUTOMÓVEIS'                                                        WHERE estrutural = '52.78' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'EQUIPAMENTOS E SISTEMA DE PROTEÇÃO E VIGILÂNCIA AMBIENTAL'                         WHERE estrutural = '52.79' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTROS MATERIAIS PERMANENTES'                                                      WHERE estrutural = '52.80' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'VENCIMENTOS E VANTAGENS FIXAS'                                                     WHERE estrutural = '92.81' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OBRIGAÇÕES PATRONAIS'                                                              WHERE estrutural = '92.82' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'CONTRATAÇÃO POR TEMPO DETERMINADO'                                                 WHERE estrutural = '92.83' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTRAS DESPESAS VARIÁVEIS DE PESSOAL'                                              WHERE estrutural = '92.84' AND exercicio = '2009';
UPDATE tcepb.elemento_tribunal SET descricao = 'OUTRAS DESPESAS DE PESSOAL DECORRENTE DE CONTRATAÇÃO'                              WHERE estrutural = '92.85' AND exercicio = '2009';

----------------
--Ticket #14576
----------------

CREATE TYPE tipoBanco as (
    banco    varchar,
    agencia  varchar,
    corrente varchar
);


CREATE TYPE colunasExportaFolhaPagamento as (
            cod_contrato   integer,
            cod_orgao      integer,
            matricula      varchar,
            dt_pagamento   varchar,
            dt_competencia varchar,
            agencia        varchar,
            banco          varchar,
            conta_corrente varchar,
            evento         varchar,
            codigo         varchar,
            valor          NUMERIC,
            natureza       varchar,
            tipo_folha     varchar,
            observacao     varchar,
            array_banco    varchar,
            irrf           varchar
);

