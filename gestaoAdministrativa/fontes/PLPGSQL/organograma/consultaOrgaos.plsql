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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28841 $
* $Name$
* $Author: rodrigosoares $
* $Date: 2008-03-28 09:44:54 -0300 (Sex, 28 Mar 2008) $
*
* Casos de uso: uc-01.05.02
*/

--- Gris - 18/10/2004
    -- Criada a funcao - organograma.fn_consulta_orgao(INTEGER,INTEGER) - Solicitacao Diego Barbosa.
CREATE OR REPLACE FUNCTION organograma.fn_consulta_orgao(INTEGER,INTEGER)
RETURNS VARCHAR AS '

    DECLARE
        r_record          RECORD;

        v_codigo          VARCHAR;
        v_sql             VARCHAR;

        i_codOrganograma  INTEGER;
        i_codOrgao        INTEGER;

    BEGIN
        v_codigo = '''';
       
        IF TRIM(cast($1 as varchar)) <> ''0'' THEN

            i_codOrgao       := $2;
            i_codOrganograma := $1;

            v_sql := ''
                SELECT
                    o.valor, ni.mascaracodigo
                FROM
                     organograma.orgao_nivel as o
                    ,organograma.nivel as ni
                WHERE o.cod_organograma = ni.cod_organograma
                AND o.cod_nivel = ni.cod_nivel
                AND o.cod_organograma  = ''||i_codOrganograma||''
                AND o.cod_orgao  = ''||i_codOrgao||''
                ORDER BY o.cod_nivel
                '';
                                             
            FOR r_record IN EXECUTE v_sql LOOP
                --v_codigo := v_codigo||''.''||publico.fn_mascara_dinamica ( ( case when r_record.mascaracodigo = '''' then ''0'' else r_record.mascaracodigo end ) , r_record.valor);
					 v_codigo := v_codigo||''.''||sw_fn_mascara_dinamica ( ( case when r_record.mascaracodigo = '''' then ''0'' else r_record.mascaracodigo end ) , r_record.valor);
            END LOOP;

        END IF;

        v_codigo := SUBSTR(v_codigo,2,LENGTH(v_codigo));

        RETURN v_codigo;

    END;

'language 'plpgsql';
