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
    * Página de Filtro Banco Recurso
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: FLBancoRecurso.php 60687 2014-11-10 11:28:34Z franver $

    * Casos de uso: uc-02.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");

$stPrograma = "BancoRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtroRelatorio');

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName    ( "stAcao" );
$obHdnAcao->setValue   ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName    ( "stCtrl" );
$obHdnCtrl->setValue   ( "" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName ( "stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCBancoRecurso.php" );

// Filtro Entidades
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario( $obForm );

// Filtro para Recurso
include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Filtro Estrutural
include_once ( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpEstruturalPlano.class.php" );
$obIIntervaloPopUpEstruturalPlano = new IIntervaloPopUpEstruturalPlano;

// define objeto TextBox para codigo reduzido inicial
$obTxtCodPlanoInicial = new TextBox;
$obTxtCodPlanoInicial->setName   ( "inCodPlanoInicial" );
$obTxtCodPlanoInicial->setRotulo ( "Código Reduzido" );
$obTxtCodPlanoInicial->setTitle  ( "Informe o Código Reduzido da Conta" );
// Define Objeto Label
$obLblCodPlano = new Label;

$obLblCodPlano->setValue(" até  ");

// define objeto TextBox para codigo reduzido final
$obTxtCodPlanoFinal = new TextBox;
$obTxtCodPlanoFinal->setName   ( "inCodPlanoFinal" );
$obTxtCodPlanoFinal->setRotulo ( "Código Reduzido" );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );

$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Descrição da Conta de Banco' );

// Filtro Ordenação
$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo   ( "Ordenação"                         );
$obCmbOrdem->setTitle    ( "Selecione a ordenação."            );
$obCmbOrdem->setName     ( "inOrdenacao"                       );
$obCmbOrdem->setStyle    ( "width: 150px"                      );
$obCmbOrdem->addOption   ( "0", "Selecione"                    );
$obCmbOrdem->addOption   ( "1", "Código Estrutural"                   );
$obCmbOrdem->addOption   ( "2", "Código Reduzido"                     );
$obCmbOrdem->addOption   ( "3", "Recurso"                      );
$obCmbOrdem->addOption   ( "4", "Banco/Agência/Conta Corrente" );
$obCmbOrdem->setNull     ( true );

// Organizando Filtros do Banco - Agência - Conta

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Recupera Mascara
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->listar( $rsSistemaContabil );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->listar( $rsClassificacaoContabil );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->listar( $rsRecurso );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

$arBancos = $rsBanco->getElementos();
foreach ($arBancos as $arBanco) {
    if ($arBanco['cod_banco'] != 0) {
        $arNewBancos[] = $arBanco;
    }
}
$rsBanco->setElementos( $arNewBancos );
$rsBanco->setNumLinhas( count( $arNewBancos ) );

$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"            );
$obTxtBanco->setId       ( "inNumBanco"            );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco'] );
$obTxtBanco->setRotulo   ( "Banco"                 );
$obTxtBanco->setMaxlength( 5                       );
$obTxtBanco->setTitle    ( "Selecione o banco"     );
$obTxtBanco->setDisabled ( $boDisabled             );
$obTxtBanco->setInteiro  ( true                    );
$obTxtBanco->obEvento->setOnChange  ( " if(this.value != ''){
                                            montaParametrosGET('MontaAgencia');
                                        } else {
                                            document.getElementById('inCodBanco').value = '';
                                            document.getElementById('inCodAgencia').value = '';
                                            document.getElementById('stContaCorrente').value = '';
                                        }
                                    ");

$obCmbBanco = new Select;
$obCmbBanco->setName      ( "stNomeBanco"   );
$obCmbBanco->setId        ( "stNomeBanco"   );
$obCmbBanco->setValue     ( $_REQUEST['inNumBanco']   );
$obCmbBanco->setDisabled  ( $boDisabled     );
$obCmbBanco->addOption    ( "", "Selecione" );
$obCmbBanco->setCampoId   ( "num_banco"     );
$obCmbBanco->setCampoDesc ( "nom_banco"     );
$obCmbBanco->preencheCombo( $rsBanco        );
$obCmbBanco->setNull(true);
$obCmbBanco->obEvento->setOnChange  ( " montaParametrosGET('MontaAgencia');");

$obTxtAgencia = new TextBox;
$obTxtAgencia->setName     ( "inNumAgencia"        );
$obTxtAgencia->setId       ( "inNumAgencia"        );
$obTxtAgencia->setValue    ( $_REQUEST['inNumAgencia'] );
$obTxtAgencia->setRotulo   ( "Agência"            );
$obTxtAgencia->setMaxLength( 10                    );
$obTxtAgencia->setTitle    ( "Selecione a agência" );
$obTxtAgencia->setDisabled ( $boDisabled           );
$obTxtAgencia->setNull(true);
$obTxtAgencia->obEvento->setOnChange  ( " montaParametrosGET('MontaContaCorrente'); ");

$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->setValue     ( $_REQUEST['inNumAgencia']  );
$obCmbAgencia->addOption    ( "", "Selecione"  );
$obCmbAgencia->setDisabled  ( $boDisabled      );
$obCmbAgencia->setNull(true);
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); ");

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo   ( "Conta Corrente");
$obCmbContaCorrente->setName      ( "stContaCorrente"    );
$obCmbContaCorrente->setId        ( "stContaCorrente"    );
$obCmbContaCorrente->setValue     ( $_REQUEST['stContaCorrente']   );
$obCmbContaCorrente->addOption    ( "", "Selecione"          );
$obCmbContaCorrente->setCampoId   ( "num_conta_corrente"     );
$obCmbContaCorrente->setCampoDesc ( "num_conta_corrente"     );
$obCmbContaCorrente->setDisabled  ( $boDisabled );
$obCmbContaCorrente->setNull(true);
$obCmbContaCorrente->setTitle("Selecione a Conta Corrente");
$obCmbContaCorrente->obEvento->setOnChange  ( " montaParametrosGET('BuscaContaCorrente'); ");

// Hidden Cod Banco
$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId ('inCodBanco');
//$obHdnBanco->setValue ( $request->get('inCodBanco') );

// Hidden Cod Agencia
$obHdnAgencia = new Hidden;
$obHdnAgencia->setName ( 'inCodAgencia' );
$obHdnAgencia->setId ( 'inCodAgencia' );
//$obHdnAgencia->setValue ( $request->get('inCodAgencia') );

//Define Objeto Hidden para Código da Conta
$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
//$obHdnCodConta->setValue( $inCodConta );

//Define Objeto Hidden para Código do Plano
$obHdnCodPlano = new Hidden;
$obHdnCodPlano->setName ( "inCodPlano" );
//$obHdnCodPlano->setValue( $inCodPlano );

// Hidden conta corrente
$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
//$obHdnContaCorrente->setValue( $request->get('inContaCorrente'));

$obOk = new Ok;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction     ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget     ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda                    ('UC-02.02.18');
$obFormulario->addForm                     ( $obForm );
$obFormulario->addHidden                   ( $obHdnAcao    );
$obFormulario->addHidden                   ( $obHdnCtrl    );
$obFormulario->addHidden                   ( $obHdnCaminho );
$obFormulario->addHidden                   ( $obHdnBanco );
$obFormulario->addHidden                   ( $obHdnAgencia );
$obFormulario->addHidden                   ( $obHdnCodConta );
$obFormulario->addHidden                   ( $obHdnCodPlano );
$obFormulario->addHidden                   ( $obHdnContaCorrente );
$obFormulario->addTitulo                   ( "Dados para Filtro" );
$obFormulario->addComponente               ( $obEntidadeUsuario );
$obFormulario->addComponente               ( $obIIntervaloPopUpEstruturalPlano );
$obFormulario->agrupaComponentes           ( array( $obTxtCodPlanoInicial, $obLblCodPlano ,$obTxtCodPlanoFinal ) );
$obIMontaRecursoDestinacao->geraFormulario ($obFormulario );
$obFormulario->addComponente               ( $obTxtDesc             );
$obFormulario->addComponenteComposto       ( $obTxtBanco  , $obCmbBanco   );
$obFormulario->addComponenteComposto       ( $obTxtAgencia, $obCmbAgencia );
$obFormulario->addComponente               ( $obCmbContaCorrente );
$obFormulario->addComponente               ( $obCmbOrdem );

$obFormulario->defineBarra          ( array( $obOk ) );
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
