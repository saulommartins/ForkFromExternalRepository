<?php
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
?>
<?php
/**
    * Arquivo de mapeamento para a função que busca os dados das Metas de Arrecadação da Receita
    * Data de Criação   : 07/02/2014

    * @author Analista      Sérgio
    * @author Desenvolvedor Carlos Adriano

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGMetaFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGMetaFiscal()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_meta_fiscal');

    $this->AddCampo('exercicio'   , 'varchar', false, '', false, false);
    $this->AddCampo('cod_ppa'     , 'integer', false, '', false, false);
    $this->AddCampo('cod_pib'     , 'integer', false, '', false, false);
    $this->AddCampo('cod_inflacao', 'integer', false, '', false, false);
}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT '".$this->getDado("exercicio")."' AS exercicio
             , *

          FROM ".$this->getTabela()." (
                '".$this->getDado("exercicio")."'
              , '".$this->getDado("cod_ppa")."'
              , ".$this->getDado("cod_pib")."
              , ".$this->getDado("cod_inflacao")."
        ) AS (
                vlCorrenteReceitaTotal              DECIMAL(14,2)
              , vlCorrenteReceitaPrimaria           DECIMAL(14,2)
              , vlCorrenteDespesaTotal              DECIMAL(14,2)
              , vlCorrenteDespesaPrimaria           DECIMAL(14,2)
              , vlResultadoPrimario                 DECIMAL(14,2)
              , vlCorrenteResultadoNominal          DECIMAL(14,2)
              , vlCorrenteDividaPublicaConsolidada  DECIMAL(14,2)
              , vlCorrenteDividaConsolidadaLiquida  DECIMAL(14,2)

              , vlConstanteReceitaTotal             DECIMAL(14,2)
              , vlConstanteReceitaPrimaria          DECIMAL(14,2)
              , vlConstanteDespesaTotal             DECIMAL(14,2)
              , vlConstanteDespesaPrimaria          DECIMAL(14,2)
              , vlConstanteResultadoPrimario        DECIMAL(14,2)
              , vlConstanteResultadoNominal         DECIMAL(14,2)
              , vlConstanteDividaPublicaConsolidada DECIMAL(14,2)
              , vlConstanteDividaConsolidadaLiquida DECIMAL(14,2)

              , pcPIBReceitaTotal                   DECIMAL(14,2)
              , pcPIBReceitaPrimaria                DECIMAL(14,2)
              , pcPIBDespesaTotal                   DECIMAL(14,2)
              , pcPIBDespesaPrimaria                DECIMAL(14,2)
              , pcPIBResultadoPrimario              DECIMAL(14,2)
              , pcPIBResultadoNominal               DECIMAL(14,2)
              , pcPIBDividaPublicaConsolidada       DECIMAL(14,2)
              , pcPIBDividaConsolidadaLiquida       DECIMAL(14,2)
            )";

    return $stSql;
}

}
