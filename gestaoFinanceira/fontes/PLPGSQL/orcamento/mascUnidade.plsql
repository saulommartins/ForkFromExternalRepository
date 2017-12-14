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
    * Retorna a máscara da unidade baseado na mascara da despesa
    * Data de Criação: 08/02/2010 
 
 
    * @author Analista:      Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
    * @author Desenvolvedor: Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
 
    * @package      URBEM 
    * @subpackage   Orcamento 
 
    * $Id: $ 
*/
CREATE OR REPLACE FUNCTION orcamento.fn_masc_unidade(stExercicio VARCHAR ) RETURNS VARCHAR AS $$
DECLARE
    stRetorno       VARCHAR = '';
BEGIN

    SELECT split_part(valor, '.', 2) AS masc_unidade FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = stExercicio INTO stRetorno;

    RETURN stRetorno;
END;

$$ LANGUAGE 'plpgsql';
