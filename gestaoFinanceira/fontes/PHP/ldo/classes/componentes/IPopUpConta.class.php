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
 * Componente IPopUpConta
 * Data de Criação: 07/09/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.mendes>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.03
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO   . 'TAdministracaoConfiguracao.class.php';
include_once CLA_BUSCAINNER;

class  IPopUpConta extends BuscaInner
{
    /**
     * Atributo para verificar se no Buscar Inner vai para a LS ou FL do componente
     * @
     **/
    private $usaFiltro;
    private $obHiddenCodConta;
    private $stExercicio;
    private $obHiddenStExercicio;

    public function setUsaFiltro($valor)
    {
        $this->usaFiltro = $valor;
    }

    public function getUsaFiltro()
    {
        return $this->usaFiltro;
    }

    public function setStExercicio($valor)
    {
        $this->stExercicio = $valor;
    }

    public function getStExercicio()
    {
        return $this->stExercicio;
    }

    public function __construct()
    {
        Sessao::remove('linkPopUp');
        parent::BuscaInner();

        $this->setTitle("Informe Código da Conta.");

        $this->setRotulo("Código da Conta");
        $this->setNull(true);
        $this->setId("stDescricao");
        $this->obCampoCod->setName("inCodConta");
        $this->obCampoCod->setId("inCodConta");
        $this->obCampoCod->setValue("");
        $this->obCampoCod->setAlign("left");

        $this->setStExercicio(Sessao::getExercicio());
        $this->setUsaFiltro(true);

        $this->obHiddenStExercicio = new Hidden();
        $this->obHiddenStExercicio->setName("stExercicio");
        $this->obHiddenStExercicio->setId("stExercicio");
        $this->obHiddenStExercicio->setValue($this->getStExercicio());
    }

    public function geraFormulario(&$obFormulario)
    {
        $pgOcul = "'".CAM_GF_LDO_POPUPS."conta/OCProcurarConta.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stExercicio=".$this->getStExercicio()."'";

        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'contaDespesa');");

        if ($this->getUsaFiltro()) {
            $this->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."conta/FLProcurarConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->obHiddenStExercicio->getValue()."','".Sessao::getId()."','800','550');");
        } else {
            $this->setFuncaoBusca("abrePopUp('".CAM_GF_LDO_POPUPS."conta/LSProcurarConta.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->obHiddenStExercicio->getValue()."','".Sessao::getId()."','800','550');");
        }

        $obFormulario->addComponente($this);
        $obFormulario->addHidden($this->obHiddenStExercicio);
    }
}
