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
* $Id: recuperaQuantidadeImovelPorLote.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2006/11/21 11:41:10  gris
   -- Inclusão função interna recuperaTrechoValorMetroQuadradoTerritorial
   -- Inclusão função interna recuperaTrechoValorMetroQuadradoPredial
   -- Inclusão função interna recuperaQuantidadeImovelPorLote

*/

   --
   -- Criação das Funções no banco.
   --
   CREATE OR REPLACE FUNCTION recuperaQuantidadeImovelPorLote(intInscricaoMunicipal INTEGER )
   RETURNS INTEGER AS $$
   DECLARE
      intQtdTotalMoveis INTEGER;
   BEGIN
         Select SUM(1) as total_imovel
           INTO intQtdTotalMoveis
           FROM imobiliario.imovel_lote
          WHERE imovel_lote.cod_lote = (   SELECT imovel_lote.cod_lote
                                             FROM imobiliario.imovel_lote
                                            WHERE imovel_lote.inscricao_municipal = intInscricaoMunicipal
                                         ORDER BY imovel_lote.timestamp desc
                                            LIMIT 1
                                       )
            AND timestamp = ( SELECT MAX(timestamp)
                                FROM imobiliario.imovel_lote
                               WHERE inscricao_municipal = intInscricaoMunicipal
                            )
        GROUP BY imovel_lote.timestamp
              ;

      RETURN intQtdTotalMoveis;
   END;
   $$ LANGUAGE 'plpgsql';

