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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_retorna_valor_IT.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/12/21 11:56:07  fabio
alterado nome das funcoes, em funcao da palavra "retorna" reservada pelo gerador de calculo

Revision 1.1  2006/12/20 10:46:17  fabio
funcoes para buscat o valor do imposto durante calculo de iptu, e nova funcao de multa p/ mata (2006 ->)


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_return_valor_IT() returns numeric AS '
DECLARE
    nuResultado     NUMERIC;
BEGIN

    SELECT ccor.valor
      INTO nuResultado
      FROM calculos_correntes AS ccor
           INNER JOIN arrecadacao.calculo as calc
                   ON calc.cod_calculo  = ccor.cod_calculo
                  AND calc.cod_credito  = 2
                  AND calc.cod_especie  = 1
                  AND calc.cod_genero   = 1
                  AND calc.cod_natureza = 1
    ;

    return coalesce(nuResultado,0.00);

END;
' LANGUAGE 'plpgsql';
