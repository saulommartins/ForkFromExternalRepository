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
    * Titulo do arquivo PL que retorna o num_orgao de acordo com o órgão passado
    * Data de Criação   : 29/12/2008


    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
 */

CREATE OR REPLACE FUNCTION orcamento.recuperaEstruturalDespesa(INTEGER, VARCHAR, INTEGER, BOOLEAN, BOOLEAN) RETURNS VARCHAR AS $$
DECLARE
    inCodConta          ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inTamanho           ALIAS FOR $3;
    boSubElemento       ALIAS FOR $4;
    boPonto             ALIAS FOR $5;

    stMascaraEstrutural VARCHAR   := '';
    stCodEstrutural     VARCHAR   := '';
    stCodRetorno        VARCHAR   := '';
    arValor             VARCHAR[];
    inIndex             INTEGER := 0;
    boLaco              BOOLEAN := TRUE;
BEGIN

    -- Busca o codigo estrutural da conta informada
    SELECT cod_estrutural
      INTO stCodEstrutural
      FROM orcamento.conta_despesa 
     WHERE cod_conta = inCodConta
       AND exercicio = stExercicio;


    arValor := string_to_array(stCodEstrutural, '.');
    
    WHILE (boLaco) LOOP
        inIndex := inIndex + 1;
        IF (arValor[inIndex] <> '') THEN
            IF (boPonto = TRUE) THEN
                stMascaraEstrutural := stMascaraEstrutural||'.'||arValor[inIndex];
            ELSE
                stMascaraEstrutural := stMascaraEstrutural||arValor[inIndex];
            END IF;
        ELSE
            boLaco = FALSE;
        END IF;
    END LOOP;
    inIndex := inIndex + 1;
    
    IF (LENGTH(stMascaraEstrutural) > 0) THEN
        IF (boPonto = TRUE) THEN
            stMascaraEstrutural := SUBSTRING(stMascaraEstrutural, 2, LENGTH(stMascaraEstrutural));
        END IF;
    END IF;

    IF (boSubElemento = TRUE) THEN
        stCodRetorno = SUBSTRING(stMascaraEstrutural, 6, inTamanho);
    ELSE 
        stCodRetorno = stMascaraEstrutural;
    END IF;
    stCodRetorno = SUBSTRING(stCodRetorno, 1, inTamanho);

    RETURN stCodRetorno;
END;
$$ LANGUAGE plpgsql
