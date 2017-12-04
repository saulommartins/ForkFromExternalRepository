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
* $Revision: 13028 $
* $Name$
* $Author: diego $
* $Date: 2006-07-20 11:25:24 -0300 (Qui, 20 Jul 2006) $
*
* Casos de uso: uc-03.03.05
*/

/*
$Log$
Revision 1.10  2006/07/20 14:25:24  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:11:41  diego


*/

CREATE OR REPLACE FUNCTION almoxarifado.fn_retorna_proximo_estrutural_livre(INTEGER, INTEGER, VARCHAR)
RETURNS INTEGER AS $$

DECLARE

inCodCatalogo         ALIAS FOR $1;
inNivel               ALIAS FOR $2;
stCodEstruturalMae    ALIAS FOR $3;

stSql                 VARCHAR;
inMaxNivel            INTEGER;

BEGIN

-- PEGA O MAIOR NIVEL, APARTIR DO NIVEL QUE FOI SELECIONADO PARA SER INSERIDAS NOVAS CLASSIFICAÇÕES

      SELECT  COALESCE(( SELECT MAX(cod_nivel)                     
                  FROM almoxarifado.catalogo_classificacao 
            INNER JOIN almoxarifado.classificacao_nivel 
                    ON classificacao_nivel.cod_catalogo = catalogo_classificacao.cod_catalogo  
                   AND classificacao_nivel.cod_classificacao = catalogo_classificacao.cod_classificacao 
                 WHERE classificacao_nivel.cod_catalogo = inCodCatalogo
                   AND nivel = inNivel 
                   AND cod_estrutural like (SELECT publico.fn_mascarareduzida(stCodEstruturalMae)||'%') ), 0)
               into inMaxNivel;

RETURN inMaxNivel+1;

END;

$$ LANGUAGE 'plpgsql';
