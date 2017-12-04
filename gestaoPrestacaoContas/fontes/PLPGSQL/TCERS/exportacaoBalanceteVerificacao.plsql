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
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.9  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.8  2006/07/17 14:21:32  cako
Bug #6013#

Revision 1.7  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_exportacao_balancete_verificacao(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN

    stSql := 'SELECT * FROM ( 
            SELECT
            tabela.cod_estrutural
            , case when pce.cod_entidade is not null
                        then pce.cod_entidade
                   else pba.cod_entidade
              end as cod_entidade
            , nivel
            , tabela.nom_conta
            , vl_saldo_anterior
            , vl_saldo_debitos
            , vl_saldo_creditos
            , vl_saldo_atual 
            ,contabilidade.fn_tipo_conta_plano(pc.exercicio, tabela.cod_estrutural) as tipo_conta
            , sc.nom_sistema
            ,escrituracao
            ,tabela.indicador_superavit
            FROM contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||'
                                                            ,'|| quote_literal(stFiltro) ||'
                                                            ,'|| quote_literal(stDtInicial) ||'
                                                            ,'|| quote_literal(stDtFinal) ||'
                                                            , ''A''::char)
                 as tabela (cod_estrutural VARCHAR
                            , nivel INTEGER
                            , nom_conta VARCHAR
                            , cod_sistema INTEGER
                            , indicador_superavit CHAR(12)
                            , vl_saldo_anterior NUMERIC
                            , vl_saldo_debitos NUMERIC
                            , vl_saldo_creditos NUMERIC
                            , vl_saldo_atual NUMERIC
                            ) 
            ,contabilidade.plano_conta as pc 
            LEFT JOIN ( 
                select
                    pb.cod_entidade,               
                    pa.cod_conta,               
                    pa.exercicio               
                from               
                    contabilidade.plano_banco as pb,               
                    contabilidade.plano_analitica as pa               
                where               
                    pb.cod_plano    = pa.cod_plano AND               
                    pb.exercicio    = pa.exercicio               
            ) as pba ON (   pc.cod_conta   = pba.cod_conta AND               
                            pc.exercicio   = pba.exercicio )               
            LEFT JOIN  tcers.plano_conta_entidade as pce               
                    ON (   pc.cod_conta   = pce.cod_conta AND               
                            pc.exercicio   = pce.exercicio ),               
            contabilidade.sistema_contabil as sc 
            WHERE '|| quote_literal(stExercicio) ||' = pc.exercicio
            AND tabela.cod_estrutural = pc.cod_estrutural
            AND pc.exercicio = sc.exercicio
            AND pc.cod_sistema = sc.cod_sistema 
            ) AS tabela WHERE cod_entidade IS NULL OR '||stFiltro
    ;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
