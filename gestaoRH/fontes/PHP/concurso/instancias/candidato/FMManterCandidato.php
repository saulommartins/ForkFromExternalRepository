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
* Página de Formulário Candidato
* Data de Criação: 30/06/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php");

$stPrograma = "ManterCandidato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRConcursoCandidato = new RConcursoCandidato;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty($stAcao) ) {
    $stAcao = "incluir";
}

$obRConcursoCandidato->obRConcursoConcurso->recuperaConfiguracao( $arConfiguracao );
foreach ($arConfiguracao as $key => $valor) {
    if ( $key == 'mascara_nota'.Sessao::getEntidade() ) {
        $stMascaraNota = $valor;
    }
    if ( $key == 'tipo_portaria_edital'.Sessao::getEntidade() ) {
            $inTipoNormaEdital = $valor;
    }
}

// mostra atributos selecionados
/*if ($stAcao == "incluir") {
    $obRConcursoCandidato->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoCandidato =  array( "cod_candidato"    => $_REQUEST["inCodCandidato"] );
    $obRConcursoCandidato->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRConcursoCandidato->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

}*/

/***************************************/
//Busca os dados para ALTERAÇÃO
//***************************************//

if ($stAcao == 'alterar') {
    $obRConcursoCandidato->setCodCandidato( $_REQUEST['inCodCandidato'] );
    $obRConcursoCandidato->listarCandidato( $rsCandidato );
    $inCodCandidato  = $rsCandidato->getCampo( "cod_candidato");
    $inCodEdital     = $rsCandidato->getCampo( "cod_edital");
    $inTipoProva     = $rsCandidato->getCampo( "tipo_prova");
    $stNomeCandidato = $rsCandidato->getCampo( "nom_cgm" );
    $inNumCGM        = $rsCandidato->getCampo( "numcgm" );
    $inClassificacao = $rsCandidato->getCampo( "classificacao" );
    $inTipoProva     = $rsCandidato->getCampo( "tipo_prova");
    $stNotaMinima    = $rsCandidato->getCampo( "nota_minima");
    $boTitulacao     = $rsCandidato->getCampo( "avalia_titulacao" );
    $stNotaProva     = $rsCandidato->getCampo( "nota_prova" );
    $stNotaTitulacao = $rsCandidato->getCampo( "nota_titulacao" );
    $stNotaMedia     = $rsCandidato->getCampo( "media" );
    $stSituacao      = $rsCandidato->getCampo( "situacao" );

    if( $stNotaMedia == '0' )
        $stNotaMedia = '';

    if ($stSituacao != 'Sem nota') {
        $obMascara = New Mascara;
        $stNotaMedia = str_replace(".","",$stNotaMedia);
        $obMascara->preencheMascaraComZeros2( $stNotaMedia, $stMascaraNota );
        $stNotaMedia = ltrim($obMascara->getMascarado(), "0");

        $stNotaProva = str_replace(".","",$stNotaProva);
        $obMascara->preencheMascaraComZeros2( $stNotaProva, $stMascaraNota );
        $stNotaProva = ltrim($obMascara->getMascarado(),"0");
        $stNotaTitulacao = str_replace(".","",$stNotaTitulacao);
        $obMascara->preencheMascaraComZeros2( $stNotaTitulacao, $stMascaraNota );
        $stNotaTitulacao = ltrim($obMascara->getMascarado(),"0");
    }

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName		( "stAcao" 				);
$obHdnAcao->setValue	( $stAcao 				);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName		( "stCtrl" 				);
$obHdnCtrl->setValue	( "" 					);

$obHdnCodCandidato = new Hidden;
$obHdnCodCandidato->setName	( "inCodCandidato" 		);
$obHdnCodCandidato->setValue( "$inCodCandidato" 	);

$obHdnCodEdital = new Hidden;
$obHdnCodEdital->setName	( "inCodEdital" 		);
$obHdnCodEdital->setValue	( "$inCodEdital" 		);

$obHdnTipoProva = new Hidden;
$obHdnTipoProva->setName	( "inHdnTipoProva" 		);
$obHdnTipoProva->setValue	( $inTipoProva		    );

$obHdnNotaMinima = new Hidden;
$obHdnNotaMinima->setName	( "inHdnNotaMinima" 		);
$obHdnNotaMinima->setValue	( $stNotaMinima          );

$obHdnMedia = new Hidden;
$obHdnMedia->setName   ( "inHdnMedia" 		);
$obHdnMedia->setValue  ( $inMedia          );

$obHdnProvaTitulacao = new Hidden;
$obHdnProvaTitulacao->setName	( "inHdnProvaTitulacao" );
$obHdnProvaTitulacao->setValue	( $boTitulacao  		);

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName		( "inNumCGM" 			);
$obHdnNumCGM->setValue		( "$inNumCGM" 			);

$obTxtCodEdital = new TextBox;
$obTxtCodEdital->setRotulo    ( "Edital"   );
$obTxtCodEdital->setTitle     ( "Selecione o concurso".Sessao::getEntidade()."");
$obTxtCodEdital->setName      ( "inCodEdital" );
$obTxtCodEdital->setValue     ( $inCodEdital  );
$obTxtCodEdital->setMaxLength ( 5  );
$obTxtCodEdital->setSize      ( 10 );
$obTxtCodEdital->setNull      ( false );
$obTxtCodEdital->obEvento->setOnChange("buscaValor('preencheComboCargos');");

$obRConcursoCandidato->obRConcursoConcurso->listarConcursoHomologadoPorExercicio( $rsConcurso,$stFiltro );

$obCmbEdital = new Select;
$obCmbEdital->setRotulo       ( "Edital"				    );
$obCmbEdital->setName         ( "inTxtEdital" 			    );
$obCmbEdital->setStyle        ( "width: 200px"			    );
$obCmbEdital->setTitle        ( "Selecione o concurso".Sessao::getEntidade().""	    );
$obCmbEdital->setCampoID      ( "cod_edital" 	        );
$obCmbEdital->setCampoDesc    ( "nom_norma" 	        );
$obCmbEdital->addOption       ( "", "Selecione" 			);
$obCmbEdital->setValue        ( $inCodEdital    			);
$obCmbEdital->setNull         ( false 			            );
$obCmbEdital->preencheCombo	  ( $rsConcurso 				);
$obCmbEdital->obEvento->setOnChange("buscaValor('preencheComboCargos');");

$obLblDtHomologacao = new Label;
$obLblDtHomologacao->setRotulo       ( 'Data de homologação'    );
$obLblDtHomologacao->setName         ( 'dtHomologacao'          );
$obLblDtHomologacao->setID           ( 'dtHomologacao'          );

//CONSULTA
$obBscCgm = new BuscaInner;
$obBscCgm->setRotulo			( "Candidato" 				);
$obBscCgm->setTitle				( "Informe o CGM do Candidato" );
$obBscCgm->setNull				( false 					);
$obBscCgm->setId				( "nom_cgm" 				);
$obBscCgm->obCampoCod->setName	( "inNumCGM" 				);
$obBscCgm->obCampoCod->setValue	( $inNumCGM 				);
$obBscCgm->obCampoCod->setAlign	("right");
$obBscCgm->obCampoCod->obEvento->setOnBlur("buscaValor( 'buscaCGM','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."' );");
$obBscCgm->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','nom_cgm','fisica','".Sessao::getId()."','800','550')" );

$obTxtCodCargo = new TextBox;
$obTxtCodCargo->setRotulo    ( "Cargo"   );
$obTxtCodCargo->setTitle     ( "Selecione o cargo a que o candidato");
$obTxtCodCargo->setName      ( "inCodCargo" );
$obTxtCodCargo->setValue     ( $inCodCargo  );
$obTxtCodCargo->setMaxLength ( 5  );
$obTxtCodCargo->setSize      ( 10 );
$obTxtCodCargo->setNull      ( false );

$obCmbCargos = new Select;
$obCmbCargos->setRotulo       ( "Cargo"				    );
$obCmbCargos->setName         ( "stCargo" 			    );
$obCmbCargos->setStyle        ( "width: 200px"			    );
$obCmbCargos->setTitle        ( "Selecione o cargo a que o candidato"	    );
$obCmbCargos->setCampoID      ( "cod_cargo" 	        );
$obCmbCargos->setCampoDesc    ( "descricao" 	        );
$obCmbCargos->addOption       ( "", "Selecione" 			);
$obCmbCargos->setValue        ($inCodCargo     			);
$obCmbCargos->setNull         ( false 			            );

$obLblEndereco = new Label;
$obLblEndereco->setRotulo	( 'Endereço' 		);
$obLblEndereco->setId   	( 'stEndereco'		);

$obLblEstado = new Label;
$obLblEstado->setRotulo		( 'Estado' 		);
$obLblEstado->setId   		( 'stEstado'	);

$obLblCidade = new Label;
$obLblCidade->setRotulo		( 'Cidade' 		);
$obLblCidade->setId   		( 'stCidade'	);

$obLblBairro = new Label;
$obLblBairro->setRotulo		( 'Bairro' 		);
$obLblBairro->setId   		( 'stBairro'	);

$obLblCEP = new Label;
$obLblCEP->setRotulo		( 'CEP' 		);
$obLblCEP->setId   		    ( 'stCep'   	);

$obLblFoneRes = new Label;
$obLblFoneRes->setRotulo	( 'Telefone residencial' 		);
$obLblFoneRes->setId   		( 'stFoneRes'	);

$obLblFoneCel = new Label;
$obLblFoneCel->setRotulo	( 'Telefone Celular' 		);
$obLblFoneCel->setId   		( 'stFoneCel'	);

$obLblMail = new Label;
$obLblMail->setRotulo		( 'e-mail' 		);
$obLblMail->setId   	    ( 'stEmail'   	);

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//****************************************************
//
//Componentes para alteração - classificacao candidato
//
//****************************************************

$obLblCodEdital = new Label;
$obLblCodEdital->setRotulo    ( "Edital"   );
$obLblCodEdital->setValue     ( $inCodEdital  );

$obLblCandidato = new Label;
$obLblCandidato->setRotulo    ( "Candidato"   );
$obLblCandidato->setValue     ( $inNumCGM ." - ". $stNomeCandidato  );

$obTxtProvaPratica = new TextBox;
$obTxtProvaPratica->setRotulo    ( "Prova prática"   );
$obTxtProvaPratica->setTitle     ( "Informa a nota da prova prática");
$obTxtProvaPratica->setName      ( "stNotaProvaPratica" );
$obTxtProvaPratica->setValue     ( $stNotaProva  );
$obTxtProvaPratica->setMaxLength ( 6  );
$obTxtProvaPratica->setSize      ( 10 );
$obTxtProvaPratica->setNull      ( false );
$obTxtProvaPratica->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraNota."', this, event);");
$obTxtProvaPratica->obEvento->setOnChange("buscaValor('calculaMedia');");

$obTxtProvaTeoricoPratica = new TextBox;
$obTxtProvaTeoricoPratica->setRotulo    ( "Prova teórico/prática"   );
$obTxtProvaTeoricoPratica->setTitle     ( "Informa a nota da prova teórico/prática");
$obTxtProvaTeoricoPratica->setName      ( "stNotaProvaTeoricoPratica" );
$obTxtProvaTeoricoPratica->setValue     ( $stNotaProva  );
$obTxtProvaTeoricoPratica->setMaxLength ( 10  );
$obTxtProvaTeoricoPratica->setSize      ( 10 );
$obTxtProvaTeoricoPratica->setNull      ( false );
$obTxtProvaTeoricoPratica->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraNota."', this, event);");
$obTxtProvaTeoricoPratica->obEvento->setOnChange("buscaValor('calculaMedia');");

$obTxtProvaTitulacao = new TextBox;
$obTxtProvaTitulacao->setRotulo    ( "Titulação"   );
$obTxtProvaTitulacao->setTitle     ( "Informa a nota para titulação");
$obTxtProvaTitulacao->setName      ( "stNotaTitulacao" );
$obTxtProvaTitulacao->setValue     ( $stNotaTitulacao  );
$obTxtProvaTitulacao->setMaxLength ( 10  );
$obTxtProvaTitulacao->setSize      ( 10 );
$obTxtProvaTitulacao->setNull      ( false );
$obTxtProvaTitulacao->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraNota."', this, event)");
$obTxtProvaTitulacao->obEvento->setOnChange("buscaValor('calculaMedia');");

$obTxtMedia = new TextBox;
$obTxtMedia->setRotulo    ( "Média"   );
$obTxtMedia->setName      ( "stMedia" );
$obTxtMedia->setValue     ( $stNotaMedia  );
$obTxtMedia->setMaxLength ( 10  );
$obTxtMedia->setSize      ( 10 );
$obTxtMedia->setDisabled  ( true  );
$obTxtMedia->setStyle     ( "color: #333333"  );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction		( $pgProc 					);
$obForm->setTarget      ( "oculto" 					);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm			( $obForm		);
$obFormulario->addHidden		( $obHdnAcao		);
$obFormulario->addHidden		( $obHdnCtrl		);
$obFormulario->addTitulo		( "Dados do Candidato"	);

if ($stAcao=='incluir') {
    $obFormulario->addComponenteComposto( $obTxtCodEdital, $obCmbEdital );
    $obFormulario->addComponente( $obLblDtHomologacao);
    $obFormulario->addComponente( $obBscCgm 		    );
    $obFormulario->addComponenteComposto($obTxtCodCargo, $obCmbCargos );
    $obFormulario->addTitulo	( "Dados para correspondência"	);
    $obFormulario->addComponente( $obLblEndereco 	    );
    $obFormulario->addComponente( $obLblEstado 	        );
    $obFormulario->addComponente( $obLblCidade   	    );
    $obFormulario->addComponente( $obLblBairro 	        );
    $obFormulario->addComponente( $obLblCEP          	);
    $obFormulario->addTitulo	( "Dados para contato"	);
    $obFormulario->addComponente( $obLblFoneRes      	);
    $obFormulario->addComponente( $obLblFoneCel      	);
    $obFormulario->addComponente( $obLblMail          	);
} else {
    $obFormulario->addHidden    ( $obHdnCodCandidato      );
    $obFormulario->addHidden    ( $obHdnNumCGM 		      );
    $obFormulario->addHidden    ( $obHdnCodEdital         );
    $obFormulario->addHidden    ( $obHdnTipoProva         );
    $obFormulario->addHidden    ( $obHdnNotaMinima        );
    $obFormulario->addHidden    ( $obHdnMedia             );
}

if ($stAcao=='alterar') {
    $obFormulario->addComponente( $obLblCodEdital          	);
    $obFormulario->addComponente( $obLblCandidato          	);
    $obFormulario->addTitulo	( "Nota das avaliações"	    );
    if ($inTipoProva == 1) {
        $obFormulario->addComponente( $obTxtProvaPratica	    );
    } else {
        $obFormulario->addComponente( $obTxtProvaTeoricoPratica );
    }
    if ($boTitulacao == 't') {
        $obFormulario->addHidden( $obHdnProvaTitulacao      );
        $obFormulario->addComponente( $obTxtProvaTitulacao      );
    }
    $obFormulario->addComponente( $obTxtMedia               );
}

/*if ($stAcao == 'incluir') {
    $obMontaAtributos->geraFormulario( $obFormulario      );
}*/

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
