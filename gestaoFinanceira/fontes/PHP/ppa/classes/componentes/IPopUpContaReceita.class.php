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
* Arquivo de popup de busca de Conta Receita
* Data de Criação: 02/11/2008

* @author Analista: Heleno Menezes dos Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos

* @package URBEM
* @subpackage

* Casos de uso: uc-02.09.11
*/

include_once(CLA_BUSCAINNER);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"      );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

class IPopUpContaReceita extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;

    public $inAnoExercicio;

    public function setAnoExercicio($inAnoExercicio)
    {
        $this->inAnoExercicio = $inAnoExercicio;
    }

    public function getAnoExercicio()
    {
        return $this->inAnoExercicio;
    }

    /**
        * Metodo Construtor
        * @access Public
    */
    public function IPopUpContaReceita($obForm, &$obFormulario)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $obHdnCodContaReceita = new Hidden;
        $obHdnCodContaReceita->setName ( 'inCodContaReceita' );
        $obHdnCodContaReceita->setId   ( 'inCodContaReceita' );
        $obHdnCodContaReceita->setValue( $_REQUEST['inCodContaReceita'] );
        $obFormulario->addHidden($obHdnCodContaReceita);

        $this->setRotulo('Receita');
        $obHdnMascClassificacao = new Hidden;
        $obHdnMascClassificacao->setName ( "stMascClassificacao" );
        $obHdnMascClassificacao->setValue( $stMascaraRubrica );
        $obROrcamentoReceita      = new ROrcamentoReceita;
        $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
        $stMascaraRubrica = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();
        $this->setRotulo               ( "Receita" );
        $this->setTitle                ( "Informe a rubrica de receita." );
        $this->setNulL                 ( false );
        $this->setId                   ( "stDescricaoReceita" );
        $this->setValue                ( $_REQUEST['stDescricao'] );
        $this->obCampoCod->setName     ( "codEstruturalReceita" );
        $this->obCampoCod->setId       ( "codEstruturalReceita" );
        $this->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
        $this->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
        $this->obCampoCod->setValue    ( $_GET['stMascClassReceita'] );
        $this->obCampoCod->setAlign    ("left");
        $this->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
        $this->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");

    }

    public function montaHTML()
    {
        $sessao = $_SESSION ['sessao'];
        $inAnoExercicio = $this->inAnoExercicio ? $this->getAnoExercicio() : Sessao::read('exercicio');
        $this->setFuncaoBusca("abrePopUp('".CAM_GF_PPA_POPUPS."contaReceita/FLProcurarContaReceita.php','".$this->obForm->getName()."','".$this->obCampoCod->getName()."','".$this->getId()."','".$inAnoExercicio."','".Sessao::getId()."','800','550');");
        $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript( '".CAM_GF_PPA_POPUPS.'contaReceita/OCProcurarContaReceita.php?'.Sessao::getId()."&stNomCampoCod=".$this->obCampoCod->getName()."&inAnoExercicio=".$inAnoExercicio."&stIdCampoDesc=".$this->getId()."&stNomForm=".$this->obForm->getName()."&inCodigo='+this.value, 'buscaContaReceita' );");
        parent::montaHTML();
    }

}
?>
