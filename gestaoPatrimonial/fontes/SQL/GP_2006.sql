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
* $Id:$
*
* Versão 2.00.6
*/

----------------
-- Ticket #18354
----------------

ALTER TABLE licitacao.contrato_licitacao DROP  CONSTRAINT fk_contrato_licitacao_2;
ALTER TABLE licitacao.contrato_licitacao ADD   COLUMN     exercicio_licitacao CHAR(4);
UPDATE      licitacao.contrato_licitacao SET              exercicio_licitacao = exercicio;
ALTER TABLE licitacao.contrato_licitacao ALTER COLUMN     exercicio_licitacao SET NOT NULL;
ALTER TABLE licitacao.contrato_licitacao ADD   CONSTRAINT fk_contrato_licitacao_2  FOREIGN KEY                   (cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao)
                                                                                   REFERENCES licitacao.licitacao(cod_licitacao, cod_modalidade, cod_entidade, exercicio);

ALTER TABLE licitacao.contrato_compra_direta DROP  CONSTRAINT fk_contrato_compra_direta_2;
ALTER TABLE licitacao.contrato_compra_direta ADD   COLUMN     exercicio_compra_direta CHAR(4);
UPDATE      licitacao.contrato_compra_direta SET              exercicio_compra_direta = exercicio;
ALTER TABLE licitacao.contrato_compra_direta ALTER COLUMN     exercicio_compra_direta SET NOT NULL;
ALTER TABLE licitacao.contrato_compra_direta ADD   CONSTRAINT fk_contrato_compra_direta_2 FOREIGN KEY                     (cod_compra_direta, cod_entidade, exercicio_compra_direta, cod_modalidade)
                                                                                          REFERENCES compras.compra_direta(cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade);


----------------
-- Ticket #18365
----------------

CREATE TABLE licitacao.tipo_contrato (
    cod_tipo        INTEGER         NOT NULL,
    sigla           VARCHAR(8)      NOT NULL,
    descricao       VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tipo_contrato     PRIMARY KEY (cod_tipo)
);
GRANT ALL ON licitacao.tipo_contrato TO urbem;


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2012'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN
        INSERT INTO licitacao.tipo_contrato VALUES ( 1, 'CT'   , 'Termo de Contrato'                                );
        INSERT INTO licitacao.tipo_contrato VALUES ( 2, 'TACT' , 'Termo Aditivo ao Contrato'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 3, 'TRRCT', 'Termo de Re-Ratificaçao de Contrato'              );
        INSERT INTO licitacao.tipo_contrato VALUES ( 4, 'TDCT' , 'Termo de Distrato de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES ( 5, 'TRCT' , 'Termo de Rescisão de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES ( 6, 'TCU'  , 'Termo de Concessão de Uso'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 7, 'TACU' , 'Termo de Aditivo de Concessão de Uso'             );
        INSERT INTO licitacao.tipo_contrato VALUES ( 8, 'TPU'  , 'Termo de Permissão de Uso'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 9, 'TAPU' , 'Termo Aditivo de Permissão de Uso'                );
        INSERT INTO licitacao.tipo_contrato VALUES (10, 'TAU'  , 'Termo de Autorização de Uso'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (11, 'TAAU' , 'Termo Aditivo a Autorização de Uso'               );
        INSERT INTO licitacao.tipo_contrato VALUES (12, 'TC'   , 'Termo de Cessão'                                  );
        INSERT INTO licitacao.tipo_contrato VALUES (13, 'TAC'  , 'Termo Aditivo a Cessão'                           );
        INSERT INTO licitacao.tipo_contrato VALUES (14, 'TCO'  , 'Termo de Compromisso'                             );
        INSERT INTO licitacao.tipo_contrato VALUES (15, 'TACO' , 'Termo Aditivo ao Compromisso'                     );
        INSERT INTO licitacao.tipo_contrato VALUES (16, 'TDRU' , 'Termo de Direito Real de Uso'                     );
        INSERT INTO licitacao.tipo_contrato VALUES (17, 'TADU' , 'Termo Aditivo ao Direito Real de Uso'             );
        INSERT INTO licitacao.tipo_contrato VALUES (18, 'TD'   , 'Termo de Doação'                                  );
        INSERT INTO licitacao.tipo_contrato VALUES (19, 'CACT' , 'Carta Contrato'                                   );
        INSERT INTO licitacao.tipo_contrato VALUES (20, 'OS'   , 'Ordem de Serviços'                                );
        INSERT INTO licitacao.tipo_contrato VALUES (21, 'TAOS' , 'Termo Aditivo a Ordem de Serviços'                );
        INSERT INTO licitacao.tipo_contrato VALUES (22, 'TRTA' , 'Termo de Revogação do Termo de Autorização de Uso');
        INSERT INTO licitacao.tipo_contrato VALUES (23, 'TA'   , 'Termo de Adesão ao Contrato'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (24, 'TOU'  , 'Termo de Outorga'                                 );
        INSERT INTO licitacao.tipo_contrato VALUES (25, 'TAOU' , 'Termo Aditivo de Outorga'                         );
        INSERT INTO licitacao.tipo_contrato VALUES (26, 'TEXO' , 'Termo de Ex-Ofício'                               );
        INSERT INTO licitacao.tipo_contrato VALUES (27, 'TACC' , 'Termo Adititvo a Carta Contrato'                  );
        INSERT INTO licitacao.tipo_contrato VALUES (28, 'TCT'  , 'Termo de Cooperação Técnica'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (29, 'ATCT' , 'Termo Aditivo de Cooperação Técnica'              );
        INSERT INTO licitacao.tipo_contrato VALUES (30, 'TOS'  , 'Termo de Ordem de Serviços'                       );
        INSERT INTO licitacao.tipo_contrato VALUES (31, 'TRAA' , 'Termo de Recebimento de Auxílio Aluguel'          );
        INSERT INTO licitacao.tipo_contrato VALUES (32, 'TRCM' , 'Termo de Recebimento de Cheque Moradia'           );
        INSERT INTO licitacao.tipo_contrato VALUES (33, 'TRIN' , 'Termo de Recebimento de Indenização'              );
        INSERT INTO licitacao.tipo_contrato VALUES (34, 'TQC'  , 'Termo de Quitação de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES (35, 'PI'   , 'Protocolo de Intenções'                           );
        INSERT INTO licitacao.tipo_contrato VALUES (36, 'TAPI' , 'Termo Aditivo de Protocolo de Intenções'          );
        INSERT INTO licitacao.tipo_contrato VALUES (37, 'TAD'  , 'Termo Aditivo de Doação'                          );
        INSERT INTO licitacao.tipo_contrato VALUES (38, 'ARC'  , 'Apostila de Retificação de Contrato'              );

    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();


ALTER TABLE licitacao.contrato ADD COLUMN cod_tipo_contrato INTEGER NOT NULL;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_5 FOREIGN KEY                        (cod_tipo_contrato)
                                                            REFERENCES licitacao.tipo_contrato (cod_tipo);


----------------
-- Ticket #18363
----------------

CREATE TABLE compras.compra_direta_processo (
    cod_compra_direta       INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    exercicio_entidade      CHAR(4)         NOT NULL,
    cod_modalidade          INTEGER         NOT NULL,
    exercicio_processo      CHAR(4)         NOT NULL,
    cod_processo            INTEGER         NOT NULL,
    CONSTRAINT pk_compra_direta_processo    PRIMARY KEY           (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade),
    CONSTRAINT fk_compra_direta_processo_1  FOREIGN KEY           (exercicio_processo, cod_processo)
                                            REFERENCES sw_processo(ano_exercicio, cod_processo)
);
GRANT ALL ON compras.compra_direta_processo TO urbem;


----------------
-- Ticket #18364
----------------


CREATE TABLE compras.homologacao (
    exercicio               CHAR(4)         NOT NULL,
    num_homologacao         INTEGER         NOT NULL,
    exercicio_compra_direta CHAR(4)         NOT NULL,
    cod_compra_direta       INTEGER         NOT NULL,
    cod_modalidade          INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    lote                    INTEGER         NOT NULL,
    cod_cotacao             INTEGER         NOT NULL,
    cod_item                INTEGER         NOT NULL,
    exercicio_cotacao       CHAR(4)         NOT NULL,
    cgm_fornecedor          INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    homologado              BOOLEAN         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_homologacao               PRIMARY KEY                                 (num_homologacao, cod_compra_direta, cod_modalidade, cod_entidade, exercicio_compra_direta, lote, cod_cotacao, cod_item, exercicio_cotacao, cgm_fornecedor),
    CONSTRAINT fk_homologacao_1             FOREIGN KEY                                 (exercicio_cotacao, cod_cotacao, cod_item, lote, cgm_fornecedor)
                                            REFERENCES compras.julgamento_item          (exercicio, cod_cotacao, cod_item, cgm_fornecedor, lote),
    CONSTRAINT fk_homologacao_2             FOREIGN KEY                                 (cod_documento, cod_tipo_documento)
                                            REFERENCES administracao.modelo_documento   (cod_documento, cod_tipo_documento)
);
GRANT ALL ON compras.homologacao TO urbem;

----------------
-- Ticket #18515 
----------------

ALTER TABLE licitacao.publicacao_contrato ADD num_publicacao INTEGER;


----------------
-- Ticket #18437 
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
values
     ( 2817
     , 326
     , 'FLManterManutencaoProposta.php'
     , 'reemitir'
     , 47
     , ''
     , 'Reemitir Manutenção de Propostas'
     );

----------------
-- Ticket #18438 
----------------
update administracao.acao set ordem = ordem*2 where cod_funcionalidade = 356 ;

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
values
     ( 2818
     , 356
     , 'FLManterManutencaoProposta.php'
     , 'reemitir'
     , 17
     , ''
     , 'Reemitir Manutenção de Propostas'
     );

------------------------
-- Ticket #18522 / 18523 
------------------------

--Contrato aditivo (18522)
ALTER TABLE licitacao.publicacao_contrato_aditivos  ADD COLUMN num_publicacao INTEGER;

--Convênio (18523)
ALTER TABLE licitacao.publicacao_convenio           ADD COLUMN num_publicacao INTEGER;
ALTER TABLE licitacao.publicacao_convenio           ADD COLUMN observacao     VARCHAR(80);
ALTER TABLE licitacao.publicacao_rescisao_convenio  ADD COLUMN num_publicacao INTEGER;

--Convênio aditivo (18523)
ALTER TABLE licitacao.convenio_aditivos_publicacao  ADD  COLUMN num_publicacao INTEGER;
ALTER TABLE licitacao.convenio_aditivos_publicacao  ADD  COLUMN numcgm         INTEGER;

ALTER TABLE licitacao.convenio_aditivos_publicacao  ADD  CONSTRAINT fk_convenio_aditivos_publicacao_2 FOREIGN KEY (numcgm) REFERENCES sw_cgm(numcgm);
ALTER TABLE licitacao.convenio_aditivos_publicacao  DROP CONSTRAINT pk_convenio_aditivos_publicacao;
ALTER TABLE licitacao.convenio_aditivos_publicacao  ADD  CONSTRAINT pk_convenio_aditivos_publicacao PRIMARY KEY (exercicio_convenio,num_convenio,num_aditivo,numcgm,dt_publicacao);

