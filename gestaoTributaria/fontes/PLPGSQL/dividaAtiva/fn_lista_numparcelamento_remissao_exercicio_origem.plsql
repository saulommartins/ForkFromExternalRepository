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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
*/
--lista todos exercicios originais das inscricoes em divida do imovel que deveriam ser da mesma remissao
CREATE OR REPLACE FUNCTION fn_lista_numparcelamento_remissao_exercicio_origem(integer) RETURNS varchar as $$
DECLARE
    inNumParcelamento       ALIAS FOR $1;
    stRetorno               VARCHAR;
    inTipo                  INTEGER;
    inInscricao             INTEGER;
    dtData                  DATE;
    reRegistro              RECORD;
    stSql                   VARCHAR;

BEGIN

    SELECT divida_remissao.dt_remissao
         , CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
              1
           ELSE
              2
           END
         , COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica )
      INTO dtData
         , inTipo
         , inInscricao
      FROM divida.divida_parcelamento
INNER JOIN divida.divida_remissao
        ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
       AND divida_remissao.exercicio = divida_parcelamento.exercicio
 LEFT JOIN divida.divida_imovel
        ON divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
       AND divida_imovel.exercicio = divida_parcelamento.exercicio
 LEFT JOIN divida.divida_empresa
        ON divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
       AND divida_empresa.exercicio = divida_parcelamento.exercicio
     WHERE divida_parcelamento.num_parcelamento = inNumParcelamento;

    stSql := '
        SELECT DISTINCT divida_ativa.exercicio_original
          FROM divida.divida_parcelamento
    INNER JOIN divida.divida_remissao
            ON divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
           AND divida_remissao.exercicio = divida_parcelamento.exercicio
    INNER JOIN divida.divida_ativa
            ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
           AND divida_ativa.exercicio = divida_parcelamento.exercicio
     LEFT JOIN divida.divida_imovel
            ON divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
           AND divida_imovel.exercicio = divida_parcelamento.exercicio
     LEFT JOIN divida.divida_empresa
            ON divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
           AND divida_empresa.exercicio = divida_parcelamento.exercicio
         WHERE divida_remissao.dt_remissao = '|| quote_literal(dtData) ||' AND ';

    IF ( inTipo = 1 ) THEN
        stSql := stSql ||' divida_imovel.inscricao_municipal = '|| inInscricao;
    ELSE
        stSql := stSql ||' divida_empresa.inscricao_economica = '|| inInscricao;
    END IF;

    stRetorno := '';
    FOR reRegistro IN EXECUTE stSql LOOP
        if ( stRetorno != '' ) THEN
            stRetorno := stRetorno ||',';
        END IF;

        stRetorno := stRetorno||reRegistro.exercicio_original;
    END LOOP;

    RETURN stRetorno;

END;
$$language 'plpgsql';
