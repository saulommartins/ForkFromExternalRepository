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
* Fabio Bertoldi - 20150227
*
*/

----------------
-- Ticket #22700
----------------

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
SELECT 1
     , 'mov_sefip_retorno'
     , 1
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.tabelas_rh
              WHERE nome_tabela = 'mov_sefip_retorno'
           )
     ;


----------------
-- Ticket #22803
----------------

SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ADD   COLUMN cod_periodo_movimentacao INTEGER;');

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2015'
        AND parametro  = 'cnpj'
        AND valor      = '30624696000198'
          ;
    IF FOUND THEN
        PERFORM atualizarbanco('UPDATE      beneficio.beneficiario SET        cod_periodo_movimentacao = 780');
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ALTER COLUMN cod_periodo_movimentacao SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ADD CONSTRAINT fk_beneficiario_7 FOREIGN KEY                                   (cod_periodo_movimentacao)
                                                                                           REFERENCES folhapagamento.periodo_movimentacao(cod_periodo_movimentacao);');


----------------
-- Ticket #21970
----------------

CREATE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stCodEntidadePrefeitura     VARCHAR;
    stExecute                   VARCHAR;
    stSQL                       VARCHAR;
    reRecord                    RECORD;
BEGIN
    SELECT valor
      INTO stCodEntidadePrefeitura
      FROM administracao.configuracao
     WHERE cod_modulo = 8
       AND exercicio  = '2015'
       AND parametro  = 'cod_entidade_prefeitura'
         ;

    stSQL := '
                 SELECT cod_entidade
                   FROM administracao.entidade_rh
                  WHERE cod_entidade != '|| stCodEntidadePrefeitura::INTEGER ||'
               GROUP BY entidade_rh.cod_entidade
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        stExecute := '
                       CREATE TABLE beneficio_'|| reRecord.cod_entidade ||'.layout_plano_saude(
                           cod_layout      INTEGER             NOT NULL,
                           padrao          VARCHAR(25)         NOT NULL,
                           CONSTRAINT pk_layout_plano_saude    PRIMARY KEY (cod_layout)
                       );
                     ';
        EXECUTE stExecute;

        stExecute := '
                       GRANT ALL ON beneficio_'|| reRecord.cod_entidade ||'.layout_plano_saude TO urbem;
                     ';
        EXECUTE stExecute;

        stExecute := '
                       INSERT INTO beneficio_'|| reRecord.cod_entidade ||'.layout_plano_saude VALUES (1, ''Unimed'');
                     ';
        EXECUTE stExecute;


        stExecute := '
                       CREATE TABLE beneficio_'|| reRecord.cod_entidade ||'.layout_fornecedor(
                           cgm_fornecedor      INTEGER         NOT NULL,
                           cod_layout          INTEGER         NOT NULL,
                           CONSTRAINT pk_layout_fornecedor     PRIMARY KEY                            (cgm_fornecedor),
                           CONSTRAINT fk_layout_fornecedor_1   FOREIGN KEY                            (cgm_fornecedor)
                                                               REFERENCES compras.fornecedor          (cgm_fornecedor),
                           CONSTRAINT fk_layout_fornecedor_2   FOREIGN KEY                            (cod_layout)
                                                               REFERENCES beneficio.layout_plano_saude(cod_layout)
                       );
                     ';
        EXECUTE stExecute;

        stExecute := '
                       GRANT ALL ON beneficio_'|| reRecord.cod_entidade ||'.layout_fornecedor TO urbem;
                     ';
        EXECUTE stExecute;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22802
----------------

SELECT atualizarbanco('
CREATE TABLE folhapagamento.configuracao_beneficio_fornecedor(
    cod_configuracao        INTEGER     NOT NULL,
    timestamp               TIMESTAMP   NOT NULL,
    cgm_fornecedor          INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_beneficio_fornecedor     PRIMARY KEY                                      (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_beneficio_fornecedor_1   FOREIGN KEY                                      (cod_configuracao, timestamp)
                                                        REFERENCES folhapagamento.configuracao_beneficio (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_beneficio_fornecedor_2   FOREIGN KEY                                      (cgm_fornecedor)
                                                        REFERENCES beneficio.layout_fornecedor           (cgm_fornecedor)
);
');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.configuracao_beneficio_fornecedor TO urbem;');

SELECT atualizarbanco('INSERT INTO beneficio.beneficio_cadastro VALUES (2, ''Plano de Saúde'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.tipo_evento_beneficio VALUES (2,2,''Evento de Desconto Plano de Saúde'');');

