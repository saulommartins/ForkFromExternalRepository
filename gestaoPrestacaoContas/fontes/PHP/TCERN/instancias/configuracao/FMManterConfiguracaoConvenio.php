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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once(TCOM."TComprasObjeto.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");
include_once(CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNConvenio.class.php");
include_once(TCGM."TCGM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

if ($_REQUEST['stAcao'] == 'manter') {
    $stAcao = $request->get('stAcao');

    $obTTCERNConvenio = new TTCERNConvenio;
    $obTTCERNConvenio->setDado('num_convenio', $_REQUEST['inNumConvenio']);
    $obTTCERNConvenio->setDado('exercicio'   , $_REQUEST['stExercicio']);
    $obTTCERNConvenio->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCERNConvenio->recuperaPorChave($rsConvenio);

    $inNumConvenio = trim($rsConvenio->getCampo('num_convenio'));
    $inCodEntidade = $rsConvenio->getCampo('cod_entidade');
    $stProcesso    = $rsConvenio->getCampo('cod_processo').'/'.$rsConvenio->getCampo('exercicio_processo');
    $inCodObjeto   = $rsConvenio->getCampo('cod_objeto');
    $stNomRecurso1 = $rsConvenio->getCampo('cod_recurso_1');
    $inCodRecurso1 = $rsConvenio->getCampo('cod_recurso_1');
    $vlFonte1      = str_replace('.', ',', $rsConvenio->getCampo('valor_recurso_1'));
    $stNomRecurso2 = $rsConvenio->getCampo('cod_recurso_2');
    $inCodRecurso2 = $rsConvenio->getCampo('cod_recurso_2');
    $vlFonte2      = str_replace('.', ',', $rsConvenio->getCampo('valor_recurso_2'));
    $stNomRecurso3 = $rsConvenio->getCampo('cod_recurso_3');
    $inCodRecurso3 = $rsConvenio->getCampo('cod_recurso_3');
    $vlFonte3      = str_replace('.', ',', $rsConvenio->getCampo('valor_recurso_3'));
    $dtInicioVigencia  = $rsConvenio->getCampo('dt_inicio_vigencia');
    $dtTerminoVigencia = $rsConvenio->getCampo('dt_termino_vigencia');
    $dtAssinatura      = $rsConvenio->getCampo('dt_assinatura');
    $dtPublicacao      = $rsConvenio->getCampo('dt_publicacao');

    $obTCGM = new TCGM();
    $obTCGM->setDado('numcgm', $rsConvenio->getCampo('numcgm_recebedor'));
    $obTCGM->recuperaPorChave($rsCGM);

    $inNumCGM = $rsCGM->getCampo('numcgm');
    $stNomCGM = $rsCGM->getCampo('nom_cgm');
}

$obTComprasObjeto = new TComprasObjeto();
$obTComprasObjeto->recuperaTodos( $rsObjeto );

$obTOrcamentoRecurso = new TOrcamentoRecurso();
$obTOrcamentoRecurso->recuperaRecursoExercicio( $rsRecursos );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($_REQUEST['stAcao'] == 'manter') {
    $obTxtNumConvenio = new TextBox;
    $obTxtNumConvenio->setReadOnly(true);
} else {
    $obTxtNumConvenio = new TextBox;
    $obTxtNumConvenio->setSize(10);
}
$obTxtNumConvenio->setName  ( "stNumConvenio"      );
$obTxtNumConvenio->setId    ( "stNumConvenio"      );
$obTxtNumConvenio->setRotulo( "Número do Convênio" );
$obTxtNumConvenio->setValue ( $inNumConvenio       );
$obTxtNumConvenio->setNull  ( false                );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setCodEntidade( $inCodEntidade );
if ($_REQUEST['stAcao'] == 'manter') {
    $obEntidadeUsuario->obTextBox->setReadOnly(true);
    $obEntidadeUsuario->obSelect->boDisabled = true;
}
$obEntidadeUsuario->setNull( false );

$obPopUpProcesso = new IPopUpProcesso($obForm);
$obPopUpProcesso->setRotulo("Processo Administrativo");
$obPopUpProcesso->setValue($stProcesso);
$obPopUpProcesso->obCampoCod->setValue($stProcesso);
$obPopUpProcesso->setValidar(true);
$obPopUpProcesso->setNull(false);

$obCGMRecebedor = new IPopUpCGMVinculado( $obForm );
$obCGMRecebedor->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obCGMRecebedor->setCampoVinculo     ( 'numcgm'           );
$obCGMRecebedor->setNomeVinculo      ( 'CGM do recebedor' );
$obCGMRecebedor->setRotulo           ( 'CGM do recebedor' );
$obCGMRecebedor->setName             ( 'stCGM' );
$obCGMRecebedor->setId               ( 'stCGM' );
$obCGMRecebedor->setValue            ( $stNomCGM );
$obCGMRecebedor->obCampoCod->setName ( 'inCGM' );
$obCGMRecebedor->obCampoCod->setId   ( 'inCGM' );
$obCGMRecebedor->obCampoCod->setValue( $inNumCGM );
$obCGMRecebedor->setNull             ( false    );

$obCmbObjeto = new Select();
$obCmbObjeto->setRotulo( 'Objeto' );
$obCmbObjeto->setTitle( 'Selecione o Objeto' );
$obCmbObjeto->setName( 'inObjeto' );
$obCmbObjeto->setId( 'inObjeto' );
$obCmbObjeto->addOption( '', 'Selecione' );
$obCmbObjeto->setCampoId( 'cod_objeto' );
$obCmbObjeto->setCampoDesc( 'descricao' );
$obCmbObjeto->setStyle('width: 520px');
$obCmbObjeto->preencheCombo( $rsObjeto );
$obCmbObjeto->setValue( $inCodObjeto );
$obCmbObjeto->setNull( false );

$obFonteRecurso1 = new TextBoxSelect;
$obFonteRecurso1->setRotulo              ( "Fonte do Recurso 1" );
$obFonteRecurso1->setName                ( "inCodRecurso1"      );
$obFonteRecurso1->setTitle               ( "Informe o recurso." );
$obFonteRecurso1->setMensagem            ( "Recurso inválido"   );
$obFonteRecurso1->obTextBox->setName      ( "inCodRecurso1"     );
$obFonteRecurso1->obTextBox->setId        ( "inCodRecurso1"     );
$obFonteRecurso1->obTextBox->setRotulo    ( "Recurso"             );
$obFonteRecurso1->obTextBox->setTitle     ( "Selecione a Recurso" );
$obFonteRecurso1->obTextBox->setInteiro   ( true                  );
$obFonteRecurso1->obTextBox->setNull      ( false                 );
$obFonteRecurso1->obSelect->setName       ( "stNomRecurso1"       );
$obFonteRecurso1->obSelect->setId         ( "stNomRecurso1"       );
$obFonteRecurso1->obSelect->setCampoId    ( "cod_recurso"         );
$obFonteRecurso1->obSelect->setCampoDesc  ( "nom_recurso"         );
$obFonteRecurso1->obSelect->setStyle      ( "width: 520"          );
$obFonteRecurso1->obSelect->addOption     ( "", "Selecione"       );
$obFonteRecurso1->obSelect->preencheCombo ( $rsRecursos           );
$obFonteRecurso1->obSelect->setNull       ( false                 );
//value
$obFonteRecurso1->obTextBox->setValue( $stNomRecurso1 );
$obFonteRecurso1->obSelect->setValue( $inCodRecurso1 );

$obTxtValorFonte1 = new Moeda;
$obTxtValorFonte1->setName   ( "stValorFonte1"     );
$obTxtValorFonte1->setId     ( "stValorFonte1"     );
$obTxtValorFonte1->setRotulo ( "Valor da Fonte 1"  );
$obTxtValorFonte1->setValue  ( $vlFonte1           );
$obTxtValorFonte1->setSize   ( 14                  );

$obFonteRecurso2 = new TextBoxSelect;
$obFonteRecurso2->setRotulo              ( "Fonte do Recurso 2" );
$obFonteRecurso2->setName                ( "inCodRecurso2"      );
$obFonteRecurso2->setTitle               ( "Informe o recurso." );
$obFonteRecurso2->setMensagem            ( "Recurso inválido"   );
$obFonteRecurso2->obTextBox->setName     ( "inCodRecurso2"     );
$obFonteRecurso2->obTextBox->setId       ( "inCodRecurso2"     );
$obFonteRecurso2->obTextBox->setRotulo   ( "Recurso"             );
$obFonteRecurso2->obTextBox->setTitle    ( "Selecione a Recurso" );
$obFonteRecurso2->obTextBox->setInteiro  ( true                  );
$obFonteRecurso2->obTextBox->setNull     ( false                 );
$obFonteRecurso2->obSelect->setName      ( "stNomRecurso2"       );
$obFonteRecurso2->obSelect->setId        ( "stNomRecurso2"       );
$obFonteRecurso2->obSelect->setCampoId   ( "cod_recurso"         );
$obFonteRecurso2->obSelect->setCampoDesc ( "nom_recurso"         );
$obFonteRecurso2->obSelect->setStyle     ( "width: 520"          );
$obFonteRecurso2->obSelect->addOption    ( "", "Selecione"       );
$obFonteRecurso2->obSelect->preencheCombo( $rsRecursos           );
$obFonteRecurso2->obSelect->setNull      ( false                 );
//value
$obFonteRecurso2->obTextBox->setValue( $stNomRecurso2 );
$obFonteRecurso2->obSelect->setValue( $inCodRecurso2 );

$obTxtValorFonte2 = new Moeda;
$obTxtValorFonte2->setName  ( "stValorFonte2"       );
$obTxtValorFonte2->setId    ( "stValorFonte2"       );
$obTxtValorFonte2->setRotulo( "Valor da Fonte 2"    );
$obTxtValorFonte2->setValue ( $vlFonte2             );
$obTxtValorFonte2->setSize  ( 14                    );

$obFonteRecurso3 = new TextBoxSelect;
$obFonteRecurso3->setRotulo              ( "Fonte do Recurso 3" );
$obFonteRecurso3->setName                ( "inCodRecurso3"      );
$obFonteRecurso3->setTitle               ( "Informe o recurso." );
$obFonteRecurso3->setMensagem            ( "Recurso inválido"   );
$obFonteRecurso3->obTextBox->setName     ( "inCodRecurso3"     );
$obFonteRecurso3->obTextBox->setId       ( "inCodRecurso3"     );
$obFonteRecurso3->obTextBox->setRotulo   ( "Recurso"             );
$obFonteRecurso3->obTextBox->setTitle    ( "Selecione a Recurso" );
$obFonteRecurso3->obTextBox->setInteiro  ( true                  );
$obFonteRecurso3->obTextBox->setNull     ( false                 );
$obFonteRecurso3->obSelect->setName      ( "stNomRecurso3"       );
$obFonteRecurso3->obSelect->setId        ( "stNomRecurso3"       );
$obFonteRecurso3->obSelect->setCampoId   ( "cod_recurso"         );
$obFonteRecurso3->obSelect->setCampoDesc ( "nom_recurso"         );
$obFonteRecurso3->obSelect->setStyle     ( "width: 520"          );
$obFonteRecurso3->obSelect->addOption    ( "", "Selecione"       );
$obFonteRecurso3->obSelect->preencheCombo( $rsRecursos           );
$obFonteRecurso3->obSelect->setNull      ( false                 );
//value
$obFonteRecurso3->obTextBox->setValue( $stNomRecurso3 );
$obFonteRecurso3->obSelect->setValue( $inCodRecurso3 );

$obTxtValorFonte3 = new Moeda;
$obTxtValorFonte3->setName  ( "stValorFonte3"     );
$obTxtValorFonte3->setId    ( "stValorFonte3"     );
$obTxtValorFonte3->setRotulo( "Valor da Fonte 3"  );
$obTxtValorFonte3->setValue ( $vlFonte3           );
$obTxtValorFonte3->setSize  ( 14                  );

$obDtInicioVigencia = new Data;
$obDtInicioVigencia->setName  ( "dtInicioVigencia"   );
$obDtInicioVigencia->setRotulo( "Data de início da vigência"   );
$obDtInicioVigencia->setTitle ( ''          );
$obDtInicioVigencia->setValue ( $dtInicioVigencia );
$obDtInicioVigencia->setNull  ( false       );

$obDtTerminoVigencia = new Data;
$obDtTerminoVigencia->setName  ( "dtTerminoVigencia"   );
$obDtTerminoVigencia->setRotulo( "Data de Término da vigência"   );
$obDtTerminoVigencia->setTitle ( ''          );
$obDtTerminoVigencia->setValue ( $dtTerminoVigencia    );
$obDtTerminoVigencia->setNull  ( false       );

$obDtAssinatura = new Data;
$obDtAssinatura->setName  ( "dtAssinatura"   );
$obDtAssinatura->setRotulo( "Data de Assinatura"   );
$obDtAssinatura->setTitle ( ''          );
$obDtAssinatura->setValue ( $dtAssinatura );
$obDtAssinatura->setNull  ( false       );

$obDtPublicacao = new Data;
$obDtPublicacao->setName  ( "dtPublicacao" );
$obDtPublicacao->setRotulo( "Data de Publicação" );
$obDtPublicacao->setValue ( $dtPublicacao );
$obDtPublicacao->setTitle ( ''          );
$obDtPublicacao->setNull  ( false       );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtNumConvenio );
$obFormulario->addComponente( $obEntidadeUsuario );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obCGMRecebedor );
$obFormulario->addComponente( $obCmbObjeto );
$obFormulario->addComponente( $obFonteRecurso1 );
$obFormulario->addComponente( $obTxtValorFonte1 );
$obFormulario->addComponente( $obFonteRecurso2 );
$obFormulario->addComponente( $obTxtValorFonte2 );
$obFormulario->addComponente( $obFonteRecurso3 );
$obFormulario->addComponente( $obTxtValorFonte3 );
$obFormulario->addComponente( $obDtInicioVigencia );
$obFormulario->addComponente( $obDtTerminoVigencia );
$obFormulario->addComponente( $obDtAssinatura );
$obFormulario->addComponente( $obDtPublicacao );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
