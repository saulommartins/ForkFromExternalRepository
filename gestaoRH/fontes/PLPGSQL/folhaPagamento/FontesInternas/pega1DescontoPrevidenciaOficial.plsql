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
* $Autor: MArcia $
* Date: 2006/04/19 10:50:00 $
*
* Caso de uso: uc-04.05.14
*
* Objetivo: recebe o valor da base a comparar. Utiliza buffers para obter 
* codigo da previdencia e o timestamp da data final da competencia.
* Recupera o percentual de desconto e calcula o valor do desconto da previdencia .
* Verificar se a base informada nao ultrapassa o teto da previdencia.
*/




CREATE OR REPLACE FUNCTION pega1DescontoPrevidenciaOficial(numeric) RETURNS numeric as '

DECLARE
    nuValorBase               ALIAS FOR $1;
    inCodPrevidencia          INTEGER;
    stDataFinalCompetencia    VARCHAR;

    nuValorBaseAux            NUMERIC := 0.00;
    nuTetoBaseDesconto        NUMERIC := 0.00;
    nuPercentualDesc          NUMERIC := 0.00;
    nuValorDesconto           NUMERIC := 0.00;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodPrevidencia := recuperarBufferInteiro(''inCodPrevidenciaOficial'');
    stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');
--    inCodPrevidencia := 1;
--    stDataFinalCompetencia := ''2004-10-31 23:59:59'';

    nuTetoBaseDesconto := pega0TetoDescPrevidenciaNaData(inCodPrevidencia,stDataFinalCompetencia);

    IF nuValorBase > nuTetoBaseDesconto THEN
         nuValorBaseAux := nuTetoBaseDesconto;    
    ELSE
         nuValorBaseAux := nuValorBase;    
    END if;

    nuPercentualDesc := pega0PercentualDescontoPrevidenciaNaData(inCodPrevidencia,stDataFinalCompetencia,nuValorBaseAux);

    nuValorDesconto := trunc( (nuValorBaseAux * nuPercentualDesc / 100) ,2);

    RETURN nuValorDesconto;
END;
' LANGUAGE 'plpgsql';

