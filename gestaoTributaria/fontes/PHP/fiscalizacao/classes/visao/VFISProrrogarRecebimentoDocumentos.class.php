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
    * Classe de Visao do Prorrogar Recebimento de Documentos
    * Data de Criação   : 08/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Visao

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/componentes/ITextBoxSelectDocumento.class.php';
require_once( CAM_GT_FIS_COMPONENTES."IFISTextBoxSelectDocumento.class.php" );
require_once( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
require_once( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );

final class VFISProrrogarRecebimentoDocumentos
{
    private $controller;
    private $visaoProcessoFiscal;
    private $visaoIniciarProcessoFiscal;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
        $this->visaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal($this->controller);
    }

    public function prorrogarRecebimentoDocumentos($param)
    {
        return $this->controller->prorrogarRecebimento( $param );
    }

    public function montaForm($param)
    {
        return $this->visaoIniciarProcessoFiscal->montaForm( $param );
    }

    public function recuperarListaInicioFiscalizacaoEconomica($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoEconomica();
    }

    public function recuperarListaInicioFiscalizacaoObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoObra();
    }

    public function recuperarListaInicioFiscalizacaoEconomicaObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getListaInicioFiscalizacaoEconomicaObra();
    }

    public function iniciarInicioFiscalizacaoEconomica($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getInicioFiscalizacaoEconomica();
    }

    public function iniciarInicioFiscalizacaoObra($stFiltro)
    {
        if ($stFiltro) {
            $this->controller->setCriterio($stFiltro);
        }

        return $this->controller->getInicioFiscalizacaoObra();
    }

    public function filtrosDocumentos($param)
    {
        if ($param['inTipoFiscalizacao'] != "") {
                $stFiltro[] = " pf.cod_tipo = " .$param['inTipoFiscalizacao']. "\n";
        }

        if ($param['inCodProcesso'] != "") {
            $stFiltro[] = " pf.cod_processo = " .$param['inCodProcesso']. "\n";
        }

        if ($param['inInscricaoEconomica'] != "") {
            $stFiltro[] = " pfe.inscricao_economica = " .$param['inInscricaoEconomica']. "\n";
        }

        if ($param['inCodImovel'] != "") {
            $stFiltro[] = " pfo.inscricao_municipal = " .$param['inCodImovel']. "\n";
        }

        if ($param['numcgm'] != "") {
            $stFiltro[] = " fc.numcgm = " .$param['numcgm']. "\n";

        }

        if ($param['boInicio']) {
            $stFiltro[] = " pfc.cod_processo is null \n";
        }

        $stFiltro[] = " fif.cod_processo notnull \n";

        $stFiltro[] = " ftf.cod_processo is null \n";

        $return = " ";

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }
}
?>
