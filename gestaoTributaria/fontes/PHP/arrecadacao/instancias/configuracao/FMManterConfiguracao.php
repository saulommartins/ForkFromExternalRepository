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
    * Página de Formulário da Caonfiguração do modulo arrecadação
    * Data de Criação   : 11/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log$
Revision 1.16  2007/09/25 14:50:06  vitor
Ticket#10246#

Revision 1.15  2007/02/16 11:40:44  dibueno
Bug #8432#

Revision 1.14  2007/02/16 10:11:25  dibueno
Inclusão de opção de Baixa Manual Única

Revision 1.13  2006/09/15 11:02:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

//include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_CLASSES.'componentes/IPopUpNorma.class.php';
include_once CAM_GT_ARR_NEGOCIO.'RARRConfiguracao.class.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRConfiguracao = new RARRConfiguracao;
$obErro = $obRARRConfiguracao->consultar();

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obRdbBaixaManualAceita = new Radio;
$obRdbBaixaManualAceita->setRotulo     ( "Diferença na Baixa Manual" );
$obRdbBaixaManualAceita->setName       ( "stBaixaManual" );
$obRdbBaixaManualAceita->setLabel      ( "Aceita" );
$obRdbBaixaManualAceita->setValue      ( "aceita" );
$obRdbBaixaManualAceita->setChecked    ( ( $obRARRConfiguracao->getBaixaManual() == "aceita" || $obRARRConfiguracao->getBaixaManual() == "" )                    );
$obRdbBaixaManualAceita->setTitle      ( "Forma de Tratamento"   );
$obRdbBaixaManualAceita->setNull       ( false                   );

$obRdbBaixaManualBloqueia = new Radio;
$obRdbBaixaManualBloqueia->setRotulo   ( "Diferença na Baixa Manual"                                );
$obRdbBaixaManualBloqueia->setName     ( "stBaixaManual"                                            );
$obRdbBaixaManualBloqueia->setLabel    ( "Bloqueia"                                                 );
$obRdbBaixaManualBloqueia->setValue    ( "bloqueia"                                                 );
$obRdbBaixaManualBloqueia->setChecked  ( ( $obRARRConfiguracao->getBaixaManual() == "bloqueia" )    );
$obRdbBaixaManualBloqueia->setNull     ( false                                                      );

$obRdbBaixaManualConfirma = new Radio;
$obRdbBaixaManualConfirma->setRotulo   ( "Diferença na Baixa Manual"                                );
$obRdbBaixaManualConfirma->setName     ( "stBaixaManual"                                            );
$obRdbBaixaManualConfirma->setLabel    ( "Confirma"                                                 );
$obRdbBaixaManualConfirma->setValue    ( "confirma"                                                 );
$obRdbBaixaManualConfirma->setChecked  ( ( $obRARRConfiguracao->getBaixaManual() == "confirma" )    );
$obRdbBaixaManualConfirma->setNull     ( false                                                      );

#====================================== BAIXA MANUAL UNICA
$obRdbBaixaManualUnicaSim = new Radio;
$obRdbBaixaManualUnicaSim->setRotulo     ( "Baixa Manual Única" );
$obRdbBaixaManualUnicaSim->setName       ( "stBaixaManualUnica" );
$obRdbBaixaManualUnicaSim->setLabel      ( "Aceita" );
$obRdbBaixaManualUnicaSim->setValue      ( "sim" );
$obRdbBaixaManualUnicaSim->setChecked    ( ( $obRARRConfiguracao->getBaixaManualUnica() == "sim" || $obRARRConfiguracao->getBaixaManual() == "" )                    );
$obRdbBaixaManualUnicaSim->setTitle      ( "Aceitar Baixa Manual de Parcela Única"   );
$obRdbBaixaManualUnicaSim->setNull       ( false                   );

$obRdbBaixaManualUnicaNao = new Radio;
$obRdbBaixaManualUnicaNao->setRotulo   ( "Baixa Manual Única"                                       );
$obRdbBaixaManualUnicaNao->setName     ( "stBaixaManualUnica"                                       );
$obRdbBaixaManualUnicaNao->setLabel    ( "Não"                                                      );
$obRdbBaixaManualUnicaNao->setValue    ( "nao"                                                      );
$obRdbBaixaManualUnicaNao->setChecked  ( ( $obRARRConfiguracao->getBaixaManualUnica() == "nao" )    );
$obRdbBaixaManualUnicaNao->setNull     ( false                                                      );

$obTextReceberDAVencida = new TextBox; //prazo para baixa manual DA vencida
$obTextReceberDAVencida->setName     ( "stPrazoReceberDA" );
$obTextReceberDAVencida->setTitle    ( "Prazo (em dias) para receber pagamentos da divida ativa após vencimento."  );
$obTextReceberDAVencida->setRotulo   ( "Baixa Manual D.A. Vencida" );
$obTextReceberDAVencida->setNull     ( false );
$obTextReceberDAVencida->setId       ( "stPrazoReceberDA" );
$obTextReceberDAVencida->setSize     ( 5 );
$obTextReceberDAVencida->setMascara  ( "999" );
$obTextReceberDAVencida->setInteiro  ( true );
$obTextReceberDAVencida->setValue    ( $obRARRConfiguracao->getBaixaManualDAVencida() );

$obFlValorMaximo = new Numerico;
$obFlValorMaximo->setName          ( "flValorMaximo"                                                            );
$obFlValorMaximo->setTitle         ( "Valor máximo a ser aceito em casos de diferença de pagamento."            );
$obFlValorMaximo->setValue         ( $obRARRConfiguracao->getValorMaximo()                                      );
$obFlValorMaximo->setRotulo        ( "Valor Máximo"                                                             );
$obFlValorMaximo->setNull          ( false                                                                      );

$obFlValorMinimo = new Numerico;
$obFlValorMinimo->setName          ( "flValorMinimoLacamentoAutomatico"                                         );
$obFlValorMinimo->setTitle         ( "Valor mínimo para lançamentos automáticos durante a baixa de pagamentos." );
$obFlValorMinimo->setValue         ( $obRARRConfiguracao->getMinimoLancamentoAutomatico()                       );
$obFlValorMinimo->setRotulo        ( "Valor Mínimo para Lançamentos Automáticos"                                );
$obFlValorMinimo->setNull          ( false                                                                      );

$obRdbFormaPercentual = new Radio;
$obRdbFormaPercentual->setRotulo   ( "Forma de Verificação do Valor"                                          );
$obRdbFormaPercentual->setName     ( "stFormaVerificacao"                                                     );
$obRdbFormaPercentual->setLabel    ( "Percentual"                                                             );
$obRdbFormaPercentual->setNull     ( false                                                                    );
$obRdbFormaPercentual->setChecked  ( ( $obRARRConfiguracao->getFormaVerificacao() == "percentual" || $obRARRConfiguracao->getFormaVerificacao() == ""   )   );
$obRdbFormaPercentual->setTitle    ( "Forma para apurar a diferença de pagamento"                             );
$obRdbFormaPercentual->setValue    ( "percentual"                                                             );

$obRdbFormaAbsoluto = new Radio;
//$obRdbFormaAbsoluto->setRotulo       ( "Número Inscrição Econômica"                                            );
$obRdbFormaAbsoluto->setName         ( "stFormaVerificacao"                                                     );
$obRdbFormaAbsoluto->setLabel        ( "Valor Absoluto"                                                         );
$obRdbFormaAbsoluto->setNull         ( false                                                                    );
$obRdbFormaAbsoluto->setChecked      ( ( $obRARRConfiguracao->getFormaVerificacao() == 'absoluto' )             );
//$obRdbFormaAbsoluto->setTitle        ( "Define se o número da inscrição econômica será informado ou gerado"     );
$obRdbFormaAbsoluto->setValue        ( "absoluto"                                                               );

$obBscConvenio = new BuscaInner;
$obBscConvenio->setRotulo( "Convênio para Parcelamentos" );
$obBscConvenio->setTitle( "Convênio a ser utilizado para parcelamentos de créditos" );
//$obBscConvenio->setId( "stConvenio" );
$obBscConvenio->obCampoCod->setName("inNumConvenio");
$obBscConvenio->obCampoCod->setValue( $obRARRConfiguracao->getConvenioParcelamento() );
$obBscConvenio->obCampoCod->obEvento->setOnChange("buscaValor('buscaConvenio');");
$obBscConvenio->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."convenio/FLProcurarConvenio.php','frm','inNumConvenio','','todos','".Sessao::getId()."','800','550');" );

$obRdbSupensaoSim = new Radio;
$obRdbSupensaoSim->setRotulo    ( "Ativar Suspensão para Valores Abaixo do Mínimo" );
$obRdbSupensaoSim->setName      ( "stSupensao"                                                      );
$obRdbSupensaoSim->setLabel     ( "Sim"                                                             );
$obRdbSupensaoSim->setNull      ( false                                                             );
$obRdbSupensaoSim->setChecked   ( ( $obRARRConfiguracao->getSuspensao() == 'sim' || $obRARRConfiguracao->getSuspensao() ==  '' ) );
$obRdbSupensaoSim->setTitle     ( "Ativa a suspensão automática de cálculos quando o valor estiver abaixo do configurado" );
$obRdbSupensaoSim->setValue     ( "sim"                                                             );

$obRdbSupensaoNao = new Radio;
$obRdbSupensaoNao->setName         ( "stSupensao"                                                   );
$obRdbSupensaoNao->setLabel        ( "Não"                                                          );
$obRdbSupensaoNao->setNull         ( false                                                          );
$obRdbSupensaoNao->setChecked      ( ( $obRARRConfiguracao->getSuspensao() == 'nao' )               );
$obRdbSupensaoNao->setValue        ( "nao" );

$obRdbEmissaoCarneSim = new Radio;
$obRdbEmissaoCarneSim->setRotulo    ( "*Emissão de Carnês para Contribuintes sem CPF/CNPJ." );
$obRdbEmissaoCarneSim->setName      ( "stEmissaoCarne"                                                  );
$obRdbEmissaoCarneSim->setLabel     ( "Emitir"                                                          );
$obRdbEmissaoCarneSim->setNull      ( false                                                             );
$obRdbEmissaoCarneSim->setChecked   ( ( $obRARRConfiguracao->getEmissaoCarne() == 'emitir' || $obRARRConfiguracao->getEmissaoCarne() ==  '' ) );
$obRdbEmissaoCarneSim->setTitle     ( "Forma de validação para a emissão de carnês de contribuintes sem CPF/CNPJ cadastrado no CGM." );
$obRdbEmissaoCarneSim->setValue     ( 'emitir'                                                          );

$obRdbEmissaoCarneNao = new Radio;
$obRdbEmissaoCarneNao->setName         ( "stEmissaoCarne"                                               );
$obRdbEmissaoCarneNao->setLabel        ( "Não Emitir"                                                   );
$obRdbEmissaoCarneNao->setNull         ( false                                                          );
$obRdbEmissaoCarneNao->setChecked      ( $obRARRConfiguracao->getEmissaoCarne() == 'naoemitir'          );
$obRdbEmissaoCarneNao->setValue        ( 'naoemitir' );

$obRdbEmissaoCarneIsentoNao = new Radio;
$obRdbEmissaoCarneIsentoNao->setRotulo    ( "*Emissão de Carnês para Contribuintes Isento." );
$obRdbEmissaoCarneIsentoNao->setName      ( "stEmissaoCarneIsento"                                                  );
$obRdbEmissaoCarneIsentoNao->setLabel     ( "Não"                                                          );
$obRdbEmissaoCarneIsentoNao->setNull      ( false                                                             );
$obRdbEmissaoCarneIsentoNao->setChecked   ( ($obRARRConfiguracao->getEmissaoCarneIsento() == 'nao' || $obRARRConfiguracao->getEmissaoCarneIsento() == "" )             );
$obRdbEmissaoCarneIsentoNao->setTitle     ( "Forma de validação para a emissão de carnês de contribuintes isentos." );
$obRdbEmissaoCarneIsentoNao->setValue     ( 'nao'                                                          );

$obRdbEmissaoCarneIsentoSim = new Radio;
$obRdbEmissaoCarneIsentoSim->setRotulo    ( "*Emissão de Carnês para Contribuintes Isento" );
$obRdbEmissaoCarneIsentoSim->setName      ( "stEmissaoCarneIsento"                                                  );
$obRdbEmissaoCarneIsentoSim->setLabel     ( "Sim"                                                          );
$obRdbEmissaoCarneIsentoSim->setNull      ( false                                                             );
$obRdbEmissaoCarneIsentoSim->setChecked   ( ($obRARRConfiguracao->getEmissaoCarneIsento() == 'sim'            ));
$obRdbEmissaoCarneIsentoSim->setValue     ( 'sim'                                                          );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "inExercicio" );

$stNorma = "&nbsp;";
$inNorma = '';
if($obRARRConfiguracao->getFundLegal()!=null){
    $obTNorma = new TNorma;
    
    $stFiltro = " WHERE cod_norma = ".$obRARRConfiguracao->getFundLegal();
    $obTNorma->recuperaNormasDecreto( $rsNorma, $stFiltro );
    if ( !$rsNorma->eof() ) {
        $inNorma = $rsNorma->getCampo( "cod_norma" );
        $stNorma = $rsNorma->getCampo( "nom_tipo_norma" )." ".$rsNorma->getCampo( "num_norma_exercicio" )." - ".$rsNorma->getCampo( "nom_norma" );
        $rsNorma->proximo();
    }
}

$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setRotulo           ( "Fundamentação Legal" );
$obIPopUpNorma->obInnerNorma->setTitle            ( "Norma que regulamenta a desoneração." );
$obIPopUpNorma->obInnerNorma->obCampoCod->setValue( $inNorma );
$obIPopUpNorma->obInnerNorma->setValue            ( $stNorma );

$obTextCarneSecretaria   = new TextBox;
$obTextCarneSecretaria->setName     ( "stCarneSecretaria" );
$obTextCarneSecretaria->setTitle    ( "Carnê Secretaria"  );
$obTextCarneSecretaria->setRotulo   ( "Carnê Secretaria" );
$obTextCarneSecretaria->setNull     ( false );
$obTextCarneSecretaria->setId       ( "stCarneSecretaria" );
$obTextCarneSecretaria->setSize     ( 80 );
$obTextCarneSecretaria->setMaxLength( 100 );
$obTextCarneSecretaria->setValue    ( $obRARRConfiguracao->getCarneSecretaria() );

$obTextCarneDepartamento = new TextBox;
$obTextCarneDepartamento->setName   ( "stCarneDepartamento" );
$obTextCarneDepartamento->setTitle  ( "Carnê Departamento " );
$obTextCarneDepartamento->setValue  ( $obRARRConfiguracao->getCarneDepartamento() );
$obTextCarneDepartamento->setRotulo ( "Carnê Departamento" );
$obTextCarneDepartamento->setNull   ( false );
$obTextCarneDepartamento->setId     ( "stCarneDepartamento" );
$obTextCarneDepartamento->setSize   ( 80 );
$obTextCarneDepartamento->setMaxLength ( 100 );

$obTextCarneDam          = new TextBox;
$obTextCarneDam->setName    ( "stCarneDam" );
$obTextCarneDam->setTitle   ( "Carnê Dam" );
$obTextCarneDam->setValue   ( $obRARRConfiguracao->getCarneDam() );
$obTextCarneDam->setRotulo  ( "Carnê Dam" );
$obTextCarneDam->setNull    ( false );
$obTextCarneDam->setId      ( "stCarneDam" );
$obTextCarneDam->setSize    ( 80 );
$obTextCarneDam->setMaxLength ( 100 );

$obRdbCobrarNotaAvulsaSim = new Radio;
$obRdbCobrarNotaAvulsaSim->setRotulo     ( "Cobrar Nota Avulsa" );
$obRdbCobrarNotaAvulsaSim->setName       ( "stNotaAvulsa" );
$obRdbCobrarNotaAvulsaSim->setLabel      ( "Sim" );
$obRdbCobrarNotaAvulsaSim->setValue      ( "sim" );
$obRdbCobrarNotaAvulsaSim->setChecked    ( ( $obRARRConfiguracao->getNotaAvulsa() == "sim" || $obRARRConfiguracao->getNotaAvulsa() == "" ) );
$obRdbCobrarNotaAvulsaSim->setTitle      ( "Definir se a nota avulsa será cobrada."   );
$obRdbCobrarNotaAvulsaSim->setNull       ( false                   );

$obRdbCobrarNotaAvulsaNao = new Radio;
$obRdbCobrarNotaAvulsaNao->setRotulo   ( "Cobrar Nota Avulsa" );
$obRdbCobrarNotaAvulsaNao->setName     ( "stNotaAvulsa" );
$obRdbCobrarNotaAvulsaNao->setLabel    ( "Não" );
$obRdbCobrarNotaAvulsaNao->setValue    ( "nao" );
$obRdbCobrarNotaAvulsaNao->setChecked  ( ( $obRARRConfiguracao->getNotaAvulsa() == "nao" ) );
$obRdbCobrarNotaAvulsaNao->setNull     ( false );

$obCmbViasNotaAvulsa = new Select;
$obCmbViasNotaAvulsa->setRotulo       ( "Nº de Vias da Nota Avulsa" );
$obCmbViasNotaAvulsa->setTitle        ( "Nº de vias da nota avulsa." );
$obCmbViasNotaAvulsa->setName         ( "cmbViasNota" );
$obCmbViasNotaAvulsa->addOption       ( "", "Selecione"  );
$obCmbViasNotaAvulsa->addOption       ( "2", "2"  );
$obCmbViasNotaAvulsa->addOption       ( "4", "4"  );
$obCmbViasNotaAvulsa->setValue        ( $obRARRConfiguracao->getQtdViasNotaAvulsa() );
$obCmbViasNotaAvulsa->setCampoId      ( "qtd_vias" );
$obCmbViasNotaAvulsa->setCampoDesc    ( "desc_vias" );
$obCmbViasNotaAvulsa->setNull         ( false );

$obRARRConfiguracao->setCodModulo ( 2 );
$obErro = $obRARRConfiguracao->consultar();
$obFlCodigoFEBRABAN = new TextBox;
$obFlCodigoFEBRABAN->setName          ( "inCodFEBRABAN" );
$obFlCodigoFEBRABAN->setInteiro       ( true );
$obFlCodigoFEBRABAN->setTitle         ( "Código FEBRABAN."            );
$obFlCodigoFEBRABAN->setValue         ( $obRARRConfiguracao->getCodFebraban() );
$obFlCodigoFEBRABAN->setRotulo        ( "Código FEBRABAN"                                                             );
$obFlCodigoFEBRABAN->setNull          ( false );
$obFlCodigoFEBRABAN->setSize      ( 4 );
$obFlCodigoFEBRABAN->setMaxLength ( 4 );
$obFlCodigoFEBRABAN->setMinLength ( 4 );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"              );
$obBtnClean->setValue                   ( "Cancelar"              );
$obBtnClean->setTipo                    ( "button"                );
$obBtnClean->obEvento->setOnClick       ( "document.frm.reset();" );
$obBtnClean->setDisabled                ( false                   );

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK , $obBtnClean );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm );
$obFormulario->addHidden             ( $obHdnExercicio );
$obFormulario->addHidden             ( $obHdnCtrl );
$obFormulario->addHidden             ( $obHdnAcao );
$obFormulario->addTitulo             ( "Dados para Configuração" );
$obFormulario->agrupaComponentes     ( array( $obRdbBaixaManualAceita, $obRdbBaixaManualBloqueia, $obRdbBaixaManualConfirma) );
$obFormulario->agrupaComponentes     ( array( $obRdbBaixaManualUnicaSim, $obRdbBaixaManualUnicaNao ) );
$obFormulario->addComponente         ( $obTextReceberDAVencida );
$obFormulario->addComponente         ( $obFlValorMaximo );
$obFormulario->addComponente         ( $obFlValorMinimo );
$obFormulario->addComponente         ( $obFlCodigoFEBRABAN );

$obFormulario->addComponenteComposto ( $obRdbFormaPercentual, $obRdbFormaAbsoluto );

$obFormulario->addComponente         ($obBscConvenio);

//$obFormulario->addComponenteComposto ( $obRdbValoresCalculados, $obRdbValoresInformados        );
$obFormulario->addComponenteComposto ( $obRdbSupensaoSim, $obRdbSupensaoNao );
$obFormulario->addComponenteComposto ( $obRdbEmissaoCarneSim, $obRdbEmissaoCarneNao );
$obFormulario->addComponenteComposto ( $obRdbEmissaoCarneIsentoSim, $obRdbEmissaoCarneIsentoNao );
$obIPopUpNorma->geraFormulario($obFormulario);
$obFormulario->addComponente ( $obTextCarneSecretaria );
$obFormulario->addComponente ( $obTextCarneDepartamento );
$obFormulario->addComponente ( $obTextCarneDam );
$obFormulario->addComponenteComposto ( $obRdbCobrarNotaAvulsaSim, $obRdbCobrarNotaAvulsaNao );
$obFormulario->addComponente ( $obCmbViasNotaAvulsa );
//$obFormulario->OK();
$obFormulario->defineBarra($botoesForm);
$obFormulario->show();

?>
