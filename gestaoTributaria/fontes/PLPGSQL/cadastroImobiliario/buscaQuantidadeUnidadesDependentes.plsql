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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaQuantidadeUnidadesDependentes.plsql 64237 2015-12-21 17:43:02Z fabio $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION imobiliario.buscaQuantidadeUnidadesDependentes(integer) returns INTEGER AS '
DECLARE
    inImovel        ALIAS FOR $1;
    inQuantidade    INTEGER;
BEGIN
       SELECT COUNT(cod_construcao)
         INTO inQuantidade
         FROM imobiliario.unidade_dependente
        WHERE inscricao_municipal = inImovel
          AND cod_construcao NOT IN (
                                      SELECT cod_construcao
                                        FROM (
                                                 SELECT MAX (TIMESTAMP) AS TIMESTAMP
                                                      , cod_construcao
                                                      , dt_termino
                                                   FROM imobiliario.baixa_unidade_dependente
                                               GROUP BY cod_construcao
                                                      , dt_termino
                                             ) AS BT
                                       WHERE BT.dt_termino IS NULL
                                    );

RETURN inQuantidade;

END;
'LANGUAGE 'plpgsql';
