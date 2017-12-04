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

CREATE OR REPLACE FUNCTION busca_primeira_reemissao( inCodParcela   INTEGER
                                                   ) RETURNS        DATE    AS $$
DECLARE
    dtVencimentoOriginal    DATE;
BEGIN
        SELECT parcela_reemissao.vencimento
          INTO dtVencimentoOriginal
          FROM arrecadacao.parcela_reemissao
    INNER JOIN (
                   SELECT MIN(parcela_reemissao.timestamp) AS timestamp
                        , parcela_reemissao.cod_parcela
                     FROM arrecadacao.parcela_reemissao
                    WHERE parcela_reemissao.cod_parcela = inCodParcela
                 GROUP BY parcela_reemissao.cod_parcela
               ) AS apr
            ON apr.timestamp   = parcela_reemissao.timestamp
           and apr.cod_parcela = parcela_reemissao.cod_parcela
             ;
    RETURN dtVencimentoOriginal;

END;
$$ LANGUAGE 'plpgsql';

