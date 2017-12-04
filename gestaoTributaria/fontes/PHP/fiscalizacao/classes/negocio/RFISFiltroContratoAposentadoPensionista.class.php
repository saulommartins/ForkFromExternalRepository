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
    * Classe de regra de negócio do componente IFiltroContratoAposentadoPensionista.class.php
    * Data de Criação: 29/12/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:
*/
require_once(CAM_GT_FIS_NEGOCIO.'RFISProcessoFiscal.class.php');
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISFiscal.class.php');
require_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
require_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");

class RFISFiltroContratoAposentadoPensionista extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRecuperaContratoFiscais()
    {
        $mapeamento = "TFISFiscal";
        $metodo = "recuperaContratoFiscais";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getRecuperaContratoServidorCasoCausa()
    {
        $mapeamento = "TPessoalContratoServidorCasoCausa";
        $metodo = "recuperaTodos";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }
}
?>
