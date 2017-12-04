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

*
* $Id: $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION economico.buscaBairroEmpresa( inInscricaoEconomica INTEGER )
RETURNS INTEGER AS $$
DECLARE
    inCodBairro     INTEGER;
    inMes           NUMERIC;
BEGIN

       SELECT CASE
                WHEN EDF.inscricao_economica IS NOT NULL 
                    THEN ILB.cod_bairro
                ELSE EDI.cod_bairro
              END                                                   AS cod_bairro
         INTO inCodBairro
         FROM economico.cadastro_economico                          AS ECE
    LEFT JOIN economico.domicilio_fiscal                            AS EDF
           ON EDF.inscricao_economica   = ECE.inscricao_economica
    LEFT JOIN imobiliario.imovel_lote                               AS IIL
           ON IIL.inscricao_municipal   = EDF.inscricao_municipal
    LEFT JOIN imobiliario.lote_bairro                               AS ILB
           ON IIL.cod_lote              = ILB.cod_lote
    LEFT JOIN economico.domicilio_informado                         AS EDI
           ON EDI.inscricao_economica   = ECE.inscricao_economica
        WHERE ECE.inscricao_economica   = inInscricaoEconomica;

   RETURN inCodBairro;
END;
$$ LANGUAGE 'plpgsql';
