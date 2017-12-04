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
  CREATE TYPE participante_documentos AS (
    cod_licitacao       INTEGER,
    cod_documento       INTEGER,
    dt_validade         DATE,
    cgm_fornecedor      INTEGER,
    cod_modalidade      INTEGER,
    cod_entidade        INTEGER,
    exercicio           CHARACTER(4),
    num_documento       CHARACTER VARYING (30),
    dt_emissao          DATE,
    timestamp           TIMESTAMP
  );
*/

CREATE OR REPLACE FUNCTION fn_recupera_participante_documentos (VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF participante_documentos AS $$

DECLARE
    stExercicio           ALIAS FOR $1;
    stCodEntidades        ALIAS FOR $2;
    stFiltro              ALIAS FOR $3;

    reRegistro            RECORD;
    stSql                 VARCHAR := '';

BEGIN
    stSql := '
                  SELECT participante_documentos.*
                    FROM licitacao.participante_documentos
              INNER JOIN licitacao.licitacao_documentos
                      ON participante_documentos.cod_documento = licitacao_documentos.cod_documento
                     AND participante_documentos.cod_licitacao = licitacao_documentos.cod_licitacao
                     AND participante_documentos.cod_modalidade = licitacao_documentos.cod_modalidade
                     AND participante_documentos.cod_entidade = licitacao_documentos.cod_entidade
                     AND participante_documentos.exercicio = licitacao_documentos.exercicio
              INNER JOIN licitacao.documento
                      ON documento.cod_documento = licitacao_documentos.cod_documento
                   WHERE participante_documentos.exercicio = ''' || stExercicio || '''
                     AND participante_documentos.cod_entidade IN (' || stCodEntidades || ')
                     ';
              IF stFiltro <> '' THEN stSql := stSql || ' AND ' || stFiltro || '';
              END IF;

    FOR reRegistro IN EXECUTE stSql
    LOOP
      RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';