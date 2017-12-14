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
CREATE OR REPLACE FUNCTION calculafracaoIdeal( inImovel INTEGER) RETURNS NUMERIC(15,2) AS $$
DECLARE
  nuAreaImoveisLote  NUMERIC;
  nuAreaLote 	     NUMERIC;	
  nuFracaoIdeal      NUMERIC;	
  nuAreaImovel       NUMERIC;	
  inQuantidade 	     INTEGER; 		
BEGIN
  inQuantidade 	    := recuperaQuantidadeImovelPorLote(inImovel); 
  nuAreaLote 	    := imobiliario.fn_area_real(inImovel); 
  nuAreaImoveisLote := imobiliario.fn_calcula_area_imovel_lote(inImovel);
  nuAreaImovel      := IMOBILIARIO.FN_CALCULA_AREA_IMOVEL( inImovel );

  IF ( nuAreaImoveisLote > 0 ) THEN
    nuFracaoIdeal     := (nuAreaImovel*nuAreaLote )/nuAreaImoveisLote ;
  ELSE
    nuFracaoIdeal     := 0.00;
  END IF;

  return ROUND (nuFracaoIdeal,2);

END;
$$ LANGUAGE plpgsql;
