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
* Página de Formulario de Inclusao/Alteracao da Configuração de Salário Família

* Data de Criação: 19/04/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 31475 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.44
*/

//Ticket #13872

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                   );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php"                           );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                            );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSalarioFamilia";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

$obEvento = new RFolhaPagamentoEvento;
$obEvento->listarTiposEventoSalarioFamilia( $rsTiposEventos, $boTransacao = "" );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obRFolhaPagamentoSalarioFamilia = new RFolhaPagamentoSalarioFamilia;
$obRFolhaPagamentoSalarioFamilia->listarSalarioFamilia( $rsSalarioFamilia );

$obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->listarTodosRegimePrevidencia( $rsRegimePrevidencia );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoFolhaSituacao->consultarFolha();

if ($stAcao == "alterar") {
    $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia( $_GET["inCodRegimePrevidencia"] );
    $obRFolhaPagamentoSalarioFamilia->setTimestamp( $_GET["stTimestamp"] );

    $obRFolhaPagamentoSalarioFamilia->consultarSalarioFamilia();
    $inCodRegimePrevidencia = $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getCodRegimePrevidencia();
    $stTimestamp            = $obRFolhaPagamentoSalarioFamilia->getTimestamp();
    $stRegimePrevidenciario = $obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->getRegimePrevidenciario();
    $inIdadeLimite          = $obRFolhaPagamentoSalarioFamilia->getIdadeLimite();
    $dtVigencia             = $obRFolhaPagamentoSalarioFamilia->getVigencia();
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o codigo do RegimePrevidencia
$obHdnCodRegimePrevidencia = new Hidden;
$obHdnCodRegimePrevidencia->setName ( "stCodRegime" );
$obHdnCodRegimePrevidencia->setValue( $inCodRegimePrevidencia );

//Define o codigo do RegimePrevidencia
$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName ( "stTimestamp" );
$obHdnTimestamp->setValue( $stTimestamp );

$boEntrou = false;
$rsSalarioFamilia->setPrimeiroElemento();
$rsRegimePrevidencia->setPrimeiroElemento();

if ($stAcao == "incluir") {
    //Define objetos RADIO para armazenar o TIPO dos Itens
    for ( $i=0 ; $i<$rsRegimePrevidencia->getNumLinhas() ; $i++ ) {
        $obRdbRegimeRGPS[$i] = new Radio;
        $obRdbRegimeRGPS[$i]->setRotulo  ( "Regime Previdenciário"                                   );
        $obRdbRegimeRGPS[$i]->setTitle   ( "Informe o regime previdenciário para configuração do salário-família." );
        $obRdbRegimeRGPS[$i]->setName    ( "stCodRegime"                                             );
        $obRdbRegimeRGPS[$i]->setid      ( "stCodRegime".$rsRegimePrevidencia->getCampo("descricao") );
        $obRdbRegimeRGPS[$i]->setLabel   ( $rsRegimePrevidencia->getCampo("descricao")               );
        $obRdbRegimeRGPS[$i]->setValue   ( $rsRegimePrevidencia->getCampo("cod_regime_previdencia")  );

        //Se este regime já possuir uma configuração
        if ( $rsRegimePrevidencia->getCampo("cod_regime_previdencia") == $rsSalarioFamilia->getCampo("cod_regime_previdencia") ) {
            //INÍCIO - Utilizado no label abaixo
            if ($i==0) {
                $stPrevidencias = $rsRegimePrevidencia->getCampo("descricao");
            } elseif ( $i < ($rsRegimePrevidencia->getNumLinhas()-1) ) {
                $stPrevidencias = $stPrevidencias.", ".$rsRegimePrevidencia->getCampo("descricao");
            } else {
                $stPrevidencias = $stPrevidencias." e ".$rsRegimePrevidencia->getCampo("descricao");
            }
            //FIM - Utilizado no label abaixo

            $obRdbRegimeRGPS[$i]->setDisabled ( true                                                 );
            $obRdbRegimeRGPS[$i]->setChecked ( false                                                 );
            $rsSalarioFamilia->proximo();
        } else {
            if (!$boEntrou) {
                $obRdbRegimeRGPS[$i]->setChecked  ( true                                             );
                $boEntrou = true;
            } else {
                $obRdbRegimeRGPS[$i]->setChecked  ( false                                            );
            }
        }
        $obRdbRegimeRGPS[$i]->setNull    ( false                                                     );
        $rsRegimePrevidencia->proximo();
    }
} else {
    //Define objeto LABEL para mostrar o regime previdenciario
    $obLabelRegimePrevidenciario = new Label;
    $obLabelRegimePrevidenciario->setRotulo ( "Regime Previdenciário" );
    $obLabelRegimePrevidenciario->setName   ( "stLblRegimePrevidenciario" );
    $obLabelRegimePrevidenciario->setId     ( "stLblRegimePrevidenciario" );
    $obLabelRegimePrevidenciario->setValue  ( $stRegimePrevidenciario );
}

//Define objeto LABEL para mostrar a situação do cadastro
$obLabelSituacao = new Label;
$obLabelSituacao->setRotulo ( "Situação" );
$obLabelSituacao->setName   ( "stLblSituacao" );
$obLabelSituacao->setId     ( "stLblSituacao" );
$obLabelSituacao->setValue  ( "O salário-família já está configurado para o(s) regime(s) de previdência: ".$stPrevidencias."." );

$arObBscTipoEvento = array();

$rsTiposEventos->setPrimeiroElemento();
for ( $i=1 ; $i<=$rsTiposEventos->getNumLinhas() ; $i++ ) {
    $inCodTipo      = $rsTiposEventos->getCampo("cod_tipo");
    $stIDCampo      = "stTipoEvento".$inCodTipo;
    $stNameCampoCod = "inTipoEvento".$inCodTipo;

    $stCodigoEvento    = '';
    $stDescricaoEvento = '';

    for ( $j=0 ; $j<count($obRFolhaPagamentoSalarioFamilia->arFolhaPagamentoEvento) ; $j++ ) {
        if ( $inCodTipo == $obRFolhaPagamentoSalarioFamilia->arRFolhaPagamentoEventoSalarioFamilia[$j]->getCodTipoEventoSalarioFamilia() ) {
            $stCodigoEvento = $obRFolhaPagamentoSalarioFamilia->arFolhaPagamentoEvento[$j]->getCodigo();
            $stDescricaoEvento   = $obRFolhaPagamentoSalarioFamilia->arFolhaPagamentoEvento[$j]->getDescricao();
        }
    }

    switch ($inCodTipo) {
        case 1:
            $stNatureza = "P";
            break;
        case 2:
            $stNatureza = "B";
            break;
    }

    $arObBscTipoEvento[$i] = new BuscaInner;
    $arObBscTipoEvento[$i]->setRotulo                         ( $rsTiposEventos->getCampo("descricao")      );
    $arObBscTipoEvento[$i]->setTitle                          ( "Informe o evento para o pagamento do salário-família." );
    $arObBscTipoEvento[$i]->setNull                           ( false                                       );
    $arObBscTipoEvento[$i]->setId                             ( $stIDCampo                                  );
    $arObBscTipoEvento[$i]->obCampoCod->setName               ( $stNameCampoCod                             );
    $arObBscTipoEvento[$i]->obCampoCod->setId                 ( $stNameCampoCod                             );
    $arObBscTipoEvento[$i]->obCampoCod->setMascara            ( $stMascaraEvento                            );
    $arObBscTipoEvento[$i]->obCampoCod->setValue              ( $stCodigoEvento                             );
    $arObBscTipoEvento[$i]->obCampoCod->setAlign              ( "LEFT"                                      );
    $arObBscTipoEvento[$i]->obCampoCod->setMascara            ( $stMascaraEvento                            );
    $arObBscTipoEvento[$i]->obCampoCod->setPreencheComZeros   ( "E"                                         );
    $arObBscTipoEvento[$i]->obCampoCod->obEvento->setOnChange ( "preencheEvento( this, '".$stIDCampo."', '".$stNatureza."' );" );
    $arObBscTipoEvento[$i]->obCampoCod->setMaxLength          ( 10                                          );
    $arObBscTipoEvento[$i]->obCampoCod->setSize               ( 10                                          );
    $arObBscTipoEvento[$i]->setValue                          ( $stDescricaoEvento                          );
    $arObBscTipoEvento[$i]->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','".$stNameCampoCod."','".$stIDCampo."','','".Sessao::getId()."&stNatureza=".$stNatureza."&boEventoSistema=true','800','550')" );
    $rsTiposEventos->proximo();
}

//Define objeto TEXTBOX para armazenar a DESCRICAO do modelo
$obTxtIdadeLimite = new TextBox;
$obTxtIdadeLimite->setRotulo            ( "Idade Limite"  );
$obTxtIdadeLimite->setTitle             ( "Informe a idade limite para o pagamento do salário-família." );
$obTxtIdadeLimite->setName              ( "inIdadeLimite" );
$obTxtIdadeLimite->setId                ( "inIdadeLimite" );
$obTxtIdadeLimite->setValue             ( $inIdadeLimite  );
$obTxtIdadeLimite->setInteiro           ( true            );
$obTxtIdadeLimite->setSize              ( 3               );
$obTxtIdadeLimite->setMaxLength         ( 3               );
$obTxtIdadeLimite->setNull              ( false           );

//Valor inicial do salario
$obTxtSalarioInicial = new Moeda;
$obTxtSalarioInicial->setRotulo    ( "*Valor Inicial do Salário");
$obTxtSalarioInicial->setTitle     ( "Informe o valor inicial do salário-família da faixa do salário para concessão." );
$obTxtSalarioInicial->setName      ( "inSalarioInicial" );
$obTxtSalarioInicial->setValue     ( $inSalarioInicial  );
$obTxtSalarioInicial->setMaxLength ( 14  );
$obTxtSalarioInicial->setSize      ( 15  );
$obTxtSalarioInicial->setNull      ( true );

//Valor final do salario
$obTxtSalarioFinal = new Moeda;
$obTxtSalarioFinal->setRotulo    ( "*Valor Final do Salário");
$obTxtSalarioFinal->setTitle     ( "Informe o valor inicial do salário-família da faixa do salário para concessão." );
$obTxtSalarioFinal->setName      ( "inSalarioFinal" );
$obTxtSalarioFinal->setValue     ( $inSalarioFinal  );
$obTxtSalarioFinal->setMaxLength ( 14  );
$obTxtSalarioFinal->setSize      ( 15  );
$obTxtSalarioFinal->setNull      ( true );

//Valor final do salario
$obTxtValorPagar = new Moeda;
$obTxtValorPagar->setRotulo    ( "*Valor a Pagar");
$obTxtValorPagar->setTitle     ( "Informe o valor a ser pago." );
$obTxtValorPagar->setName      ( "inValorPagar" );
$obTxtValorPagar->setValue     ( $inValorPagar  );
$obTxtValorPagar->setMaxLength ( 14  );
$obTxtValorPagar->setSize      ( 15  );
$obTxtValorPagar->setNull      ( true );

$obLimparDadosFaixasConcessões = new Button;
$obLimparDadosFaixasConcessões->setName                    ( "btnLimparForm"  );
$obLimparDadosFaixasConcessões->setValue                   ( "Limpar"         );
$obLimparDadosFaixasConcessões->setTipo                    ( "button"         );
$obLimparDadosFaixasConcessões->obEvento->setOnClick       ( "limparDadosFaixasConcessões()" );
$obLimparDadosFaixasConcessões->setDisabled                ( false            );

$obBtnIncluir = new Button;
$obBtnIncluir->setName                    ( "obBtnIncluir"              );
$obBtnIncluir->setValue                   ( "Incluir"                   );
$obBtnIncluir->setTipo                    ( "button"                    );
$obBtnIncluir->obEvento->setOnClick       ( "incluirFaixasConcessoes()" );
$obBtnIncluir->setDisabled                ( false                       );

$botoesForm = array ( $obBtnIncluir , $obLimparDadosFaixasConcessões );

$obSpnConcessoesCadastradas = new Span;
$obSpnConcessoesCadastradas->setId ( "spnConcessoesCadastradas" );

$obDtVigencia = new Data;
$obDtVigencia->setName                       ( "dtVigencia"                  );
$obDtVigencia->setValue                      ( $dtVigencia                   );
$obDtVigencia->setRotulo                     ( "Vigência"                    );
$obDtVigencia->setNull                       ( false                         );
$obDtVigencia->setTitle                      ( 'Informe a data da vigência.' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

if ( ( $rsSalarioFamilia->getNumLinhas() == $rsRegimePrevidencia->getNumLinhas() ) && $stAcao != "alterar" ) {
    $obFormulario->addTitulo         ( "Configuração do Salário-Família"          );
    $obFormulario->addComponente     ( $obLabelSituacao                           );
} else {
    $obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addHidden         ( $obHdnCtrl                                 );
    $obFormulario->addHidden         ( $obHdnAcao                                 );
    $obFormulario->addHidden         ( $obHdnCodRegimePrevidencia                 );
    $obFormulario->addHidden         ( $obHdnTimestamp                            );
    $obFormulario->addTitulo         ( "Dados do Salário-Família"                 );

    if ($stAcao == "incluir") {
        $obFormulario->agrupaComponentes ( $obRdbRegimeRGPS                       );
    } else {
        $obFormulario->addComponente     ( $obLabelRegimePrevidenciario           );
    }
    
    for ( $i=1 ; $i<=$rsTiposEventos->getNumLinhas() ; $i++ ) {
        $obFormulario->addComponente ( $arObBscTipoEvento[$i]                     );
    }
    
    $obFormulario->addComponente     ( $obTxtIdadeLimite                          );
    $obFormulario->addTitulo         ( "Dados das Faixas de Concessões"           );
    $obFormulario->addComponente     ( $obTxtSalarioInicial                       );
    $obFormulario->addComponente     ( $obTxtSalarioFinal                         );
    $obFormulario->addComponente     ( $obTxtValorPagar                           );
    $obFormulario->agrupaComponentes ( $botoesForm                                );
    $obFormulario->addSpan           ( $obSpnConcessoesCadastradas                );
    $obFormulario->addComponente     ( $obDtVigencia                              );

    if ($stAcao == "incluir") {
        $obBtnLimpar = new Button;
        $obBtnLimpar->setName                    ( "btnLimparCampos"             );
        $obBtnLimpar->setValue                   ( "Limpar"                      );
        $obBtnLimpar->setTipo                    ( "button"                      );
        $obBtnLimpar->obEvento->setOnClick       ( "limparCampos();" );
        $obBtnLimpar->setDisabled                ( false                         );

        $obBtnOK = new Ok;
        $botoesForm  = array ( $obBtnOK , $obBtnLimpar );

        $obFormulario->defineBarra($botoesForm);
    } else {
        $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
    }

    sistemaLegado::executaFramePrincipal ("buscaValor('montaTela');");

    if ($stAcao == "incluir") {
        $obFormulario->setFormFocus( $obRdbRegimeRGPS[0]->getId() );
    } else {
        $obFormulario->setFormFocus( $arObBscTipoEvento[1]->obCampoCod->getId() );
    }
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
