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
    * Arquivo de popup de busca de Instituição/Entidade
    * Data de Criação: 02/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-30 13:04:04 -0300 (Seg, 30 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

class  IPopUpInstituicaoEntidade
{
    /**
        * @access Private
        * @var Object
    */
    public $obBscCGM;
    public $obLblCGM;
    public $obHdnCGM;
    public $obHdnCGMNome;
    public $obLblCNPJ;
    public $obLblEndereco;
    public $obLblBairro;
    public $obLblCidade;
    public $obLblTelefone;
    public $boDadosExtra;
    public $boLabel;
    public $boFiltro;
    public $boInstituicaoEntidade;

    public function setDadosExtra($valor)
    {
        $this->boDadosExtra = $valor;
        Sessao::write('boDadosExtra', $valor);
    }
    public function setLabel($valor) { $this->boLabel = $valor; }
    public function setFiltro($valor) { $this->boFiltro = $valor; }
    public function setInstituicaoEntidade($valor) { $this->boInstituicaoEntidade = $valor; }

    public function getDadosExtra() { return $this->boDadosExtra; }
    public function getLabel() { return $this->boLabel; }
    public function getFiltro() { return $this->boFiltro; }
    public function getInstituicaoEntidade() { return $this->boInstituicaoEntidade; }
    /**
        * Metodo Construtor
        * @access Public

    */
    public function IPopUpInstituicaoEntidade($boInstituicao=true)
    {
        $this->setFiltro(false);
        $this->setDadosExtra(true);
        $this->setInstituicaoEntidade($boInstituicao);
        $this->obBscCGM = new BuscaInner();
        if ($boInstituicao) {
            $this->obBscCGM->setRotulo                 ( 'Instituição' );
            $this->obBscCGM->setTitle                  ( 'Informe o CGM da instituição de ensino.' );
        } else {
            $this->obBscCGM->setRotulo                 ( 'Entidade Intermediadora' );
            $this->obBscCGM->setTitle                  ( 'Informe o CGM da entidade intermediadora.' );
        }
        $this->obBscCGM->setId                     ( 'stNomCGM'         );
        $this->obBscCGM->setNull                   ( false              );

        $this->obBscCGM->obCampoCod->setName       ( "inCGM"            );
        $this->obBscCGM->obCampoCod->setSize       ( 6                  );
        $this->obBscCGM->obCampoCod->setMaxLength  ( 10                 );
        $this->obBscCGM->obCampoCod->setAlign      ( "left"             );

        $this->obLblCGM = new Label();
        $this->obLblCGM->setRotulo("Instituição");
        $this->obLblCGM->setId("stCGM");

        $this->obHdnCGM = new hidden();
        $this->obHdnCGM->setName("inCGM");

        $this->obHdnCGMNome = new hidden();
        $this->obHdnCGMNome->setName("stNomCGM");

        $this->obLblCNPJ = new Label();
        $this->obLblCNPJ->setRotulo("CNPJ");
        $this->obLblCNPJ->setId("stCNPJ");

        $this->obLblEndereco = new Label();
        $this->obLblEndereco->setRotulo("Endereço");
        $this->obLblEndereco->setId("stEndereco");

        $this->obLblBairro = new Label();
        $this->obLblBairro->setRotulo("Bairro");
        $this->obLblBairro->setId("stBairro");

        $this->obLblCidade = new Label();
        $this->obLblCidade->setRotulo("Cidade");
        $this->obLblCidade->setId("stCidade");

        $this->obLblTelefone = new Label();
        $this->obLblTelefone->setRotulo("Telefone");
        $this->obLblTelefone->setId("stTelefone");

    }

    public function geraFormulario(&$obFormulario,&$obForm)
    {
        $pgOcul  = "'".CAM_GRH_EST_PROCESSAMENTO."OCProcurarCgm.php?".Sessao::getId()."&".$this->obBscCGM->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obBscCGM->obCampoCod->getName()."&stIdCampoDesc=".$this->obBscCGM->getId()."&stTipoBusca=juridica&boFiltro=".$this->getFiltro()."&boInstituicao=".$this->getInstituicaoEntidade()."'";
        if ( $this->getDadosExtra() ) {
            $pgOcul2 = "'".CAM_GRH_EST_PROCESSAMENTO."OCProcurarCgm.php?".Sessao::getId()."&".$this->obBscCGM->obCampoCod->getName()."='+this.value";
            $this->obBscCGM->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');ajaxJavaScript(".$pgOcul2.",'preencherDados');" );
        } else {
            $this->obBscCGM->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');" );
        }
        $this->obBscCGM->setFuncaoBusca("abrePopUp('" . CAM_GRH_EST_POPUPS . "instituicaoEnsino/FLProcurarCgm.php','".$obForm->getName()."', '". $this->obBscCGM->obCampoCod->stName ."','". $this->obBscCGM->stId . "','juridica','".Sessao::getId() ."&boFiltro=".$this->getFiltro()."&boInstituicao=".$this->getInstituicaoEntidade()."','800','550');");

        if ( $this->getLabel() ) {
            $obFormulario->addComponente($this->obLblCGM);
            $obFormulario->addHidden($this->obHdnCGM);
            $obFormulario->addHidden($this->obHdnCGMNome);
        } else {
            $obFormulario->addComponente($this->obBscCGM);
        }
        if ( $this->getDadosExtra() ) {
            $obFormulario->addComponente($this->obLblCNPJ);
            $obFormulario->addComponente($this->obLblEndereco);
            $obFormulario->addComponente($this->obLblBairro);
            $obFormulario->addComponente($this->obLblCidade);
            $obFormulario->addComponente($this->obLblTelefone);
        }
    }
}
?>
