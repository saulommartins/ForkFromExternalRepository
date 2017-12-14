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
--/**
--    * Função PLSQL
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION pegaFormulaEvento(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS VARCHAR AS $$
DECLARE
    inCodEvento         ALIAS FOR $1;
    inCodConfiguracao   ALIAS FOR $2;
    inCodSubDivisao     ALIAS FOR $3;
    inCodCargo          ALIAS FOR $4;
    inCodEspecialidade  ALIAS FOR $5;
    stFormulaEvento     VARCHAR;
    stNatureza          VARCHAR;
    stEntidade       VARCHAR;
    stSql               VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stNatureza := recuperarBufferTexto('stNatureza');
    
    IF stNatureza = 'B' THEN
        stSql := 'SELECT cod_modulo||''.''||cod_biblioteca||''.''||cod_funcao 
                    FROM folhapagamento'||stEntidade||'.configuracao_evento_caso 
                   WHERE cod_evento = '||inCodEvento||'
                     AND cod_configuracao = '||inCodConfiguracao||'
                ORDER BY timestamp desc LIMIT 1';
        stFormulaEvento := selectIntoVarchar(stSql);

    ELSE                             
        stSql := 'SELECT cod_modulo ||''.''|| cod_biblioteca ||''.''|| cod_funcao
          FROM folhapagamento'||stEntidade||'.configuracao_evento_caso_cargo         AS car
             , folhapagamento'||stEntidade||'.configuracao_evento_caso               AS cas
             , (  SELECT cod_evento
                       , max(timestamp) AS timestamp
                    FROM folhapagamento'||stEntidade||'.configuracao_evento_caso
                GROUP BY cod_evento)                                 AS max_cas
             , folhapagamento'||stEntidade||'.configuracao_evento_caso_sub_divisao   AS sub
         WHERE sub.cod_caso         = cas.cod_caso
           AND sub.cod_evento       = cas.cod_evento
           AND sub.timestamp        = cas.timestamp
           AND sub.cod_configuracao = cas.cod_configuracao
           AND cas.cod_evento       = max_cas.cod_evento
           AND cas.timestamp        = max_cas.timestamp
           AND cas.cod_evento       = max_cas.cod_evento
           AND cas.timestamp        = max_cas.timestamp
           AND cas.cod_caso         = car.cod_caso
           AND cas.cod_evento       = car.cod_evento
           AND cas.timestamp        = car.timestamp
           AND cas.cod_configuracao = car.cod_configuracao
           AND car.cod_evento       = '||inCodEvento||'
           AND car.cod_configuracao = '||inCodConfiguracao||'
           AND sub.cod_sub_divisao  = '||inCodSubDivisao||'
           AND car.cod_cargo        = '||inCodCargo;                              
        stFormulaEvento := selectIntoVarchar(stSql);
    END IF;

    RETURN stFormulaEvento;
END;
$$ LANGUAGE 'plpgsql';

