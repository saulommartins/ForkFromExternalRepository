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
* Versao 2.05.4
*
* Fabio Bertoldi - 20160824
*
*/

----------------
-- Ticket #24031
----------------

ALTER TABLE administracao.modelo_arquivos_documento DROP CONSTRAINT fk_modelo_arquivos_documento_3;
ALTER TABLE divida.documento                        DROP CONSTRAINT fk_documento_1                ;
ALTER TABLE divida.modalidade_documento             DROP CONSTRAINT fk_modalidade_documento_2     ;
ALTER TABLE divida.emissao_documento                DROP CONSTRAINT fk_emissao_documento_1        ;
ALTER TABLE divida.documento_parcela                DROP CONSTRAINT fk_documento_parcela_1        ;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodDocumento  INTEGER;
BEGIN
    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Termo Inscrição DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Certidão DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 3 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Memorial Calculo DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Termo Consolidação DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Notificação DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 4 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

    SELECT cod_documento
      INTO inCodDocumento
      FROM administracao.modelo_documento
     WHERE cod_tipo_documento = 5
       AND nome_documento     = 'Termo Parcelamento DA';

    UPDATE administracao.modelo_documento           SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE administracao.modelo_arquivos_documento  SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento                         SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.modalidade_documento              SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.emissao_documento                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;
    UPDATE divida.documento_parcela                 SET cod_tipo_documento = 2 WHERE cod_tipo_documento = 5 and cod_documento = inCodDocumento;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

ALTER TABLE divida.documento_parcela                ADD  CONSTRAINT fk_documento_parcela_1          FOREIGN KEY (num_parcelamento, cod_tipo_documento, cod_documento)   REFERENCES divida.documento(num_parcelamento, cod_tipo_documento, cod_documento);
ALTER TABLE divida.emissao_documento                ADD  CONSTRAINT fk_emissao_documento_1          FOREIGN KEY (num_parcelamento, cod_tipo_documento, cod_documento)   REFERENCES divida.documento(num_parcelamento, cod_tipo_documento, cod_documento);
ALTER TABLE divida.modalidade_documento             ADD  CONSTRAINT fk_modalidade_documento_2       FOREIGN KEY (cod_tipo_documento, cod_documento)                     REFERENCES administracao.modelo_documento(cod_tipo_documento, cod_documento);
ALTER TABLE divida.documento                        ADD  CONSTRAINT fk_documento_1                  FOREIGN KEY (cod_tipo_documento, cod_documento)                     REFERENCES administracao.modelo_documento(cod_tipo_documento, cod_documento);
ALTER TABLE administracao.modelo_arquivos_documento ADD  CONSTRAINT fk_modelo_arquivos_documento_3  FOREIGN KEY (cod_tipo_documento, cod_documento)                     REFERENCES administracao.modelo_documento(cod_tipo_documento, cod_documento);


----------------------------------------------------------------------------
-- INSERT DO CARNE PARA ACAO DE Nota Fiscal Avulsa (2240) - LUCIANA 20160908
----------------------------------------------------------------------------

INSERT
  INTO arrecadacao.acao_modelo_carne
     ( cod_modelo
     , cod_acao
     )
SELECT 4
     , 2240
 WHERE 0 = (
             SELECT COUNT(1)
               FROM arrecadacao.acao_modelo_carne
              WHERE cod_acao = 2240
           )
     ;


-----------------------
-- Ticket #24112 #24122
-----------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2016'
        AND parametro  = 'cnpj'
        AND valor      = '04628681000198'
          ;
    IF FOUND THEN
        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE cod_modelo = 6
              ;
        IF NOT FOUND THEN
            INSERT INTO arrecadacao.modelo_carne        VALUES (6, 'Carne IPTU', 'RCarneIptuPresidenteFigueiredo.class.php', 12, FALSE);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (6, 963 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (6, 964 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (6, 978 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (6, 979 );
    
            INSERT INTO arrecadacao.modelo_carne        VALUES (7, 'Carne ISS', 'RCarneIssPresidenteFigueiredo.class.php', 12, FALSE);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 963 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 964 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 978 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 979 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 1677);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 1678);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (7, 2240);
        END IF;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 5
     , 25
     , 8
     , 'Carne IPTU Presidente Figueiredo'
     , 'LHCarneIPTUPresidenteFigueiredo.php'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 5
                AND cod_modulo    = 25
                AND cod_relatorio = 8
           )
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 5
     , 25
     , 9
     , 'Carne ISS Presidente Figueiredo'
     , 'LHCarneISSPresidenteFigueiredo.php'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 5
                AND cod_modulo    = 25
                AND cod_relatorio = 9
           )
     ;


----------------
-- Ticket #24118
----------------

DROP   VIEW imobiliario.vw_matricula_imovel_atual;
ALTER TABLE imobiliario.matricula_imovel ALTER COLUMN mat_registro_imovel TYPE VARCHAR(20);
CREATE VIEW imobiliario.vw_matricula_imovel_atual
         AS SELECT matricula_imovel.inscricao_municipal
                 , matricula_imovel.timestamp
                 , matricula_imovel.mat_registro_imovel
                 , matricula_imovel.zona
              FROM imobiliario.matricula_imovel
                 , (
                       SELECT matricula_imovel.inscricao_municipal
                            , max(matricula_imovel.timestamp) AS timestamp
                         FROM imobiliario.matricula_imovel
                     GROUP BY matricula_imovel.inscricao_municipal
                   ) AS max_matricula_imovel
             WHERE matricula_imovel.inscricao_municipal = max_matricula_imovel.inscricao_municipal
               AND matricula_imovel.timestamp           = max_matricula_imovel.timestamp
                 ;
GRANT ALL ON imobiliario.vw_matricula_imovel_atual TO urbem;

