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
    * Data de Criação:	01/10/2008

    * @author Analista:
    * @author Desenvolvedor:Luiz Felipe Prestes Teixeira
    * Casos de uso:

    $Id: $

*/

include_once ( CLA_OBJETO );

class IMontaNumeroCompraDireta extends Objeto
{
    public $obForm;
    public $obExercicio;
    public $obITextBoxSelectEntidadeGeral;
    public $obITextBoxSelectEntidadeUsuario;
    public $obTxtOrgao;
    public $obCmbOrgao;
    public $obTxtUnidade;
    public $obCmbUnidade;
    public $obTxtNumeroContrato;
    public $obISelectModalidade;
    public $obCmbTipoObjeto;
    public $obCmbCompraDireta;

    public $stName;
    public $stRotulo;
    public $stFiltraCompraDireta;
    public $boSelecionaAutomaticamenteCompraDireta;
    public $boEntidadeUsuario;
    public $obHdnDtCompraDireta;

    public function setRotulo($valor) { $this->stRotulo = $valor; }
    public function setName($valor) { $this->stName   = $valor; }
    public function setSelecionaAutomaticamenteCompraDireta($valor) { $this->boSelecionaAutomaticamenteCompraDireta = $valor; }
    public function setEntidadeUsuario($valor) { $this->boEntidadeUsuario = $valor; }
    public function getEntidadeUsuario() { return $this->boEntidadeUsuario; }

    public function getRotulo() { return $this->stRotulo; }
    public function getName() { return $this->stNme;    }
    public function getSelecionaAutomaticamenteCompraDireta() { return $this->boSelecionaAutomaticamenteCompraDireta; }

    public function IMontaNumeroCompraDireta(&$obForm, $boFiltraCompraDireta=false)
    {
        parent::Objeto();
        $oculto = CAM_GP_COM_INSTANCIAS."processamento/OCMontaNumeroCompraDireta.php";

        $rsCompraDireta = new RecordSet();

        include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
        include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
        
        $this->obExercicio = new Exercicio();
        $this->obExercicio->setName( 'stExercicioCompraDireta' );

        $this->setSelecionaAutomaticamenteCompraDireta(true);

        $this->obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
        $this->obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioCompraDireta='+frm.stExercicioCompraDireta.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
        $this->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioCompraDireta='+frm.stExercicioCompraDireta.value+'&inCodEntidade='+this.value, 'carregaModalidade');");

        $this->obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
        $this->obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioCompraDireta='+frm.stExercicioCompraDireta.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
        $this->obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioCompraDireta='+frm.stExercicioCompraDireta.value+'&inCodEntidade='+this.value, 'carregaModalidade');");

        $this->obISelectModalidade = new Select();
        $this->obISelectModalidade->setRotulo    ("Modalidade"                    );
        $this->obISelectModalidade->setTitle     ("Selecione a modalidade."       );
        $this->obISelectModalidade->setName      ("inCodModalidade"               );
        $this->obISelectModalidade->setCampoID   ("cod_modalidade"                );
        $this->obISelectModalidade->addOption    ("","Selecione"                  );
        $this->obISelectModalidade->addOption    ("8","8 - Dispensa de Licitação" );
        $this->obISelectModalidade->addOption    ("9","9 - Inexibilidade"         );
        $this->obISelectModalidade->setNull      ( false                          );
        $this->obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$oculto."?". Sessao::getId(). "&inCodCompraDireta='+frm.inCodCompraDireta.value+'&stExercicioCompraDireta='+frm.stExercicioCompraDireta.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stFiltraCompraDireta=".$boFiltraCompraDireta."&numCompraDireta='+document.getElementById('hdnNumCompraDireta').value, 'carregaCompraDireta');");

        $this->obCmbCompraDireta = new Select();
        $this->obCmbCompraDireta->setName     ( 'inCodCompraDireta'          );
        $this->obCmbCompraDireta->setRotulo   ( 'Compra Direta'              );
        $this->obCmbCompraDireta->setTitle    ( 'Selecione a Compra Direta.' );
        $this->obCmbCompraDireta->setId       ( 'inCodCompraDireta'          );
        $this->obCmbCompraDireta->setCampoID  ( 'cod_compra_direta'          );
        $this->obCmbCompraDireta->setCampoDesc( 'cod_compra_direta'          );
        $this->obCmbCompraDireta->addOption   ( '','Selecione'               );
        $this->obCmbCompraDireta->setNull     ( false                        );
        $this->obCmbCompraDireta->preencheCombo( $rsCompraDireta      );
        $this->obCmbCompraDireta->obEvento->setOnChange("ajaxJavaScript('".$oculto."?".Sessao::getId()."&stExercicioCompraDireta=' +frm.stExercicioCompraDireta.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+ '&inCodCompraDireta='+this.value, 'carregaDadosCompraDireta');");

        $dtCompraDireta = (isset($dtCompraDireta)) ? $dtCompraDireta : '';

        $this->obHdnDtCompraDireta = new Hidden();
        $this->obHdnDtCompraDireta->setName( 'hdnDtCompraDireta' );
        $this->obHdnDtCompraDireta->setId( 'hdnDtCompraDireta' );
        $this->obHdnDtCompraDireta->setValue( $dtCompraDireta );

        $this->obHdnNumCompraDireta = new Hidden();
        $this->obHdnNumCompraDireta->setName( 'hdnNumCompraDireta' );
        $this->obHdnNumCompraDireta->setId( 'hdnNumCompraDireta' );
    }

    public function geraFormulario(&$obFormulario)
    {
        Sessao::write('IMontaNumeroCompraDireta', $this);
        $obFormulario->addComponente( $this->obExercicio );
        if ( !$this->getEntidadeUsuario() ) {
            $obFormulario->addComponente( $this->obITextBoxSelectEntidadeGeral );
        } else {
            $obFormulario->addComponente( $this->obITextBoxSelectEntidadeUsuario );
        }
        
        $obFormulario->addComponente( $this->obISelectModalidade );
        $obFormulario->addComponente( $this->obCmbCompraDireta );
        $obFormulario->addHidden    ( $this->obHdnDtCompraDireta );
        $obFormulario->addHidden    ( $this->obHdnNumCompraDireta );
    }
}
?>
