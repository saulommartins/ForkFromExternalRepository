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
/**
    * Ultimo Timestamp do Periodo de Movimentação
    * Data de Criação: 31/03/2009
    
    
    * @author Analista: Dagiane	Vieira	
    * @author Desenvolvedor: <Alex Cardoso>
    
    * @ignore

    $Id: $
    
    * Casos de uso: uc-04.05.00
*/

CREATE OR REPLACE FUNCTION ultimoTimestampPeriodoMovimentacao(INTEGER, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodPeriodoMovimentacao            ALIAS FOR $1;
    stEntidade                          ALIAS FOR $2;
    stSql                               VARCHAR;
    stTimestamp                         VARCHAR;
BEGIN
    IF inCodPeriodoMovimentacao = 0 THEN
        stTimestamp := now()::timestamp(3)::VARCHAR;
    ELSE
        stSql := 'SELECT max(cod_periodo_movimentacao) FROM folhapagamento'||stEntidade||'.periodo_movimentacao';
        
        IF inCodPeriodoMovimentacao < selectIntoInteger(stSql) THEN
            stSql := 'SELECT max(timestamp)::varchar
                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao 
                       WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
            stTimestamp := selectIntoVarchar(stSql);
        ELSE
            stTimestamp := now()::timestamp(3)::VARCHAR;
        END IF;
    END IF;

    RETURN stTimestamp;
END
$$ LANGUAGE plpgsql;