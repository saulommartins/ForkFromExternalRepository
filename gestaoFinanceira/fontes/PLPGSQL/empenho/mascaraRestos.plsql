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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.03.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_mascara_restos(VARCHAR, VARCHAR) RETURNS VARCHAR AS '
DECLARE
   stMascara    ALIAS FOR $1;
   stValor      ALIAS FOR $2;
   inCountMas   integer := 1;
   inCountVal   integer := 1;
   inTamMasc    integer := 0;
   inTamVal    integer := 0;
   inCount      integer := 0;

   stMascaraa   varchar := '''';
   stValorr   varchar := '''';
   stOut        varchar := '''';
BEGIN
   --Inicializa nas variaveis as constates recebidas
   stMascaraa = stMascara || ''.'';
   stValorr = stValor;
   --Armazena o tamanho da mascara e do valor
   inTamMasc = length(stMascaraa);
   inTamVal = length(stValorr);

   --Lista todos os intervalos entre os pontos
   WHILE inCountVal <= publico.fn_strcount(stMascaraa,''.'') LOOP

      --Armazena a ocorrencia do primeiro ponto na mascara
      inCountMas = strpos(stMascaraa,''.'');

      --Armazena do valor a quantidade de caracteres encontrado nos intervalos de pontos da mascara
      stOut = stOut || substr(stValorr, 0, inCountMas) || ''.'';

      --Recorta o intervalo selecionado da mascara
      stMascaraa = substr(stMascaraa,inCountMas+1,inTamMasc);

      --Recorta o intervalo selecionado do valor
      stValorr = substr(stValorr,inCountMas,inTamVal);

      --Armazena o tamanho da mascara
      inTamMasc = length(stMascaraa);
   END LOOP;
   stOut = substr(stOut,0,length(stOut));

   RETURN stOut;
END;

'language 'plpgsql';
