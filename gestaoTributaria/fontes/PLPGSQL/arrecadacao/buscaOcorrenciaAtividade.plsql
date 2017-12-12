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
* $Id: $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION buscaOcorrenciaAtividade( int, int )  RETURNS integer AS '
DECLARE
    inInscricaoEconomica   ALIAS FOR $1;
    inCodigoAtividade      ALIAS FOR $2;
    inRetorno              integer;
    
BEGIN

	SELECT 
		ocorrencia_atividade
	INTO    
		inRetorno
	FROM    
		economico.atividade_cadastro_economico
	WHERE   
		inscricao_economica = inInscricaoEconomica 
	AND 
	        cod_atividade       = inCodigoAtividade;

   

    RETURN inRetorno; 
END;
' LANGUAGE 'plpgsql';
