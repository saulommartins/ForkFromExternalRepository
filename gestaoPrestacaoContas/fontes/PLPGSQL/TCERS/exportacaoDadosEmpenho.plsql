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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.01
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_dados_empenho(integer,varchar,integer) RETURNS VARCHAR[] AS $$
DECLARE
    inCodEmpenho        ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inCodEntidade       ALIAS FOR $3;
    boImplantado        BOOLEAN     := false;
    stSql               VARCHAR     :=  '';
    stOut               VARCHAR     :=  '';
    arRetorno           VARCHAR[]   := ARRAY[0] ;
    arRetorno1          VARCHAR     := '';
    arRetorno2          VARCHAR     := '';
    arRetorno3          VARCHAR     := '';
    arRetorno4          VARCHAR     := '';
    arRetorno5          VARCHAR     := '';
    arRetorno6          VARCHAR     := '';
    arRetorno7          VARCHAR     := '';
    arRetorno8          VARCHAR     := '';
BEGIN

    SELECT  pre.implantado
    INTO boImplantado
    FROM    empenho.pre_empenho as pre  ,
            empenho.empenho     as ee
    WHERE   ee.exercicio        =   stExercicio
        AND ee.cod_empenho      =   inCodEmpenho
        AND ee.cod_entidade     =   inCodEntidade
        AND pre.exercicio       =   ee.exercicio
        AND pre.cod_pre_empenho =   ee.cod_pre_empenho;

       IF  boImplantado = true THEN
                            SELECT
                                    cast (rpe.num_orgao     as varchar) ,
                                    cast (rpe.num_unidade   as varchar) ,
                                    cast (rpe.cod_funcao    as varchar) ,
                                    cast (rpe.cod_subfuncao as varchar) ,
                                    cast (rpe.cod_programa  as varchar) ,
                                    cast (rpe.num_pao       as varchar) ,
                                    cast (rpe.cod_estrutural as varchar),
                                    cast (rpe.recurso as varchar) as cod_recurso
                                    INTO    arRetorno1,
                                            arRetorno2,
                                            arRetorno3,
                                            arRetorno4,
                                            arRetorno5,
                                            arRetorno6,
                                            arRetorno7,
                                            arRetorno8
                            FROM    empenho.restos_pre_empenho  as rpe  ,
                                    empenho.pre_empenho         as epre ,
                                    empenho.empenho             as ee
                            WHERE   ee.exercicio        =   stExercicio
                                AND ee.cod_empenho      =   inCodEmpenho
                                AND ee.cod_entidade     =   inCodEntidade
                                AND epre.exercicio      =   ee.exercicio
                                AND epre.cod_pre_empenho=   ee.cod_pre_empenho
                                AND rpe.exercicio       =   epre.exercicio
                                AND rpe.cod_pre_empenho =   epre.cod_pre_empenho
                            ORDER BY cod_estrutural;
        ELSE
                            SELECT CAST (despesa.num_orgao     AS VARCHAR)
                                 , CAST (despesa.num_unidade   AS VARCHAR)
                                 , CAST (despesa.cod_funcao    AS VARCHAR)
                                 , CAST (despesa.cod_subfuncao AS VARCHAR)
                                 , CASE WHEN programa.num_programa IS NOT NULL THEN
                                        CAST (programa.num_programa AS VARCHAR)
                                   ELSE
                                        cast (despesa.cod_programa AS VARCHAR)
                                   END AS cod_programa
                                 , CASE WHEN acao.num_acao IS NOT NULL THEN
                                        CAST (acao.num_acao AS VARCHAR)
                                   ELSE
                                        CAST (despesa.num_pao AS VARCHAR)
                                   END AS num_pao
                                 , CAST (conta_despesa.cod_estrutural AS VARCHAR)
                                 , CAST (despesa.cod_recurso AS VARCHAR)
                              INTO arRetorno1
                                 , arRetorno2
                                 , arRetorno3
                                 , arRetorno4
                                 , arRetorno5
                                 , arRetorno6
                                 , arRetorno7
                                 , arRetorno8
                              FROM empenho.empenho
                              JOIN empenho.pre_empenho
                                ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                               AND pre_empenho.exercicio       = empenho.exercicio
                              JOIN empenho.pre_empenho_despesa
                                ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                              JOIN orcamento.despesa
                                ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                               AND despesa.exercicio   = pre_empenho_despesa.exercicio
                              JOIN orcamento.conta_despesa
                                ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                               AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                         LEFT JOIN orcamento.despesa_acao
                                ON despesa_acao.exercicio_despesa = despesa.exercicio
                               AND despesa_acao.cod_despesa       = despesa.cod_despesa
                         LEFT JOIN ppa.acao
                                ON acao.cod_acao = despesa_acao.cod_acao
                         LEFT JOIN ppa.programa
                                ON programa.cod_programa = acao.cod_programa
                             WHERE empenho.exercicio            =   stExercicio
                               AND empenho.cod_empenho          =   inCodEmpenho
                               AND empenho.cod_entidade         =   inCodEntidade
                          ORDER BY conta_despesa.cod_estrutural;
       END IF;
       arRetorno[1] := arRetorno1;
       arRetorno[2] := arRetorno2;
       arRetorno[3] := arRetorno3;
       arRetorno[4] := arRetorno4;
       arRetorno[5] := arRetorno5;
       arRetorno[6] := arRetorno6;
       arRetorno[7] := cast(arRetorno7 as varchar);
       arRetorno[8] := arRetorno8;
       RETURN arRetorno;
END;
$$ LANGUAGE 'plpgsql';

