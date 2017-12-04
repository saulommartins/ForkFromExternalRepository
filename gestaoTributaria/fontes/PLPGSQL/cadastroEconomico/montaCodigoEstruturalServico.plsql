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
* $Id: montaCodigoEstruturalServico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.4  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_monta_codigo_estrutural_servico() RETURNS TRIGGER AS '
DECLARE

BEGIN
    NEW.cod_estrutural := economico.fn_consulta_servico (( SELECT cod_vigencia from economico.nivel_servico_valor where cod_nivel = ( SELECT max(cod_nivel) as cod_nivel from economico.nivel_servico_valor where cod_servico = OLD.cod_servico and valor::integer <> 0 ) and cod_servico = OLD.cod_servico ), OLD.cod_servico
   ) ;
    RETURN NEW;
END;
' LANGUAGE 'plpgsql';
