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
* $Id: buscaInscricaoLancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

DROP FUNCTION arrecadacao.buscaInscricaoLancamento( INTEGER );
CREATE OR REPLACE FUNCTION arrecadacao.buscaInscricaoLancamento( inCodLancamento    INTEGER
                                                               )  RETURNS           INTEGER AS '
DECLARE
    inInscricao     integer;
BEGIN

       SELECT CASE
                   WHEN imovel_calculo.inscricao_municipal             IS NOT NULL THEN
                        imovel_calculo.inscricao_municipal
                   WHEN cadastro_economico_calculo.inscricao_economica IS NOT NULL THEN
                        cadastro_economico_calculo.inscricao_economica
              END AS inscricao
         INTO inInscricao
         FROM arrecadacao.lancamento_calculo
    LEFT JOIN arrecadacao.imovel_calculo
           ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo
    LEFT JOIN arrecadacao.cadastro_economico_calculo
           ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo
        WHERE lancamento_calculo.cod_lancamento = inCodLancamento
            ;

    RETURN inInscricao;
END;
' LANGUAGE 'plpgsql';
