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
    * Função para busca de valor diverso para utilização no calculo das folhas
    * Data de Criação: 01/11/2007


    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.05.22

    $Id: recuperarValorDiverso.sql 31697 2008-08-04 19:33:31Z souzadl $

*/
CREATE OR REPLACE FUNCTION recuperarValorDiverso(INTEGER) RETURNS NUMERIC AS $$
DECLARE
    inCodValor      ALIAS FOR $1;
    nuValor         NUMERIC;
    stEntidade   VARCHAR:='';
    stSelect        VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stSelect := 'SELECT valor
                   FROM folhapagamento'||stEntidade||'.valor_diversos
                      , (  SELECT cod_valor
                                , max(timestamp) as timestamp
                             FROM folhapagamento'||stEntidade||'.valor_diversos
                         GROUP BY cod_valor) as max_valor_diversos
                  WHERE valor_diversos.cod_valor = max_valor_diversos.cod_valor
                    AND valor_diversos.timestamp = max_valor_diversos.timestamp
                    AND valor_diversos.ativo IS TRUE
                    AND valor_diversos.cod_valor = '||inCodValor;
    nuValor := selectIntoNumeric(stSelect);
    RETURN nuValor;
END;
$$ LANGUAGE 'plpgsql';
