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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/31 10:50:00 $
*
* Caso de uso: uc-04.05.15
* Caso de uso: uc-04.05.48
*
* Objetivo: a partir do buffer relativo a competencia e ao codigo da 
* regime previdenciario fixado em 2 (RPPS ) obtem o valor do salario familia 
* por dependente
* Uso especifico para o calculo da diferenca de salario familia de estatutarios 
* com previdencia inss e que tenham diferencas de valores.
*/



CREATE OR REPLACE FUNCTION pega1ValorSalarioFamiliaEstatutario(numeric) RETURNS numeric as '

DECLARE
    nuValorBase               ALIAS FOR $1;
    nuValorSalarioFamilia     NUMERIC := 0.00;

    inCodRegimePrevidencia    INTEGER := 2;
    stDataFinalCompetencia    VARCHAR;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


  stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');

  nuValorSalarioFamilia := selectIntoNumeric('' 
       (SELECT vl_pagamento
        FROM  folhapagamento''||stEntidade||''.salario_familia as sf

           LEFT OUTER JOIN 
             ( SELECT vl_pagamento, cod_regime_previdencia, timestamp
                FROM folhapagamento''||stEntidade||''.faixa_pagamento_salario_familia 
                WHERE nuValorBase between vl_inicial AND vl_final
             ) as fpsf
             ON sf.cod_regime_previdencia = fpsf.cod_regime_previdencia
            AND sf.timestamp = fpsf.timestamp

         WHERE sf.cod_regime_previdencia  = ''||inCodRegimePrevidencia||''
           AND sf.vigencia               <= ''''''||stDataFinalCompetencia||''''''
         ORDER by sf.timestamp desc
         LIMIT 1 '') ;

     IF nuValorSalarioFamilia is null THEN
        nuValorSalarioFamilia := 0.00;
     END IF;

   RETURN nuValorSalarioFamilia;

END;
' LANGUAGE 'plpgsql';

