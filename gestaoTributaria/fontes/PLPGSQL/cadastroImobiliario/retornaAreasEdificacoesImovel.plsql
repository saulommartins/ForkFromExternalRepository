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
* funcao imobiliario.retornaAreasEdificacoesImovel(INTEGER)
* retorno: NUMERIC contendo a area de uma das edificacoes do imovel, segundo o OFFSET informado
*          0           = unidade autonoma
*          1 em diante = unidades dependentes, na ordem em que foram cadastradas
*
* Fabio Bertoldi - 20130328
*
*/

CREATE OR REPLACE FUNCTION imobiliario.retornaAreasEdificacoesImovel( inImovel      INTEGER
                                                                    , inOffset      INTEGER
                                                                    ) RETURNS       NUMERIC AS $$
DECLARE
    boEdificado     BOOLEAN;
    inCodigo        INTEGER;
    nuArea          NUMERIC(14,2);

BEGIN
    SELECT arrecadacao.verificaEdificacaoImovel(inImovel)
      INTO boEdificado
         ;
    IF boEdificado = TRUE THEN
        IF inOffset = 0 THEN
            SELECT imobiliario.fn_area_unidade_autonoma(imobiliario.fn_busca_codigo_edificacao(inImovel), inImovel)
              INTO nuArea
                 ;
        ELSE
            SELECT imobiliario.fn_busca_codigo_unidade_dependente(inImovel, inOffset-1)
              INTO inCodigo
                 ;
            SELECT imobiliario.fn_area_unidade_dependente(inCodigo, inImovel)
              INTO nuArea
                 ;
        END IF;
    ELSE
        nuArea := '0.00';
    END IF;

    RETURN nuArea;
END;
$$ LANGUAGE 'plpgsql';
