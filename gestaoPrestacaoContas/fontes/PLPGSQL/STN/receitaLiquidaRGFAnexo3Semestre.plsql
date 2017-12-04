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
* Casos de uso: uc-06.01.22
*/

/*
$Log$
Revision 1.1  2006/09/26 10:15:54  cleisson
Inclusão

Revision 1.1  2006/08/04 17:54:38  jose.eduardo
Inclusao


*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_receita_liquida_anexo3_semestre (varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stSql               VARCHAR   := '';
    stExercicioAnterior VARCHAR   := '';
    reRegistro          RECORD;
    dtAnoAnterior       DATE;
BEGIN

    dtAnoAnterior := TO_DATE(stExercicio, 'yyyy') - 1;
    stExercicioAnterior := TO_CHAR(dtAnoAnterior,'yyyy');

stSql := '
    select 
         cast(''RECEITA CORRENTE LÍQUIDA - RCL'' as varchar) as descricao
        ,cast ( (SELECT
               sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                         + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) 
          FROM stn.pl_total_subcontas (''31/12/'||stExercicioAnterior||''') as retorno (
                                                                       ordem      integer
                                                                      ,cod_conta      varchar
                                                                      ,nom_conta      varchar
                                                                      ,cod_estrutural varchar
                                                                      ,mes_1      numeric
                                                                      ,mes_2      numeric
                                                                      ,mes_3      numeric
                                                                      ,mes_4      numeric
                                                                      ,mes_5      numeric
                                                                      ,mes_6      numeric
                                                                      ,mes_7      numeric
                                                                      ,mes_8      numeric
                                                                      ,mes_9      numeric
                                                                      ,mes_10     numeric
                                                                      ,mes_11     numeric
                                                                      ,mes_12     numeric
                                                                      ,total_mes_1  numeric
                                                                      ,total_mes_2  numeric
                                                                      ,total_mes_3  numeric
                                                                      ,total_mes_4  numeric
                                                                      ,total_mes_5  numeric
                                                                      ,total_mes_6  numeric
                                                                      ,total_mes_7  numeric
                                                                      ,total_mes_8  numeric
                                                                      ,total_mes_9  numeric
                                                                      ,total_mes_10 numeric
                                                                      ,total_mes_11 numeric
                                                                      ,total_mes_12 numeric)
         WHERE ordem = 1) as numeric ) as saldo_exercicio_anterior
        ,cast ( (SELECT
               sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                         + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) 
          FROM stn.pl_total_subcontas (''30/06/'||stExercicio||''') as retorno (
                                                                       ordem      integer
                                                                      ,cod_conta      varchar
                                                                      ,nom_conta      varchar
                                                                      ,cod_estrutural varchar
                                                                      ,mes_1      numeric
                                                                      ,mes_2      numeric
                                                                      ,mes_3      numeric
                                                                      ,mes_4      numeric
                                                                      ,mes_5      numeric
                                                                      ,mes_6      numeric
                                                                      ,mes_7      numeric
                                                                      ,mes_8      numeric
                                                                      ,mes_9      numeric
                                                                      ,mes_10     numeric
                                                                      ,mes_11     numeric
                                                                      ,mes_12     numeric
                                                                      ,total_mes_1  numeric
                                                                      ,total_mes_2  numeric
                                                                      ,total_mes_3  numeric
                                                                      ,total_mes_4  numeric
                                                                      ,total_mes_5  numeric
                                                                      ,total_mes_6  numeric
                                                                      ,total_mes_7  numeric
                                                                      ,total_mes_8  numeric
                                                                      ,total_mes_9  numeric
                                                                      ,total_mes_10 numeric
                                                                      ,total_mes_11 numeric
                                                                      ,total_mes_12 numeric)
         WHERE ordem = 1) as numeric ) as saldo_primeiro_semestre
        ,cast ( (SELECT
               sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6
                         + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) 
          FROM stn.pl_total_subcontas (''31/12/'||stExercicio||''') as retorno (
                                                                       ordem      integer
                                                                      ,cod_conta      varchar
                                                                      ,nom_conta      varchar
                                                                      ,cod_estrutural varchar
                                                                      ,mes_1      numeric
                                                                      ,mes_2      numeric
                                                                      ,mes_3      numeric
                                                                      ,mes_4      numeric
                                                                      ,mes_5      numeric
                                                                      ,mes_6      numeric
                                                                      ,mes_7      numeric
                                                                      ,mes_8      numeric
                                                                      ,mes_9      numeric
                                                                      ,mes_10     numeric
                                                                      ,mes_11     numeric
                                                                      ,mes_12     numeric
                                                                      ,total_mes_1  numeric
                                                                      ,total_mes_2  numeric
                                                                      ,total_mes_3  numeric
                                                                      ,total_mes_4  numeric
                                                                      ,total_mes_5  numeric
                                                                      ,total_mes_6  numeric
                                                                      ,total_mes_7  numeric
                                                                      ,total_mes_8  numeric
                                                                      ,total_mes_9  numeric
                                                                      ,total_mes_10 numeric
                                                                      ,total_mes_11 numeric
                                                                      ,total_mes_12 numeric)
         WHERE ordem = 1) as numeric ) as saldo_segundo_semestre

';

    
    FOR reRegistro IN EXECUTE stSql
    LOOP
       RETURN next reRegistro;        
    END LOOP;
   
RETURN;

END;
$$language 'plpgsql';
