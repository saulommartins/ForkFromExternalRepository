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
* $Id: GP_1920.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.0
*/


----------------
-- Ticket #13738
----------------

--DROP TABLE licitacao.documento_atributo_valor;

CREATE TABLE licitacao.atributo_participante_documentos_valor (
    cod_licitacao           INTEGER                         NOT NULL, 
    cod_documento           INTEGER                         NOT NULL,
    dt_validade             DATE                            NOT NULL,
    cgm_fornecedor          INTEGER                         NOT NULL,
    cod_modalidade          INTEGER                         NOT NULL,
    cod_entidade            INTEGER                         NOT NULL,
    exercicio               CHARACTER(4)                    NOT NULL,
    timestamp               TIMESTAMP                       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    valor                   TEXT                            NOT NULL,
    CONSTRAINT pk_atributo_participante_documentos_valor    PRIMARY KEY (cod_licitacao, cod_documento, dt_validade, cgm_fornecedor, cod_modalidade, cod_entidade, exercicio, timestamp),
    CONSTRAINT fk_atributo_participante_documentos_valor    FOREIGN KEY (cod_licitacao, cod_documento, dt_validade, cgm_fornecedor, cod_modalidade, cod_entidade, exercicio)
                                                            REFERENCES licitacao.participante_documentos (cod_licitacao, cod_documento, dt_validade, cgm_fornecedor, cod_modalidade, cod_entidade, exercicio)
 );

GRANT ALL ON TABLE licitacao.atributo_participante_documentos_valor TO GROUP urbem;



----------------
-- Ticket #12854
----------------

CREATE TABLE licitacao.comissao_licitacao_membros (
    exercicio               CHAR(4)             NOT NULL,
    cod_entidade            INTEGER             NOT NULL,
    cod_modalidade          INTEGER             NOT NULL,
    cod_licitacao           INTEGER             NOT NULL,
    cod_comissao            INTEGER             NOT NULL,
    numcgm                  INTEGER             NOT NULL,
    cod_norma               INTEGER             NOT NULL,
    CONSTRAINT pk_comissao_licitacao_membros    PRIMARY KEY (exercicio, cod_entidade, cod_modalidade, cod_licitacao, cod_comissao, numcgm, cod_norma),
    CONSTRAINT fk_comissao_licitacao_membros_1  FOREIGN KEY                             (exercicio, cod_entidade, cod_modalidade, cod_licitacao, cod_comissao)
                                                REFERENCES licitacao.comissao_licitacao (exercicio, cod_entidade, cod_modalidade, cod_licitacao, cod_comissao),
    CONSTRAINT fk_comissao_licitacao_membros_2  FOREIGN KEY                             (cod_comissao, numcgm, cod_norma)
                                                REFERENCES licitacao.comissao_membros   (cod_comissao, numcgm, cod_norma)
);

GRANT ALL ON licitacao.comissao_licitacao_membros TO GROUP urbem;


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    crLicitacao     REFCURSOR;
    reLicitacao     RECORD;

    crMembros       REFCURSOR;
    reMembros       RECORD;

    stSql           VARCHAR;
    stSqlMembros    VARCHAR;
BEGIN

    stSql := ' SELECT *
                 FROM licitacao.comissao_licitacao;
             ';

    OPEN crLicitacao FOR EXECUTE stSql;
    LOOP
        FETCH crLicitacao INTO reLicitacao;
        EXIT WHEN NOT FOUND;

        stSqlMembros := '    SELECT *
                               FROM licitacao.comissao_membros
                          LEFT JOIN licitacao.membro_excluido
                                 ON membro_excluido.cod_comissao  = comissao_membros.cod_comissao
                                AND membro_excluido.numcgm        = comissao_membros.numcgm
                                AND membro_excluido.cod_norma     = comissao_membros.cod_norma
                              WHERE membro_excluido.numcgm        IS NULL
                                AND comissao_membros.cod_comissao = '|| reLicitacao.cod_comissao ||';
                        ';

        OPEN crMembros FOR EXECUTE stSqlMembros;
        LOOP
            FETCH crMembros INTO reMembros;
            EXIT WHEN NOT FOUND;

                INSERT
                  INTO licitacao.comissao_licitacao_membros
                     ( exercicio
                     , cod_entidade
                     , cod_modalidade
                     , cod_licitacao
                     , cod_comissao
                     , numcgm
                     , cod_norma
                     )
                VALUES ( reLicitacao.exercicio
                     , reLicitacao.cod_entidade
                     , reLicitacao.cod_modalidade
                     , reLicitacao.cod_licitacao
                     , reMembros.cod_comissao
                     , reMembros.numcgm
                     , reMembros.cod_norma
                     );

        END LOOP;

        CLOSE crMembros;

    END LOOP;

    CLOSE crLicitacao;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #12654
----------------

INSERT
  INTO patrimonio.situacao_bem 
     ( cod_situacao
     , nom_situacao)
VALUES ( 10
     , 'Não Localizado'
     );

----------------
-- Ticket #13883
----------------

ALTER TABLE almoxarifado.lancamento_material ADD COLUMN valor_mercado NUMERIC(14,2) NOT NULL DEFAULT 0.00;

UPDATE almoxarifado.lancamento_material
   SET valor_mercado                              = lancamento_material_valor.valor_mercado
  FROM almoxarifado.lancamento_material_valor
 WHERE lancamento_material_valor.cod_lancamento   = lancamento_material.cod_lancamento
   AND lancamento_material_valor.cod_item         = lancamento_material.cod_item
   AND lancamento_material_valor.cod_centro       = lancamento_material.cod_centro
   AND lancamento_material_valor.cod_marca        = lancamento_material.cod_marca
   AND lancamento_material_valor.cod_almoxarifado = lancamento_material.cod_almoxarifado;

ALTER TABLE almoxarifado.doacao_emprestimo DROP CONSTRAINT fk_doacao_emprestimo_2;
ALTER TABLE almoxarifado.doacao_emprestimo ADD  CONSTRAINT fk_doacao_emprestimo_2 FOREIGN KEY (cod_almoxarifado, cod_marca, cod_centro, cod_item, cod_lancamento)
                                                REFERENCES almoxarifado.lancamento_material   (cod_almoxarifado, cod_marca, cod_centro, cod_item, cod_lancamento);

DROP TABLE almoxarifado.lancamento_material_valor;

----------------
-- Ticket #12826
----------------

ALTER TABLE compras.solicitacao_homologada_reserva 
       DROP CONSTRAINT pk_solicitacao_homologada_reserva;

ALTER TABLE compras.solicitacao_homologada_reserva
       DROP CONSTRAINT fk_solicitacao_homologada_reserva_3;

ALTER TABLE compras.solicitacao_item_dotacao
       DROP CONSTRAINT pk_solicitacao_item_dotacao;


   ALTER TABLE compras.solicitacao_item_dotacao
ADD CONSTRAINT pk_solicitacao_item_dotacao        
   PRIMARY KEY (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa);

ALTER TABLE compras.solicitacao_item_dotacao
 ADD COLUMN quantidade NUMERIC(14,4);

UPDATE compras.solicitacao_item_dotacao
   SET quantidade = solicitacao_item.quantidade
  FROM compras.solicitacao_item
 WHERE solicitacao_item.exercicio       = solicitacao_item_dotacao.exercicio
   AND solicitacao_item.cod_entidade    = solicitacao_item_dotacao.cod_entidade
   AND solicitacao_item.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
   AND solicitacao_item.cod_centro      = solicitacao_item_dotacao.cod_centro
   AND solicitacao_item.cod_item        = solicitacao_item_dotacao.cod_item;

 ALTER TABLE compras.solicitacao_item_dotacao
ALTER COLUMN quantidade SET NOT NULL;

ALTER TABLE compras.solicitacao_homologada_reserva
 ADD COLUMN cod_conta INTEGER;

ALTER TABLE compras.solicitacao_homologada_reserva
 ADD COLUMN cod_despesa INTEGER;


UPDATE compras.solicitacao_homologada_reserva 
   SET cod_conta   = solicitacao_item_dotacao.cod_conta
     , cod_despesa = solicitacao_item_dotacao.cod_despesa 
  FROM compras.solicitacao_item_dotacao
 WHERE solicitacao_item_dotacao.exercicio       = solicitacao_homologada_reserva.exercicio
   AND solicitacao_item_dotacao.cod_entidade    = solicitacao_homologada_reserva.cod_entidade
   AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_homologada_reserva.cod_solicitacao
   AND solicitacao_item_dotacao.cod_centro      = solicitacao_homologada_reserva.cod_centro
   AND solicitacao_item_dotacao.cod_item        = solicitacao_homologada_reserva.cod_item;


   ALTER TABLE compras.solicitacao_homologada_reserva 
ADD CONSTRAINT pk_solicitacao_homologada_reserva 
   PRIMARY KEY (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa, cod_reserva);

   ALTER TABLE compras.solicitacao_homologada_reserva 
ADD CONSTRAINT fk_solicitacao_homologada_reserva_3 
   FOREIGN KEY (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa) 
    REFERENCES compras.solicitacao_item_dotacao(exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa);


----------------
-- Ticket #12826
----------------

CREATE TABLE compras.solicitacao_item_dotacao_anulacao (
    exercicio           CHARACTER(4)    NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    cod_solicitacao     INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_centro          INTEGER         NOT NULL,
    cod_item            INTEGER         NOT NULL,
    cod_conta           INTEGER         NOT NULL,
    cod_despesa         INTEGER         NOT NULL,
    quantidade          NUMERIC(14,4)   NOT NULL,
    vl_anulacao         NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_solicitacao_item_dotacao_anulacao     PRIMARY KEY (exercicio, cod_entidade, cod_solicitacao, timestamp, cod_centro, cod_item, cod_conta, cod_despesa),
    CONSTRAINT fk_solicitacao_item_dotacao_anulacao_1   FOREIGN KEY                                  (exercicio, cod_entidade, cod_solicitacao, timestamp, cod_centro, cod_item)
                                                        REFERENCES compras.solicitacao_item_anulacao (exercicio, cod_entidade, cod_solicitacao, timestamp, cod_centro, cod_item),
    CONSTRAINT fk_solicitacao_item_dotacao_anulacao_2   FOREIGN KEY                                  (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa)
                                                        REFERENCES compras.solicitacao_item_dotacao  (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa)
 );

GRANT ALL ON compras.solicitacao_item_dotacao_anulacao TO GROUP urbem;



----------------
-- Ticket #13770
----------------

CREATE TABLE almoxarifado.saida_diversa (
    cod_lancamento          INTEGER         NOT NULL,
    cod_item                INTEGER         NOT NULL,
    cod_marca               INTEGER         NOT NULL,
    cod_almoxarifado        INTEGER         NOT NULL,
    cod_centro              INTEGER         NOT NULL,
    cgm_solicitante         INTEGER         NOT NULL,
    observacao              VARCHAR(160)    NOT NULL,
    CONSTRAINT pk_saida_diversa             PRIMARY KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro, cgm_solicitante),
    CONSTRAINT fk_saida_diversa_1           FOREIGN KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
                                            REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_saida_diversa_2           FOREIGN KEY                                 (cgm_solicitante)
                                            REFERENCES sw_cgm                           (numcgm)
);

GRANT ALL ON almoxarifado.saida_diversa TO GROUP urbem;


INSERT
  INTO almoxarifado.natureza
     ( cod_natureza
     , tipo_natureza
     , descricao )
VALUES ( 9
       , 'S'
       , 'Saída Diversa'
        );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2414
          , 291
          , 'FMMovimentacaoDiversa.php'
          , 'saida'
          , 30
          , ''
          , 'Saídas Diversas'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 29
         , 9
         , 'Saída Diversa'
         , 'saidaDiversa.rptdesign'
         );



----------------
-- Ticket #12827
----------------

CREATE TABLE compras.solicitacao_convenio (
    exercicio               CHAR(4)         NOT NULL, 
    cod_entidade            INTEGER         NOT NULL,
    cod_solicitacao         INTEGER         NOT NULL,
    num_convenio            INTEGER         NOT NULL,
    exercicio_convenio      CHAR(4)         NOT NULL,
    CONSTRAINT pk_solicitacao_convenio      PRIMARY KEY                     (exercicio, cod_entidade, cod_solicitacao),
    CONSTRAINT fk_solicitacao_convenio_1    FOREIGN KEY                     (exercicio, cod_entidade, cod_solicitacao)
                                            REFERENCES compras.solicitacao  (exercicio, cod_entidade, cod_solicitacao),
    CONSTRAINT fk_solicitacao_convenio_2    FOREIGN KEY                     (num_convenio, exercicio_convenio)
                                            REFERENCES licitacao.convenio   (num_convenio, exercicio)
 );

GRANT ALL ON compras.solicitacao_convenio TO GROUP urbem;


----------------
-- Ticket #12814
----------------

CREATE TABLE compras.nota_fiscal_fornecedor_ordem (
    cgm_fornecedor          INTEGER         NOT NULL,
    cod_nota                INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    cod_ordem               INTEGER         NOT NULL,
    tipo                    CHAR(1)         NOT NULL,
    CONSTRAINT pk_nota_fiscal_fornecedor_ordem   PRIMARY KEY                                 (cgm_fornecedor, cod_nota),
    CONSTRAINT fk_nota_fiscal_fornecedor_ordem_1 FOREIGN KEY                                 (cgm_fornecedor, cod_nota)
                                                        REFERENCES compras.nota_fiscal_fornecedor   (cgm_fornecedor, cod_nota),
    CONSTRAINT fk_nota_fiscal_fornecedor_ordem_2 FOREIGN KEY                                 (exercicio, cod_entidade, cod_ordem, tipo)
                                                        REFERENCES compras.ordem                    (exercicio, cod_entidade, cod_ordem, tipo)
);

GRANT ALL ON compras.nota_fiscal_fornecedor_ordem TO GROUP urbem;

INSERT
  INTO compras.nota_fiscal_fornecedor_ordem
     ( cgm_fornecedor
     , cod_nota
     , exercicio
     , cod_entidade
     , cod_ordem )
SELECT cgm_fornecedor
     , cod_nota
     , exercicio_ordem
     , cod_entidade
     , cod_ordem
  FROM compras.nota_fiscal_fornecedor;

ALTER TABLE compras.nota_fiscal_fornecedor DROP CONSTRAINT fk_nota_fiscal_fornecedor_2;
ALTER TABLE compras.nota_fiscal_fornecedor DROP COLUMN exercicio_ordem;
ALTER TABLE compras.nota_fiscal_fornecedor DROP COLUMN cod_entidade;
ALTER TABLE compras.nota_fiscal_fornecedor DROP COLUMN cod_ordem;


---------------------------------------------
-- SOLICITADO POR GELSON GONCALVES - 20081127
---------------------------------------------

CREATE TABLE compras.mapa_item_dotacao (
    exercicio               CHAR(4)         NOT NULL,
    cod_mapa                INTEGER         NOT NULL,
    exercicio_solicitacao   CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    cod_solicitacao         INTEGER         NOT NULL,
    cod_centro              INTEGER         NOT NULL,
    cod_item                INTEGER         NOT NULL,
    lote                    INTEGER         NOT NULL,
    cod_conta               INTEGER         NOT NULL,
    cod_despesa             INTEGER         NOT NULL,
    quantidade              NUMERIC(14,2)   NOT NULL,
    vl_dotacao              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_mapa_item_dotacao         PRIMARY KEY                                     (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa),
    CONSTRAINT fk_mapa_item_dotacao_1       FOREIGN KEY                                     (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote)
                                            REFERENCES compras.mapa_item                    (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote),
    CONSTRAINT fk_mapa_item_dotacao_2       FOREIGN KEY                                     (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa)
                                            REFERENCES compras.solicitacao_item_dotacao     (exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa)
);

GRANT ALL ON compras.mapa_item_dotacao TO GROUP urbem;

CREATE OR REPLACE FUNCTION manutencao_item_dotacao() RETURNS VOID AS $$
DECLARE

    reRecord1   RECORD;
    stSQL1      VARCHAR;

    reRecord2   RECORD;
    stSQL2      VARCHAR;

    reRecord3   RECORD;
    stSQL3      VARCHAR;

BEGIN

    stSQL1 := '     SELECT *
                      FROM compras.solicitacao_item;
              ';
    FOR reRecord1 IN EXECUTE stSQL1 LOOP

        stSQL2 := '      SELECT *
                           FROM compras.solicitacao_item_dotacao
                          WHERE exercicio       = '|| reRecord1.exercicio       ||'
                            AND cod_entidade    = '|| reRecord1.cod_entidade    ||'
                            AND cod_solicitacao = '|| reRecord1.cod_solicitacao ||'
                            AND cod_centro      = '|| reRecord1.cod_centro      ||'
                            AND cod_item        = '|| reRecord1.cod_item        ||';
                  ';
        
        FOR reRecord2 IN EXECUTE stSQL2 LOOP

            stSQL3 := '     SELECT *
                              FROM compras.mapa_item
                             WHERE exercicio_solicitacao = '|| reRecord2.exercicio       ||'
                               AND cod_entidade          = '|| reRecord2.cod_entidade    ||'
                               AND cod_solicitacao       = '|| reRecord2.cod_solicitacao ||'
                               AND cod_centro            = '|| reRecord2.cod_centro      ||'
                               AND cod_item              = '|| reRecord2.cod_item        ||';
                      ';

            FOR reRecord3 IN EXECUTE stSQL3 LOOP

                INSERT
                  INTO compras.mapa_item_dotacao
                     ( exercicio
                     , cod_mapa
                     , exercicio_solicitacao
                     , cod_entidade
                     , cod_solicitacao
                     , cod_centro
                     , cod_item
                     , lote
                     , cod_conta
                     , cod_despesa
                     , quantidade
                     , vl_dotacao )
                VALUES ( reRecord3.exercicio
                     , reRecord3.cod_mapa
                     , reRecord3.exercicio_solicitacao
                     , reRecord3.cod_entidade
                     , reRecord3.cod_solicitacao
                     , reRecord3.cod_centro
                     , reRecord3.cod_item
                     , reRecord3.lote
                     , reRecord2.cod_conta
                     , reRecord2.cod_despesa
                     , reRecord3.quantidade
                     , reRecord3.vl_total
                     );

            END LOOP;
        
        END LOOP; 

    END LOOP;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_item_dotacao();
DROP FUNCTION manutencao_item_dotacao();



ALTER TABLE compras.mapa_item_anulacao ADD COLUMN cod_conta   INTEGER;
ALTER TABLE compras.mapa_item_anulacao ADD COLUMN cod_despesa INTEGER;

CREATE OR REPLACE FUNCTION manutencao_item_anulacao() RETURNS VOID AS $$
DECLARE

    reRecord    RECORD;
    stSQL       VARCHAR;

BEGIN

    stSQL := '  SELECT *
                  FROM compras.mapa_item_dotacao;
             ';

    FOR reRecord IN EXECUTE stSQL LOOP

        UPDATE compras.mapa_item_anulacao
           SET cod_conta             = reRecord.cod_conta
             , cod_despesa           = reRecord.cod_despesa
         WHERE exercicio             = reRecord.exercicio
           AND cod_mapa              = reRecord.cod_mapa
           AND exercicio_solicitacao = reRecord.exercicio_solicitacao
           AND cod_entidade          = reRecord.cod_entidade
           AND cod_solicitacao       = reRecord.cod_solicitacao
           AND cod_centro            = reRecord.cod_centro
           AND cod_item              = reRecord.cod_item
           AND lote                  = reRecord.lote;

    END LOOP;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_item_anulacao();
DROP FUNCTION manutencao_item_anulacao();

ALTER TABLE compras.mapa_item_anulacao ALTER COLUMN cod_conta   SET NOT NULL;
ALTER TABLE compras.mapa_item_anulacao ALTER COLUMN cod_despesa SET NOT NULL;
ALTER TABLE compras.mapa_item_anulacao ADD CONSTRAINT fk_mata_item_anulacao_3 FOREIGN KEY                           (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa)
                                                                              REFERENCES compras.mapa_item_dotacao  (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa);


------------------------------------------------
-- CRIACAO DA TABELA almoxarifado.lancamento_bem
-- GELSON - 20081205 ---------------------------

CREATE TABLE almoxarifado.lancamento_bem(
    cod_lancamento      integer     not null, 
    cod_item            integer     not null,
    cod_marca           integer     not null,
    cod_almoxarifado    integer     not null,
    cod_centro          integer     not null,
    cod_bem             integer     not null,
    CONSTRAINT pk_lancamento_bem    PRIMARY KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro, cod_bem),
    CONSTRAINT fk_lancamento_bem_1  FOREIGN KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
                                    REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_bem_2  FOREIGN KEY                                 (cod_bem)
                                    REFERENCES patrimonio.bem                   (cod_bem)
);

GRANT ALL ON almoxarifado.lancamento_bem TO GROUP urbem;


----------------
-- Ticket #13883
----------------

UPDATE almoxarifado.lancamento_material SET valor_mercado = valor_mercado * -1 WHERE tipo_natureza = 'S' AND valor_mercado > 0;
UPDATE almoxarifado.lancamento_material SET quantidade = quantidade * -1       WHERE tipo_natureza = 'S' AND quantidade > 0;


------------------------------------------------------------------------------------------------------
-- ALTERADA FK DA TABELA compras.mapa_item_reserva DE compras.mapa_item PARA compras.mapa_item_dotacao
-- GELSON WOLOWSKI - 20081211 ------------------------------------------------------------------------

ALTER TABLE compras.mapa_item_reserva ADD COLUMN cod_conta   INTEGER;
ALTER TABLE compras.mapa_item_reserva ADD COLUMN cod_despesa INTEGER;

UPDATE compras.mapa_item_reserva
   SET cod_conta   = mapa_item_dotacao.cod_conta
     , cod_despesa = mapa_item_dotacao.cod_despesa
  FROM compras.mapa_item_dotacao
 WHERE mapa_item_reserva.exercicio_mapa        = mapa_item_dotacao.exercicio
   AND mapa_item_reserva.cod_mapa              = mapa_item_dotacao.cod_mapa
   AND mapa_item_reserva.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
   AND mapa_item_reserva.cod_entidade          = mapa_item_dotacao.cod_entidade
   AND mapa_item_reserva.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
   AND mapa_item_reserva.cod_centro            = mapa_item_dotacao.cod_centro
   AND mapa_item_reserva.cod_item              = mapa_item_dotacao.cod_item
   AND mapa_item_reserva.lote                  = mapa_item_dotacao.lote;

ALTER TABLE compras.mapa_item_reserva ALTER COLUMN cod_conta   SET NOT NULL;
ALTER TABLE compras.mapa_item_reserva ALTER COLUMN cod_despesa SET NOT NULL;

ALTER TABLE compras.mapa_item_reserva DROP CONSTRAINT fk_mapa_item_reserva_1;
ALTER TABLE compras.mapa_item_reserva ADD  CONSTRAINT fk_mapa_item_reserva_1 FOREIGN KEY                            (exercicio_mapa, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa)
                                                                             REFERENCES  compras.mapa_item_dotacao  (exercicio,      cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa);

ALTER TABLE compras.mapa_item_reserva DROP CONSTRAINT pk_mapa_item_reserva;
ALTER TABLE compras.mapa_item_reserva ADD  CONSTRAINT pk_mapa_item_reserva   PRIMARY KEY                             (exercicio_mapa, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa);


----------------
-- Ticket #12865
----------------

ALTER TABLE compras.tipo_objeto ALTER descricao TYPE VARCHAR(50);
INSERT INTO compras.tipo_objeto (cod_tipo_objeto, descricao) VALUES ('4', 'Alienação de Bens Móveis/Imóveis');


----------------
-- Ticket #12722
----------------

CREATE TABLE almoxarifado.pedido_transferencia_item_destino(
    exercicio                   character(4)            not null, 
    cod_transferencia           integer                 not null,
    cod_item                    integer                 not null,
    cod_marca                   integer                 not null,
    cod_centro                  integer                 not null,
    cod_centro_destino          integer                 not null,
    CONSTRAINT pk_pedido_transferencia_item_destino     PRIMARY KEY                                         (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_centro_destino),
    CONSTRAINT fk_pedido_transferencia_item_destino_1   FOREIGN KEY                                         (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro)
                                                        REFERENCES almoxarifado.pedido_transferencia_item   (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro),
    CONSTRAINT fk_pedido_transferencia_item_destino_2   FOREIGN KEY                                         (cod_centro_destino)
                                                        REFERENCES almoxarifado.centro_custo                (cod_centro)
);

GRANT ALL ON almoxarifado.pedido_transferencia_item_destino TO GROUP urbem;

CREATE TABLE almoxarifado.transferencia_almoxarifado_item_destino(
    exercicio                   character(4)            not null,
    cod_transferencia           integer                 not null,
    cod_item                    integer                 not null,
    cod_marca                   integer                 not null,
    cod_centro                  integer                 not null,
    cod_centro_destino          integer                 not null,
    cod_lancamento              integer                 not null, 
    cod_almoxarifado            integer                 not null,
    CONSTRAINT pk_transferencia_almoxarifado_item_destino   PRIMARY KEY                                                 (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_centro_destino, cod_lancamento, cod_almoxarifado),
    CONSTRAINT fk_transferencia_almoxarifado_item_destino_1 FOREIGN KEY                                                 (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_centro_destino)
                                                            REFERENCES almoxarifado.pedido_transferencia_item_destino   (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_centro_destino),
    CONSTRAINT fk_transferencia_almoxarifado_item_destino_2 FOREIGN KEY                                                 (cod_item, cod_marca, cod_centro_destino, cod_lancamento, cod_almoxarifado)
                                                            REFERENCES almoxarifado.lancamento_material                 (cod_item, cod_marca, cod_centro, cod_lancamento, cod_almoxarifado)
);

GRANT ALL ON almoxarifado.transferencia_almoxarifado_item_destino TO GROUP urbem;

insert into administracao.acao( cod_acao
                              , cod_funcionalidade
                              , nom_arquivo,parametro
                              , ordem
                              , complemento_acao
                              , nom_acao)
                       values ( 2157
                              , 291
                              , 'FLEstornoEntrada.php'
                              , 'saida'
                              , 20
                              , ''
                              , 'Saída por Estorno de Entrada');


----------------
-- Ticket #14201
----------------

ALTER TABLE compras.ordem_item_anulacao DROP CONSTRAINT fk_ordem_item_anulacao_2;
ALTER TABLE compras.ordem_item_anulacao DROP CONSTRAINT pk_ordem_item_anulacao;

ALTER TABLE compras.ordem_item DROP CONSTRAINT fk_ordem_item_1;
ALTER TABLE compras.ordem_item DROP CONSTRAINT pk_ordem_item;


ALTER TABLE compras.ordem_item ADD  COLUMN exercicio_pre_empenho CHAR(4);

UPDATE compras.ordem_item
   SET exercicio_pre_empenho = item_pre_empenho_julgamento.exercicio
  FROM empenho.item_pre_empenho_julgamento
 WHERE item_pre_empenho_julgamento.cod_pre_empenho = ordem_item.cod_pre_empenho
   AND item_pre_empenho_julgamento.num_item        = ordem_item.num_item;

ALTER TABLE compras.ordem_item ALTER COLUMN exercicio_pre_empenho SET NOT NULL;
ALTER TABLE compras.ordem_item ADD  CONSTRAINT pk_ordem_item    PRIMARY KEY (exercicio, cod_entidade, cod_ordem, exercicio_pre_empenho, cod_pre_empenho, num_item, tipo);
ALTER TABLE compras.ordem_item ADD  CONSTRAINT fk_ordem_item_1  FOREIGN KEY                                     (exercicio_pre_empenho, cod_pre_empenho, num_item)
                                                                REFERENCES empenho.item_pre_empenho_julgamento  (exercicio, cod_pre_empenho, num_item);


ALTER TABLE compras.ordem_item_anulacao ADD COLUMN exercicio_pre_empenho CHAR(4);

UPDATE compras.ordem_item_anulacao
   SET exercicio_pre_empenho = compras.ordem_item.exercicio_pre_empenho
  FROM compras.ordem_item
 WHERE ordem_item.exercicio       = ordem_item_anulacao.exercicio
   AND ordem_item.cod_entidade    = ordem_item_anulacao.cod_entidade
   AND ordem_item.cod_ordem       = ordem_item_anulacao.cod_ordem
   AND ordem_item.cod_pre_empenho = ordem_item_anulacao.cod_pre_empenho
   AND ordem_item.num_item        = ordem_item_anulacao.num_item
   AND ordem_item.tipo            = ordem_item_anulacao.tipo;

ALTER TABLE compras.ordem_item_anulacao ALTER COLUMN exercicio_pre_empenho SET NOT NULL;

ALTER TABLE compras.ordem_item_anulacao ADD CONSTRAINT pk_ordem_item_anulacao      PRIMARY KEY (exercicio, cod_entidade, cod_ordem, exercicio_pre_empenho, cod_pre_empenho, num_item, timestamp, tipo);
ALTER TABLE compras.ordem_item_anulacao ADD CONSTRAINT fk_ordem_item_anulacao_2    FOREIGN KEY                  (exercicio, cod_entidade, cod_ordem, exercicio_pre_empenho, cod_pre_empenho, num_item, tipo)
                                                                                   REFERENCES compras.ordem_item(exercicio, cod_entidade, cod_ordem, exercicio_pre_empenho, cod_pre_empenho, num_item, tipo);


----------------
-- Ticket #12896
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor )
VALUES ( ( SELECT MAX(exercicio)
             FROM administracao.configuracao
         )
     , 35
     , 'tipo_valor_referencia'
     , 'solicitado'
     );


------------------------------------------
-- RECRIANDO AÇÃO 1654 - Lista Patrimonial
------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     SELECT 1654
          , 28
          , 'FLListaPatrimonial.php'
          , ''
          , 20
          , ''
          , 'Lista Patrimonial'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1654
                );


----------------
-- Ticket #12859
----------------

INSERT INTO patrimonio.natureza VALUES (0,'Não Informado'    );
INSERT INTO patrimonio.grupo    VALUES (0,0,'Não Informado',0);
INSERT INTO patrimonio.especie  VALUES (0,0,0,'Não Informado');

DROP VIEW patrimonio.vw_bem_ativo;

ALTER TABLE patrimonio.bem ALTER COLUMN descricao TYPE VARCHAR(160);

CREATE VIEW patrimonio.vw_bem_ativo AS
    SELECT bem.cod_bem
         , bem.num_placa
         , bem.numcgm
         , bem.descricao
         , bem.detalhamento
         , bem.dt_aquisicao
         , bem.dt_depreciacao
         , bem.dt_garantia
         , bem.vl_bem
         , bem.vl_depreciacao
         , bem.identificacao
      FROM patrimonio.bem
     WHERE NOT (bem.cod_bem IN ( SELECT bem_baixado.cod_bem
                                   FROM patrimonio.bem_baixado))
  GROUP BY bem.cod_bem
         , bem.num_placa
         , bem.numcgm
         , bem.descricao
         , bem.detalhamento
         , bem.dt_aquisicao
         , bem.dt_depreciacao
         , bem.dt_garantia
         , bem.vl_bem
         , bem.vl_depreciacao
         , bem.identificacao;

----------------
-- Ticket #12797
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 3
          , 6
          , 11
          , 'Relatório de Bens por Entidade'
          , 'relatorioBemEntidade.rptdesign'
          );

----------------
-- Ticket #10342
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 3
          , 6
          , 12
          , 'Lista Patrimonial'
          , 'listaPatrimonial.rptdesign'
          );



----------------
-- Ticket #12792
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 3
          , 6
          , 13
          , 'Relatório de Carga Patrimonial'
          , 'relatorioCargaPatrimonial.rptdesign'
          );


----------------
-- Ticket #12790
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 3
          , 6
          , 14
          , 'Relatório Ficha Patrimonial'
          , 'fichaPatrimonial.rptdesign'
          );

----------------
-- Ticket #12791
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 3
          , 6
          , 15
          , 'Posição Financeira por Natureza'
          , 'posicaoFinanceiraNatureza.rptdesign'
          );

CREATE TABLE licitacao.edital_suspenso (
    num_edital              INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    dt_suspensao            DATE            NOT NULL,
    justificativa           TEXT            NOT NULL,
    CONSTRAINT pk_edital_suspenso           PRIMARY KEY                 (num_edital, exercicio),
    CONSTRAINT fk_edital_suspenso_1         FOREIGN KEY                 (num_edital, exercicio)
                                            REFERENCES licitacao.edital (num_edital, exercicio)
 );

GRANT ALL ON licitacao.edital_suspenso TO GROUP urbem;


