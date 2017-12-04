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
 * Classe de regra de identificador de risco fiscal
 *
 * @category    Urbem
 * @package     STN
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: RSTNIdentificadorRiscoFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $
 */

include_once CAM_FW_INCLUDE         . 'valida.inc.php';
include_once CAM_GPC_STN_MAPEAMENTO . 'TSTNIdentificadorRiscoFiscal.class.php';

class RSTNIdentificadorRiscoFiscal
{
    public $obTSTNIdentificadorRiscoFiscal;

    /**
     * Metodo contrutor, instancia as classes necessarias.
     *
     * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTSTNIdentificadorRiscoFiscal  = new TSTNIdentificadorRiscoFiscal();
    }

    /**
     * Metodo que recupera os identificadores
     *
     * @author      Desenvolvedor   Eduardo Schitz <eduardo.schitz@cnm.org.br>
     * @param object $rsRecordSet
     *
     * @return object $obErro
     */
    public function listIdentificadores(&$rsIdentificadores)
    {
        $stFiltro = '';
        $stOrder = ' ORDER BY identificador_risco_fiscal.cod_identificador ';

        //Faz a consulta usando o filtro
        $obErro = $this->obTSTNIdentificadorRiscoFiscal->listIdentificadores($rsIdentificadores, $stFiltro, $stOrder);

        return $obErro;
    }

}
