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
    * Página de Formulario para Encerramento de contas
    * Data de Criação   : 30/09/2014
    * @author Analista: Silvia
    * @author Desenvolvedor: Evandro Melos
    * $Id: FLEncerrarConta.php 61444 2015-01-16 17:32:17Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "EncerrarConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Recupera Mascara
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "encerrar";
}

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodReduzido = new TextBox;
$obTxtCodReduzido->setName     ( "inCodReduzido" );
$obTxtCodReduzido->setValue    ( $inCodReduzido );
$obTxtCodReduzido->setRotulo   ( "Código Reduzido" );
$obTxtCodReduzido->setSize     ( 20 );
$obTxtCodReduzido->setMaxLength( 20 );
$obTxtCodReduzido->setNull     ( true );
$obTxtCodReduzido->setInteiro  ( true );
$obTxtCodReduzido->setTitle    ( 'Código reduzido' );

$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setName             ( "stCodClass" );
$obTxtCodClassificacao->setValue            ( $stCodClassificacao );
$obTxtCodClassificacao->setRotulo           ( "Código de Classificação" );
$obTxtCodClassificacao->setMascara          ( $stMascara );
$obTxtCodClassificacao->setPreencheComZeros ( 'D' );
$obTxtCodClassificacao->setNull             ( true );
$obTxtCodClassificacao->setTitle            ( 'Código de classificação da conta' );
$obTxtCodClassificacao->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[0-9.]');" );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );
$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Descrição da conta contabil' );

$obDataSaldo = new Data;
$obDataSaldo->setName  ('dtSaldo');
$obDataSaldo->setRotulo('Data do Saldo Contábil');
$obDataSaldo->setTItle ('Data limite para a pesquisas do saldo da conta contábil');
$obDataSaldo->setNull  ( true );

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
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
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco']         );
$obTxtBanco->setRotulo   ( "Banco"            );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o banco" );
$obTxtBanco->setDisabled ( $boDisabled         );
$obTxtBanco->setInteiro  ( true                );
$obTxtBanco->obEvento->setOnChange  ( " if(this.value != '') montaParametrosGET('MontaAgencia');
                                        else {
                                            document.getElementById('inCodBanco').value = '';
                                            document.getElementById('inCodAgencia').value = '';
                                            document.getElementById('stContaCorrente').value = '';
                                        }
                                    ");

$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId ('inCodBanco');
$obHdnBanco->setValue ( $_REQUEST['inCodBanco'] );

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

$obHdnAgencia = new Hidden;
$obHdnAgencia->setName ( 'inCodAgencia' );
$obHdnAgencia->setId ( 'inCodAgencia' );
$obHdnAgencia->setValue ( $_REQUEST['inCodAgencia'] );

$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->setValue     ( $_REQUEST['inNumAgencia']  );
$obCmbAgencia->addOption    ( "", "Selecione"  );
$obCmbAgencia->setDisabled  ( $boDisabled      );
$obCmbAgencia->setNull(true);
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
$obHdnContaCorrente->setValue( $_REQUEST['inContaCorrente']);

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
$obCmbContaCorrente->obEvento->setOnChange  ( " montaParametrosGET('BuscaContaCorrente'); ");

// Define Objeto TextBox para Codigo do Recurso
$obTxtRecurso = new TextBox;
$obTxtRecurso->setName    ( "inCodRecurso"                     );
$obTxtRecurso->setId      ( "inCodRecurso"                     );
$obTxtRecurso->setValue   ( $inCodRecurso                      );
$obTxtRecurso->setRotulo  ( "Recurso"                          );
$obTxtRecurso->setTitle   ( "Selecione o recurso orçamentário" );
$obTxtRecurso->setDisabled( $boDesabilitaRecurso               );
$obTxtRecurso->setMascara ( $stMascaraRecurso                  );
$obTxtRecurso->setPreencheComZeros ( 'E'                       );

// Define Objeto Select para o Recurso
$obCmbRecurso = new Select;
$obCmbRecurso->setName      ( "stNomeRecurso"    );
$obCmbRecurso->setId        ( "stNomeRecurso"    );
$obCmbRecurso->setValue     ( $inCodRecurso      );
$obCmbRecurso->addOption    ( "", "Selecione"    );
$obCmbRecurso->setCampoId   ( "[cod_fonte]"    );
$obCmbRecurso->setCampoDesc ( "[nom_recurso]"  );
$obCmbRecurso->preencheCombo( $rsRecurso         );
$obCmbRecurso->setDisabled  ( $boDesabilitaRecurso );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnBanco );
$obFormulario->addHidden( $obHdnAgencia );
$obFormulario->addHidden( $obHdnContaCorrente );

$obFormulario->addTitulo( "Dados para Filtro"        );
$obFormulario->addComponente( $obTxtCodReduzido      );
$obFormulario->addComponente( $obTxtCodClassificacao );
$obFormulario->addComponente( $obTxtDesc             );
$obFormulario->addComponente( $obDataSaldo           );
$obFormulario->addComponenteComposto( $obTxtBanco  , $obCmbBanco   );
$obFormulario->addComponenteComposto( $obTxtAgencia, $obCmbAgencia );
$obFormulario->addComponente( $obCmbContaCorrente );
$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
