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
* $Id: buscaTipoInscricaoLancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2006/10/02 15:16:02  dibueno
*** empty log message ***

Revision 1.1  2006/09/29 11:14:24  dibueno
Funcao para busca do tipo de inscricao do lançamento, se é IM ou IE

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaTipoInscricaoLancamento( inCodLancamento    INTEGER
                                                                   )  RETURNS           VARCHAR AS $$
DECLARE
    stTipo                  VARCHAR;
BEGIN

       SELECT CASE
                   WHEN imovel_calculo.inscricao_municipal             IS NOT NULL THEN
                        'IM'
                   WHEN cadastro_economico_calculo.inscricao_economica IS NOT NULL THEN
                        'IE'
                   ELSE
                        'CGM'
              END AS tipo_inscricao
         INTO stTipo
         FROM arrecadacao.lancamento_calculo
    LEFT JOIN arrecadacao.imovel_calculo
           ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo
    LEFT JOIN arrecadacao.cadastro_economico_calculo
           ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo
        WHERE lancamento_calculo.cod_lancamento = inCodLancamento
            ;

    RETURN stTipo;
END;
$$ LANGUAGE 'plpgsql';
