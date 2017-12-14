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
    * Classe interface para Filtro de Assentamentos
    * Data de Criação: 21/02/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.00

    $Id: IFiltroAssentamentoMultiplo.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploClassificacaoAssentamento.class.php"                               );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploAssentamento.class.php"                               );

class IFiltroAssentamentoMultiplo extends Objeto
{
    /**
        * @access Private
        * @var Object
    */
    public $obISelectMultiploClassificacaoAssentamento;

    /**
        * @access Private
        * @var Object
    */
    public $obISelectMultiploAssentamento;

    public function IFiltroAssentamentoMultiplo()
    {
        $this->obISelectMultiploClassificacaoAssentamento = new ISelectMultiploClassificacaoAssentamento();
        $stNomeComponente = $this->obISelectMultiploClassificacaoAssentamento->getNomeLista2();

        $stOnClick  = "selecionarFiltroAssentamentoMultiplo('".$stNomeComponente."',true);";
        $stOnClick .= "buscaValorBscInner('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroAssentamentoMultiplo.php?".Sessao::getId()."','frm','".$stNomeComponente."','".$stNomeComponente."','preencherAssentamentos');";
        $stOnClick .= "selecionarSelectMultiploRegSubCarEsp('".$stNomeComponente."',false);";
        $this->obISelectMultiploClassificacaoAssentamento->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
        $this->obISelectMultiploClassificacaoAssentamento->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
        $this->obISelectMultiploClassificacaoAssentamento->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
        $this->obISelectMultiploClassificacaoAssentamento->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
        $this->obISelectMultiploClassificacaoAssentamento->obSelect1->obEvento->setOnDblClick( $stOnClick );
        $this->obISelectMultiploClassificacaoAssentamento->obSelect2->obEvento->setOnDblClick( $stOnClick );

        $this->obISelectMultiploAssentamento = new ISelectMultiploAssentamento();
    }

    /**
        * Monta os campos do filtro do AssentamentoMultiplo
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente        ( $this->obISelectMultiploClassificacaoAssentamento  );
        $obFormulario->addComponente        ( $this->obISelectMultiploAssentamento  );
    }
}
?>
