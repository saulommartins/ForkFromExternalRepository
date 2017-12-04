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
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 23/10/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * Casos de uso: uc-03.05.15, uc-03.05.00

    $Id: IMontaNumeroLicitacao.class.php 63474 2015-08-31 21:54:15Z carlos.silva $

*/

include_once ( CLA_OBJETO );

class IMontaNumeroLicitacao extends Objeto
{
    public $obForm;
    public $obExercicio;
    public $boContrato;
    
    public $obITextBoxSelectEntidadeGeral;
    public $obITextBoxSelectEntidadeUsuario;
    public $obTxtOrgao;
    public $obCmbOrgao;
    public $obTxtUnidade;
    public $obCmbUnidade;
    public $obTxtNumeroContrato;
    public $obISelectModalidade;
    public $obCmbTipoObjeto;
    public $obTxtLicitacao;
    public $obCmbLicitacao;
    public $obProcessoLicitatorio;
    public $stName;
    public $stRotulo;
    public $stFiltraLicitacoes;
    public $boSelecionaAutomaticamenteLicitacao;
    public $boEntidadeUsuario;
    public $obHdnDtLicitacao;

    public function setRotulo($valor) { $this->stRotulo = $valor; }
    public function setContrato($valor) { $this->boContrato = $valor; }
    public function setName($valor) { $this->stName   = $valor; }
    public function setSelecionaAutomaticamenteLicitacao($valor) { $this->boSelecionaAutomaticamenteLicitacao = $valor; }
    public function setEntidadeUsuario($valor) { $this->boEntidadeUsuario = $valor; }
    public function setPreencheValorLicitacao($valor) { $this->boSetarValorLiciticao = $valor;}

    public function getRotulo() { return $this->stRotulo; }
    public function getContrato() { return $this->boContrato; }
    public function getName() { return $this->stNme;    }
    public function getSelecionaAutomaticamenteLicitacao() { return $this->boSelecionaAutomaticamenteLicitacao; }
    public function getEntidadeUsuario() { return $this->boEntidadeUsuario; }
    public function getPreencheValorLicitacao() { return $this->boSetarValorLiciticao;}

    public function setTipoBusca($stTipoBusca)
    {
        $boFiltraLicitacao = isset($boFiltraLicitacao) ? $boFiltraLicitacao : "";
        $this->obISelectModalidade = new ISelectModalidade();
        $this->obISelectModalidade->setNull(false);
        $this->obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('../../instancias/processamento/OCMontaNumeroLicitacao.php?". Sessao::getId(). "&inCodLicitacao='+frm.inCodLicitacao.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stFiltraLicitacao=".$boFiltraLicitacao."&numLicitacao='+document.getElementById('hdnNumLicitacao').value, '".$stTipoBusca."');");
    }

    public function __construct(&$obForm, $boFiltraLicitacao=false, $stFiltro='', $preencherValorLicitacao='')
    {
        parent::Objeto();
        global $pgOcult;
        $oculto = "../../instancias/processamento/OCMontaNumeroLicitacao.php";
    
        $rsLicitacao = new RecordSet();

        include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
        include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
        include_once ( CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php" );

        $this->boContrato  = false;
        $this->obExercicio = new Exercicio();
        $this->obExercicio->setName( 'stExercicioLicitacao' );

        $this->setSelecionaAutomaticamenteLicitacao(true);

        $this->obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
        $this->obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
        $this->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");

        $this->obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
        $this->obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
        $this->obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");

        $this->obISelectModalidade = new ISelectModalidade();
        $this->obISelectModalidade->setNull( false );
        $this->obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$oculto."?". Sessao::getId(). "&inCodLicitacao='+frm.inCodLicitacao.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stFiltraLicitacao=".$boFiltraLicitacao."&numLicitacao='+document.getElementById('hdnNumLicitacao').value+'&stFiltro=".$stFiltro."&boSetarValorLicitacao=".$preencherValorLicitacao."', 'carregaLicitacao');");

        $this->obCmbLicitacao = new Select();
        $this->obCmbLicitacao->setName     ( 'inCodLicitacao'   );
        $this->obCmbLicitacao->setRotulo   ( 'Licitação'        );
        $this->obCmbLicitacao->setTitle    ( 'Selecione a Licitação.' );
        $this->obCmbLicitacao->setId       ( 'inCodLicitacao'   );
        $this->obCmbLicitacao->setCampoID  ( 'cod_licitacao'    );
        $this->obCmbLicitacao->setCampoDesc( 'cod_licitacao'    );
        $this->obCmbLicitacao->addOption   ( '','Selecione'     );
        $this->obCmbLicitacao->setNull     ( false );
        $this->obCmbLicitacao->preencheCombo( $rsLicitacao      );
        $this->obCmbLicitacao->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioLicitacao=' +frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+ '&inCodLicitacao='+this.value+'&boSetarValorLicitacao=".$preencherValorLicitacao."', 'carregaProcesso');");

        $this->obProcessoLicitatorio = new Label();
        $this->obProcessoLicitatorio->setId( 'stProcesso' );
        $this->obProcessoLicitatorio->setValue( isset($stProcesso) ? $stProcesso : '&nbsp;' );
        $this->obProcessoLicitatorio->setRotulo( 'Processo Administrativo' );
        
        $this->obHdnProcessoLicitatorio = new Hidden();
        $this->obHdnProcessoLicitatorio->setName( 'hdnProcesso' );
        $this->obHdnProcessoLicitatorio->setId( 'hdnProcesso' );
        $this->obHdnProcessoLicitatorio->setValue( isset($stProcesso) ? $stProcesso : "" );

        $this->obHdnDtLicitacao = new Hidden();
        $this->obHdnDtLicitacao->setName( 'hdnDtLicitacao' );
        $this->obHdnDtLicitacao->setId( 'hdnDtLicitacao' );
        $this->obHdnDtLicitacao->setValue( isset($dtLicitacao) ? $dtLicitacao  : "");

        $this->obHdnNumLicitacao = new Hidden();
        $this->obHdnNumLicitacao->setName( 'hdnNumLicitacao' );
        $this->obHdnNumLicitacao->setId( 'hdnNumLicitacao' );
    }

    public function geraFormulario(&$obFormulario)
    {
        Sessao::write('IMontaNumeroLicitacao', $this);
        $obFormulario->addComponente( $this->obExercicio );
        if ( !$this->getEntidadeUsuario() ) {
            $obFormulario->addComponente( $this->obITextBoxSelectEntidadeGeral );
        } else {
            $obFormulario->addComponente( $this->obITextBoxSelectEntidadeUsuario );
        }
        
        $obFormulario->addComponente( $this->obISelectModalidade );
        $obFormulario->addComponente( $this->obCmbLicitacao );
        $obFormulario->addComponente( $this->obProcessoLicitatorio );
        $obFormulario->addHidden    ( $this->obHdnProcessoLicitatorio );
        $obFormulario->addHidden    ( $this->obHdnDtLicitacao );
        $obFormulario->addHidden    ( $this->obHdnNumLicitacao );
    }
}

?>