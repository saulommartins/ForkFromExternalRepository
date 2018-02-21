<?php

	// ini_set("display_errors", 1);
	// error_reporting(E_ALL);

	require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";

	class IPopUpConvenio extends Objeto
	{
	    public $obTxtExercicioConvenio;
	    public $obBuscaInner;
	    public $obSpanInfoAdicional;

	    public function IPopUpConvenio( &$obForm )
	    {
	        parent::Objeto();

	        $this->obTxtExercicioConvenio = new TextBox;
	        $this->obTxtExercicioConvenio->setRotulo ( 'Exercício do Convênio' );
	        $this->obTxtExercicioConvenio->setName   ( 'stExercicioConvenio'   );
	        $this->obTxtExercicioConvenio->setId     ( 'stExercicioConvenio'   );
	        $this->obTxtExercicioConvenio->setValue  ( Sessao::getExercicio()  );
	        $this->obTxtExercicioConvenio->setInteiro( true );

	        $this->obBuscaInner = new BuscaInner;
	        $this->obBuscaInner->obForm = &$obForm;
	        $this->obBuscaInner->setRotulo           ( 'Número do Convênio'                     );
	        $this->obBuscaInner->setTitle            ( 'Selecione o convênio na PopUp de busca' );
	        $this->obBuscaInner->obCampoCod->setName ( 'inNroConvenio'                          );
	        $this->obBuscaInner->obCampoCod->setId   ( 'inNroConvenio'                          );
	        $this->obBuscaInner->obCampoCod->setAlign( "left"                                   );
	        $this->obBuscaInner->setId               ( 'txtConvenio'                            );
	        $this->obBuscaInner->setNull             ( true );
	        $this->obBuscaInner->stTipoBusca = 'popup';

	        $this->obBuscaInner->setFuncaoBusca(
	        	"abrePopUp('".CAM_GPC_TCEMG_POPUPS."convenio/FLProcurarConvenio.php','".$this->obBuscaInner->obForm->getName()."','"
	        	.$this->obBuscaInner->obCampoCod->getName()."','".$this->obBuscaInner->getId()."','"
	        	.$this->obBuscaInner->stTipoBusca."','".Sessao::getId()
	        	."&inCodEntidade='+jQuery('#inCodEntidade').val()+'&stExercicioConvenio='+jQuery('#stExercicioConvenio').val(),'800','550');"
	        );

	        $this->obBuscaInner->setValoresBusca(
	        	CAM_GPC_TCEMG_POPUPS.'convenio/OCProcuraConvenio.php?'.Sessao::getId(), $this->obBuscaInner->obForm->getName()
	        );

	        $this->obSpanInfoAdicional = new Span;
	        $this->obSpanInfoAdicional->setId('spnInfoAdicionalConvenios');
	    }

	    public function geraFormulario($obFormulario)
	    {
	        $this->obTxtExercicioConvenio->obEvento->setOnChange(
	        	"jQuery('#".$this->obBuscaInner->obCampoCod->getId()."').val(''); jQuery('#"
	        	.$this->obBuscaInner->getId()."').html('&nbsp;'); jQuery('#spnInfoAdicionalConvenios').html('');"
	        );

	        $obFormulario->addComponente( $this->obTxtExercicioConvenio );
	        $obFormulario->addComponente( $this->obBuscaInner );
	        $obFormulario->addSpan      ( $this->obSpanInfoAdicional );
	    }
	}
