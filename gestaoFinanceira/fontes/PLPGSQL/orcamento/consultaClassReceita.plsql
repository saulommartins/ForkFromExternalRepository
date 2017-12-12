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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.09, uc-02.01.10, uc-02.01.19
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_consulta_class_receita(INTEGER,VARCHAR,VARCHAR)
   RETURNS VARCHAR AS '

    DECLARE
        r_record          RECORD;

        v_codigo          VARCHAR;
        v_sql             VARCHAR;
        v_exercicio       VARCHAR;
        v_mascara         VARCHAR;

        i_codclass        INTEGER;
        i_codconta        INTEGER;

    BEGIN
        v_codigo = '''';
        v_mascara = $3;

        IF TRIM($1::varchar) <> ''0'' THEN

            v_exercicio := $2;
            i_codconta := $1;

            v_sql := ''
                SELECT
                    cd.cod_classificacao, cd.cod_conta
                FROM
                    orcamento.classificacao_receita cd
                WHERE
                    cd.cod_conta  = ''||i_codconta||'' AND
                    cd.exercicio  = ''||quote_literal(v_exercicio)||''
		ORDER BY
		    cd.cod_posicao
            '';

            FOR r_record IN EXECUTE v_sql LOOP
                v_codigo := v_codigo||''.''||r_record.cod_classificacao;
            END LOOP;

        END IF;

        v_codigo := SUBSTR(v_codigo,2,LENGTH(v_codigo));
        v_codigo := sw_fn_mascara_dinamica(v_mascara , v_codigo);


        RETURN v_codigo;

    END;

'language 'plpgsql';