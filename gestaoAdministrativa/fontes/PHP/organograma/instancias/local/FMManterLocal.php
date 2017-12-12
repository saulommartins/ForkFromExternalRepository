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
* Arquivo de instância para manutenção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27827 $
$Name$
$Author: rodrigosoares $
$Date: 2008-01-30 08:03:41 -0200 (Qua, 30 Jan 2008) $

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRLocal   = new ROrganogramaLocal;

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == "alterar") {
    $inCodLocal = $_REQUEST['inCodLocal'];
    $obRLocal->setCodLocal( $inCodLocal );
    $obRLocal->listarLocal( $rsLocal );

    $stDescricao     = $obRLocal->getDescricao();
    $inCodLogradouro = $obRLocal->getCodLogradouro();
    $inNumero        = $obRLocal->getNumero();
    $inFone          = trim(substr($obRLocal->getFone(), 2   ));
    $inPrefixo       = trim(substr($obRLocal->getFone(), 0,2 ));
    $inRamal         = $obRLocal->getRamal();
    $boDificilAcesso = $obRLocal->getDificilAcesso();
    $boInsalubre     = $obRLocal->getInsalubre();
    $rsLogradouro = new RecordSet;
    $obRLocal->setCodLogradouro( $inCodLogradouro ) ;
    $obRLocal->listarLogradouros( $rsLogradouro );
    $stNomeLogradouro = $rsLogradouro->getCampo("tipo_nome");
}

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName              ( "stNomeLogradouro"              );
$obHdnNomLogradouro->setValue             ( $stNomeLogradouro               );

$obHdnCodLocal = new Hidden;
$obHdnCodLocal->setName                   ( "inCodLocal"                    );
$obHdnCodLocal->setValue                  ( $inCodLocal                     );

$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName              ( "inCodLogradouro"               );
$obHdnCodLogradouro->setValue             ( $inCodLogradouro                );

$obHdnSequencia = new Hidden;
$obHdnSequencia->setName                  ( "inCodSequencia"                );
$obHdnSequencia->setValue                 ( $inCodSequencia                 );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo                ( "Descrição"                     );
$obTxtDescricao->setTitle                 ( "Informe a descrição do local"  );
$obTxtDescricao->setName                  ( "stDescricao"                   );
$obTxtDescricao->setValue                 ( $stDescricao                    );
$obTxtDescricao->setSize                  ( 80                              );
$obTxtDescricao->setMaxLength             ( 80                              );
$obTxtDescricao->setNull                  ( false                           );
$obTxtDescricao->setFloat                 ( false                           );

$obTxtNumero = new TextBox;
$obTxtNumero->setRotulo                   ( "Número"                           );
$obTxtNumero->setTitle                    ( "Informe o número do logradouro"   );
$obTxtNumero->setSize                     ( 10                                 );
$obTxtNumero->setMaxLength                ( 5                                  );
$obTxtNumero->setNull                     ( true                               );
$obTxtNumero->setInteiro                  ( true                               );
$obTxtNumero->setName                     ( "inNumero"                         );
$obTxtNumero->setValue                    ( $inNumero                          );
$obTxtNumero->setNaoZero                  ( false                              );

$obTxtPrefixo = new TextBox;
$obTxtPrefixo->setRotulo                  ( "Fone"                      );
$obTxtPrefixo->setTitle                   ( "Informe o fone do local"   );
$obTxtPrefixo->setSize                    ( 4                           );
$obTxtPrefixo->setMaxLength               ( 2                           );
$obTxtPrefixo->setNull                    ( true                        );
$obTxtPrefixo->setInteiro                 ( true                        );
$obTxtPrefixo->setName                    ( "inPrefixo"                 );
$obTxtPrefixo->setValue                   ( $inPrefixo                  );
$obTxtPrefixo->setNaoZero                 ( false                       );

$obTxtFone = new TextBox;
$obTxtFone->setRotulo                     ( "Fone"                      );
$obTxtFone->setTitle                      ( "Informe o fone do local"   );
$obTxtFone->setSize                       ( 12                          );
$obTxtFone->setMaxLength                  ( 10                          );
$obTxtFone->setNull                       ( true                        );
$obTxtFone->setInteiro                    ( true                        );
$obTxtFone->setName                       ( "inFone"                    );
$obTxtFone->setValue                      ( $inFone                     );
$obTxtFone->setNaoZero                    ( false                       );

$obTxtRamal = new TextBox;
$obTxtRamal->setRotulo                    ( "Ramal"                     );
$obTxtRamal->setTitle                     ( "Informe o ramal do local"   );
$obTxtRamal->setSize                      ( 10                          );
$obTxtRamal->setMaxLength                 ( 5                           );
$obTxtRamal->setNull                      ( true                        );
$obTxtRamal->setInteiro                   ( true                        );
$obTxtRamal->setName                      ( "inRamal"                   );
$obTxtRamal->setValue                     ( $inRamal                    );
$obTxtRamal->setNaoZero                   ( false                       );

//Monta radio para definir o tipo de acesso
$obRdbAcessoDificil = new Radio;
$obRdbAcessoDificil->setRotulo            ( "Difícil acesso"                                    );
$obRdbAcessoDificil->setTitle             ( "Informe se o local é de difícil acesso"            );
$obRdbAcessoDificil->setName              ( "boDificilAcesso"                                   );
$obRdbAcessoDificil->setValue             ( "t"                                                 );
$obRdbAcessoDificil->setLabel             ( "sim"                                               );
$obRdbAcessoDificil->setChecked           ( $boDificilAcesso == 't'                             );
$obRdbAcessoDificil->setNull              ( true                                                );

$obRdbAcessoFacil = new Radio;
$obRdbAcessoFacil->setRotulo              ( "Difícil acesso"                                    );
$obRdbAcessoFacil->setTitle               ( "Informe se o local é de difícil acesso"            );
$obRdbAcessoFacil->setName                ( "boDificilAcesso"                                   );
$obRdbAcessoFacil->setValue               ( "f"                                                 );
$obRdbAcessoFacil->setLabel               ( "não"                                               );
$obRdbAcessoFacil->setChecked             ( ($boDificilAcesso == 'f'  or !$boDificilAcesso)     );
$obRdbAcessoFacil->setNull                ( true                                                );

//Monta radio para definir local insalubre
$obRdbLocalInsalubre = new Radio;
$obRdbLocalInsalubre->setRotulo           ( "Local insalubre"                           );
$obRdbLocalInsalubre->setTitle            ( "Informe se o local é insalubre"            );
$obRdbLocalInsalubre->setName             ( "boInsalubre"                               );
$obRdbLocalInsalubre->setValue            ( "t"                                         );
$obRdbLocalInsalubre->setLabel            ( "sim"                                       );
$obRdbLocalInsalubre->setChecked          ( $boInsalubre == 't'                         );
$obRdbLocalInsalubre->setNull             ( true                                        );

$obRdbLocal = new Radio;
$obRdbLocal->setRotulo                    ( "Local insalubre"                           );
$obRdbLocal->setTitle                     ( "Informe se o local é insalubre"            );
$obRdbLocal->setName                      ( "boInsalubre"                               );
$obRdbLocal->setValue                     ( "f"                                         );
$obRdbLocal->setLabel                     ( "não"                                       );
$obRdbLocal->setChecked                   ( ($boInsalubre == 'f' or !$boInsalubre)      );
$obRdbLocal->setNull                      ( true                                        );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo ( "Logradouro"                               );
$obBscLogradouro->setTitle  ( "Informe o logradouro do local" );
$obBscLogradouro->setId     ( "campoInner"                               );
$obBscLogradouro->setNull   ( false                                      );
$obBscLogradouro->setValue  ( $stNomeLogradouro                          );
$obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
$obBscLogradouro->obCampoCod->setValue ( $inCodLogradouro                );
$obBscLogradouro->obCampoCod->obEvento->setOnChange ( "buscaLogradouro();" );
$stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&stCadastro=trecho','800','550')";
$obBscLogradouro->setFuncaoBusca                    ( $stBusca );

$obForm = new Form;
$obForm->setAction            ( $pgProc );
$obForm->setTarget            ( "oculto" );

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;
$arBotoes = array( $obBtnOK, $obBtnLimpar );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm             );
$obFormulario->setAjuda      ( 'UC-01.05.03'       );
$obFormulario->addHidden     ( $obHdnAcao          );
$obFormulario->addHidden     ( $obHdnCtrl          );
$obFormulario->addHidden     ( $obHdnNomLogradouro );
$obFormulario->addHidden     ( $obHdnCodLocal      );
$obFormulario->addHidden     ( $obHdnCodLogradouro );
$obFormulario->addHidden     ( $obHdnSequencia     );
$obFormulario->addTitulo     ( "Dados do Local"    );
$obFormulario->addComponente ( $obTxtDescricao     );
$obFormulario->addComponente ( $obBscLogradouro    );
$obFormulario->addComponente ( $obTxtNumero        );

$obFormulario->agrupaComponentes (array( $obTxtPrefixo, $obTxtFone) );
$obFormulario->addComponente ( $obTxtRamal         );

$obFormulario->addComponenteComposto( $obRdbAcessoDificil , $obRdbAcessoFacil  );
$obFormulario->addComponenteComposto( $obRdbLocalInsalubre , $obRdbLocal       );

if ($stAcao == "incluir") {
    $obFormulario->defineBarra( $arBotoes );
} else {
    $stLink = $pgList."?".Sessao::getId()."&inCodigo=".$_REQUEST['inCodigo'];
    $obFormulario->Cancelar ($stLink);
}

$obFormulario->show  ();

if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    SistemaLegado::executaFramePrincipal($js);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
