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
*
* Script de DDL e DML
*
* Versao 2.03.7
*
* Gelson Gonçalves - 20150211
*
*/

----------------
-- Ticket #22676
----------------

CREATE TABLE frota.veiculo_cessao (
    id                  INTEGER NOT NULL,
    cod_veiculo         INTEGER NOT NULL,
    cod_processo        INTEGER NOT NULL,
    exercicio           VARCHAR(4) NOT NULL,
    cgm_cedente         INTEGER NOT NULL,
    dt_inicio           DATE,
    dt_termino          DATE,
    CONSTRAINT pk_veiculo_cessao    PRIMARY KEY (id),
    CONSTRAINT fk_veiculo_cessao_1  FOREIGN KEY (cod_veiculo)
                                    REFERENCES frota.veiculo (cod_veiculo),
    CONSTRAINT fk_veiculo_cessao_2  FOREIGN KEY (cod_processo,exercicio)
                                    REFERENCES sw_processo (cod_processo,ano_exercicio),
    CONSTRAINT fk_veiculo_cessao_3  FOREIGN KEY (cgm_cedente)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm)

);

GRANT ALL ON frota.veiculo_cessao TO GROUP urbem;

----------------
-- Ticket #22677
----------------

CREATE TABLE frota.veiculo_locacao (
    id                  INTEGER NOT NULL,
    cod_veiculo         INTEGER NOT NULL,
    cod_processo        INTEGER NOT NULL,
    ano_exercicio       VARCHAR(4) NOT NULL,
    cgm_locatario       INTEGER NOT NULL,
    dt_contrato         DATE NOT NULL,
    dt_inicio           DATE NOT NULL,
    dt_termino          DATE NOT NULL,
    exercicio           VARCHAR(4) NOT NULL,
    cod_entidade        INTEGER NOT NULL,
    cod_empenho         INTEGER NOT NULL,
    vl_locacao          NUMERIC(14,2) NOT NULL,
    CONSTRAINT pk_veiculo_locacao   PRIMARY KEY (id),
    CONSTRAINT fk_veiculo_locacao_1 FOREIGN KEY (cod_veiculo)
                                    REFERENCES frota.veiculo (cod_veiculo),
    CONSTRAINT fk_veiculo_locacao_2 FOREIGN KEY (cod_processo,ano_exercicio)
                                    REFERENCES sw_processo (cod_processo,ano_exercicio),
    CONSTRAINT fk_veiculo_locacao_3 FOREIGN KEY (cgm_locatario)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm),
    CONSTRAINT fk_veiculo_locacao_4 FOREIGN KEY (exercicio,cod_entidade,cod_empenho)
                                    REFERENCES empenho.empenho (exercicio,cod_entidade,cod_empenho)
);

GRANT ALL ON frota.veiculo_locacao TO GROUP urbem;


----------------
-- Ticket #22715
----------------

UPDATE administracao.acao SET nom_arquivo = 'FLManterManutencaoParticipante.php' , parametro = 'alterar' WHERE cod_acao = 1574;
UPDATE administracao.acao SET nom_arquivo = 'FLManterHabilitacaoParticipante.php', parametro = 'alterar' WHERE cod_acao = 1575;


----------------
-- Ticket #22807
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 35
     , '2015'
     , 'numeracao_automatica_licitacao'
     , ''
     );

UPDATE administracao.configuracao
   SET valor = conf_old.valor
  FROM administracao.configuracao AS conf_old
 WHERE conf_old.cod_modulo = 35
   AND conf_old.exercicio  = '2015'
   AND conf_old.parametro = 'numeracao_automatica'
   AND configuracao.cod_modulo = 35
   AND configuracao.exercicio = '2015'
   AND configuracao.parametro = 'numeracao_automatica_licitacao'
     ;


----------------
-- Ticket #22831
----------------

ALTER TABLE almoxarifado.catalogo_item ADD   COLUMN timestamp_inclusao TIMESTAMP;
ALTER TABLE almoxarifado.catalogo_item ADD   COLUMN timestamp_alteracao TIMESTAMP;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    tsDataImplantacao       TIMESTAMP;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2015'
        AND parametro  = 'cnpj'
        AND valor      = '18301002000186'
          ;
    IF FOUND THEN
        UPDATE almoxarifado.catalogo_item
           SET timestamp_inclusao  = '2014-01-01'::timestamp(3)
             , timestamp_alteracao = '2014-01-01'::timestamp(3)
             ;
    ELSE
        SELECT COALESCE(valor::timestamp(3), '2000-01-01'::timestamp(3))
          INTO tsDataImplantacao
          FROM administracao.configuracao
         WHERE cod_modulo = 9
           AND exercicio  = '2015'
           AND parametro  = 'data_implantacao'
             ;
        UPDATE almoxarifado.catalogo_item
           SET timestamp_inclusao  = tsDataImplantacao
             , timestamp_alteracao = tsDataImplantacao
             ;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

ALTER TABLE almoxarifado.catalogo_item ALTER COLUMN timestamp_inclusao  SET NOT NULL;
ALTER TABLE almoxarifado.catalogo_item ALTER COLUMN timestamp_inclusao  SET DEFAULT ('now'::text)::timestamp(3) with time zone;
ALTER TABLE almoxarifado.catalogo_item ALTER COLUMN timestamp_alteracao SET NOT NULL;
ALTER TABLE almoxarifado.catalogo_item ALTER COLUMN timestamp_alteracao SET DEFAULT ('now'::text)::timestamp(3) with time zone;


CREATE OR REPLACE FUNCTION tr_atualiza_timestamp_alteracao() RETURNS TRIGGER AS $$
DECLARE
BEGIN
    NEW.timestamp_alteracao := ('now'::text)::timestamp(3) with time zone ;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tr_update_timestamp_alteracao_catalogo_item BEFORE UPDATE ON almoxarifado.catalogo_item FOR EACH ROW EXECUTE PROCEDURE tr_atualiza_timestamp_alteracao();


----------------
-- Ticket #22834
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_catalog.pg_constraint
      WHERE conname = 'uk_fornecedor_socio_1'
          ;
    IF FOUND THEN
        ALTER TABLE compras.fornecedor_socio DROP CONSTRAINT uk_fornecedor_socio_1;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

ALTER TABLE compras.fornecedor_socio ADD  CONSTRAINT uk_fornecedor_socio_1 UNIQUE (cgm_fornecedor, cgm_socio, cod_tipo);

