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
* $Id: buscaAberturaInscricaoMunicipalMes.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2006/11/20 16:04:59  gris
   --  Inclusão de função interna buscaAberturaInscricaoMunicipalData
   --  Inclusão de função interna buscaAberturaInscricaoMunicipalDia
   --  Inclusão de função interna buscaAberturaInscricaoMunicipalMes
   --  Inclusão de função interna buscaAberturaInscricaoMunicipalAno
   --  Inclusão de função interna buscaInicioInscricaoMunicipalData
   --  Inclusão de função interna buscaInicioInscricaoMunicipalDia
   --  Inclusão de função interna buscaInicioInscricaoMunicipalMes
   --  Inclusão de função interna buscaInicioInscricaoMunicipalAno

*/

   CREATE OR REPLACE FUNCTION buscaAberturaInscricaoMunicipalMes( intInscricaoEconomica INTEGER)
   RETURNS CHARACTER VARYING  AS $$
   DECLARE
      varDiaAbertura VARCHAR(02);
   BEGIN

      SELECT BTRIM(TO_CHAR(DATE_PART( 'MONTH', cadastro_economico.dt_abertura),'99'))
        INTO varDiaAbertura
        FROM economico.cadastro_economico
       WHERE cadastro_economico.inscricao_economica =  intInscricaoEconomica
      ;

      RETURN varDiaAbertura;
   END;
   $$ LANGUAGE 'plpgsql';
