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
* Casos de uso: uc-02.01.13, uc-02.01.17
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_consulta_despesa(VARCHAR,INTEGER,BOOLEAN) RETURNS VARCHAR AS $$

DECLARE
    r_record          RECORD;

    v_codigo          VARCHAR;
    v_sql             VARCHAR;
    v_exercicio       VARCHAR;
    v_mascara         VARCHAR;
    v_mascara_des     VARCHAR;
    v_class_despesa   VARCHAR;
    v_mascara_class_despesa VARCHAR;

    i_codclass        INTEGER;
    i_coddespesa      INTEGER;
    inCount           INTEGER;
    i_cod_conta       INTEGER;
    i_num_orgao       INTEGER;
    i_num_unidade     INTEGER;
    i_cod_funcao      INTEGER;
    i_cod_subfuncao   INTEGER;
    i_cod_programa    INTEGER;
    i_num_acao        INTEGER;
    i_num_programa    INTEGER;
    i_num_pao         INTEGER;
    i_mascara         INTEGER;

    b_parametro       BOOLEAN;

    a_mascara         VARCHAR[];

BEGIN
    v_codigo = '';


        v_exercicio     := $1;
        i_coddespesa    := $2;
        b_parametro     := $3;

        SELECT INTO
                v_mascara
                administracao.configuracao.valor
        FROM    administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
        AND     administracao.configuracao.parametro = 'masc_despesa'
        AND     administracao.configuracao.exercicio = v_exercicio;

        a_mascara := string_to_array(v_mascara,'.');
        i_mascara := publico.fn_countelements(v_mascara,'.') - 1;
        v_mascara_des := '';
        FOR inCount IN 1..i_mascara LOOP
            v_mascara_des := v_mascara_des || '.' || a_mascara[inCount];
        END LOOP;
        v_mascara_des := SUBSTR(v_mascara_des,2,LENGTH(v_mascara_des));


        SELECT INTO
           i_cod_conta, i_num_orgao, i_num_unidade, i_cod_funcao, i_cod_subfuncao, i_cod_programa, i_num_pao, i_num_acao, i_num_programa
           cod_conta, num_orgao, num_unidade, cod_funcao, cod_subfuncao, despesa.cod_programa, despesa.num_pao, ppa.acao.num_acao, ppa.programa.num_programa
        FROM
            orcamento.despesa
            JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.cod_programa = despesa.cod_programa
             AND programa_ppa_programa.exercicio   = despesa.exercicio
            JOIN ppa.programa
              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            JOIN orcamento.pao_ppa_acao
              ON pao_ppa_acao.num_pao = despesa.num_pao
             AND pao_ppa_acao.exercicio = despesa.exercicio
            JOIN ppa.acao 
              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
        WHERE
            cod_despesa  = i_coddespesa AND
            despesa.exercicio    = v_exercicio;

        v_codigo := sw_fn_mascara_dinamica( v_mascara_des,
                                            i_num_orgao
                                            ||'.'||i_num_unidade
                                            ||'.'||i_cod_funcao
                                            ||'.'||i_cod_subfuncao
                                            ||'.'||i_num_programa
                                            ||'.'||i_num_acao ) ;
        SELECT INTO
                v_mascara_class_despesa
                administracao.configuracao.valor
        FROM    administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
        AND     administracao.configuracao.parametro = 'masc_class_despesa'
        AND     administracao.configuracao.exercicio = v_exercicio;

        v_class_despesa := orcamento.fn_consulta_class_despesa(i_cod_conta, v_exercicio, v_mascara_class_despesa );

        IF b_parametro THEN
            v_codigo := v_codigo ||'.'|| v_class_despesa;
        ELSE
            v_codigo := v_codigo ||'.'|| replace(v_class_despesa, '.', '');
        END IF;


    RETURN v_codigo;

END;

$$language 'plpgsql';