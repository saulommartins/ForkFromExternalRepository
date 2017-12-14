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
CREATE OR REPLACE FUNCTION pega0DataFinalCompetenciaDoPeriodoMovimento(integer) RETURNS varchar as $$

DECLARE

    inCodPeriodoMovimentacao          ALIAS FOR $1;
    dtTimestamp                       timestamp;
    stTimestamp                       VARCHAR;
    stAno                             VARCHAR;
    stMes                             VARCHAR;
    stDia                             VARCHAR;
    stAnoMes                          VARCHAR;
    stHora                            VARCHAR:=' 23:59:59' ;

stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN

   dtTimestamp := selectIntoVarchar('

   SELECT  folhapagamento'||stEntidade||'.periodo_movimentacao.dt_final                                       

   FROM                                                               
       folhapagamento'||stEntidade||'.periodo_movimentacao
                       
   WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao
          ) ;

   stTimestamp := substr(dtTimestamp::varchar,1,10);
   stAnoMes    := substr(dtTimestamp::varchar,1,8);
   stAno       := substr( dtTimestamp::varchar,1,4);
   stMes       := substr( dtTimestamp::varchar,6,2);
   stDia       := calculaNrDiasAnoMes(stAno::integer,stMes::integer);
   
   stTimestamp = stAnoMes||stDia||' '||stHora;

   RETURN stTimestamp;
END;
$$ LANGUAGE 'plpgsql';

