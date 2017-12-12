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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
include_once(CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php');
include_once(CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php');
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNObra.class.php');

//Define o nome dos arquivos PHP
$stPrograma = 'ManterConfiguracaoObra';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

if ($_REQUEST['stAcao'] == 'manter') {
    $obTTCERNObra = new TTCERNObra;
    $obTTCERNObra->setDado('num_obra', $_REQUEST['inNumObra']);
    $obTTCERNObra->setDado('exercicio'   , $_REQUEST['stExercicio']);
    $obTTCERNObra->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCERNObra->recuperaPorChave($rsObra);

    $inNumObra          = $rsObra->getCampo('num_obra');
    $inCodEntidade      = $rsObra->getCampo('cod_entidade');
    $stObra             = $rsObra->getCampo('obra');
    $stObjetivo         = $rsObra->getCampo('objetivo');
    $stLocalizacao      = $rsObra->getCampo('localizacao');
    $inCodCidade        = $rsObra->getCampo('cod_cidade');
    $inCodRecurso1      = $rsObra->getCampo('cod_recurso_1');
    $stNomRecurso1      = $rsObra->getCampo('cod_recurso_1');
    $vlFonte1           = number_format($rsObra->getCampo('valor_recurso_1'), '2', ',', '.');
    $inCodRecurso2      = $rsObra->getCampo('cod_recurso_2');
    $stNomRecurso2      = $rsObra->getCampo('cod_recurso_2');
    $vlFonte2           = number_format($rsObra->getCampo('valor_recurso_2'), '2', ',', '.');
    $inCodRecurso3      = $rsObra->getCampo('cod_recurso_3');
    $stNomRecurso3      = $rsObra->getCampo('cod_recurso_3');
    $vlFonte3           = number_format($rsObra->getCampo('valor_recurso_3'), '2', ',', '.');
    $vlOrcamentoBase    = number_format($rsObra->getCampo('valor_orcamento_base'), '2', ',', '.');
    $stProjetoExistente = $rsObra->getCampo('projeto_existente');
    $stObservacao       = $rsObra->getCampo('observacao');
    $vlLatitude         = number_format($rsObra->getCampo('latitude'), '2', ',', '.');
    $vlLongitude        = number_format($rsObra->getCampo('longitude'), '2', ',', '.');
    $inRdc              = $rsObra->getCampo('rdc');
}

$obTOrcamentoRecurso = new TOrcamentoRecurso();
$obTOrcamentoRecurso->recuperaRecursoExercicio( $rsRecursos );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( 'oculto' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( ''       );

if ($_REQUEST['stAcao'] == 'manter') {
    $obTxtNumObra = new TextBox;
    $obTxtNumObra->setReadOnly(true);
} else {
    $obTxtNumObra = new TextBox;
    $obTxtNumObra->setSize(6);
}
$obTxtNumObra->setName  ( 'stNumObra'      );
$obTxtNumObra->setId    ( 'stNumObra'      );
$obTxtNumObra->setRotulo( 'Número da Obra' );
$obTxtNumObra->setValue ( $inNumObra       );
$obTxtNumObra->setNull  ( false            );
$obTxtNumObra->setMaxLength( 6             );
$obTxtNumObra->setStyle ( 'width: 120px'   );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setCodEntidade( $inCodEntidade );
if ($_REQUEST['stAcao'] == 'manter') {
    $obEntidadeUsuario->obTextBox->setReadOnly(true);
    $obEntidadeUsuario->obSelect->boDisabled = true;
}
$obEntidadeUsuario->setNull( false );

$obTxtObra = new TextBox;
$obTxtObra->setName  ( 'stObra' );
$obTxtObra->setRotulo( 'Obra'   );
$obTxtObra->setTitle ( ''       );
$obTxtObra->setValue ( $stObra  );
$obTxtObra->setNull  ( false    );
$obTxtObra->setMaxLength  ( 150      );
$obTxtObra->setStyle ( 'width: 350px' );

$obTxtObjetivo = new TextBox;
$obTxtObjetivo->setName  ( 'stObjetivo'   );
$obTxtObjetivo->setRotulo( 'Objetivo'     );
$obTxtObjetivo->setTitle ( ''             );
$obTxtObjetivo->setValue ( $stObjetivo    );
$obTxtObjetivo->setNull  ( false          );
$obTxtObjetivo->setMaxLength ( 50         );
$obTxtObjetivo->setStyle ( 'width: 350px' );

$obTxtLocalizacao = new TextBox;
$obTxtLocalizacao->setName  ( 'stLocalizacao' );
$obTxtLocalizacao->setRotulo( 'Localização'   );
$obTxtLocalizacao->setTitle ( ''              );
$obTxtLocalizacao->setValue ( $stLocalizacao  );
$obTxtLocalizacao->setNull  ( false           );
$obTxtLocalizacao->setMaxLength  ( 50         );
$obTxtLocalizacao->setStyle ( 'width: 350px'  );

$obTxtCodCidade = new Inteiro;
$obTxtCodCidade->setName  ( 'inCodCidade'      );
$obTxtCodCidade->setRotulo( 'Código da Cidade' );
$obTxtCodCidade->setTitle ( ''                 );
$obTxtCodCidade->setValue ( $inCodCidade       );
$obTxtCodCidade->setNull  ( false              );
$obTxtCodCidade->setMaxLength  ( 6             );
$obTxtCodCidade->setStyle ( 'width: 120px'     );

$obFonteRecurso1 = new TextBoxSelect;
$obFonteRecurso1->setRotulo              ( 'Fonte do Recurso 1' );
$obFonteRecurso1->setName                ( 'inCodRecurso1'      );
$obFonteRecurso1->setTitle               ( 'Informe o recurso.' );
$obFonteRecurso1->setMensagem            ( 'Recurso inválido'   );
$obFonteRecurso1->obTextBox->setName     ( 'inCodRecurso1'     );
$obFonteRecurso1->obTextBox->setId       ( 'inCodRecurso1'     );
$obFonteRecurso1->obTextBox->setRotulo   ( 'Recurso'             );
$obFonteRecurso1->obTextBox->setTitle    ( 'Selecione a Recurso' );
$obFonteRecurso1->obTextBox->setInteiro  ( true                  );
$obFonteRecurso1->obTextBox->setNull     ( false                 );
$obFonteRecurso1->obSelect->setName      ( 'stNomRecurso1'       );
$obFonteRecurso1->obSelect->setId        ( 'stNomRecurso1'       );
$obFonteRecurso1->obSelect->setCampoId   ( 'cod_recurso'         );
$obFonteRecurso1->obSelect->setCampoDesc ( 'nom_recurso'         );
$obFonteRecurso1->obSelect->setStyle     ( 'width: 520'          );
$obFonteRecurso1->obSelect->addOption    ( '', 'Selecione'       );
$obFonteRecurso1->obSelect->preencheCombo( $rsRecursos           );
$obFonteRecurso1->obSelect->setNull      ( false                 );
//value
$obFonteRecurso1->obTextBox->setValue( $stNomRecurso1 );
$obFonteRecurso1->obSelect->setValue( $inCodRecurso1 );

$obTxtValorFonte1 = new Moeda;
$obTxtValorFonte1->setName  ( 'vlFonte1'     );
$obTxtValorFonte1->setId    ( 'vlFonte1'     );
$obTxtValorFonte1->setRotulo( 'Valor da Fonte 1'  );
$obTxtValorFonte1->setValue ( $vlFonte1           );
$obTxtValorFonte1->setSize  ( 14                  );

$obFonteRecurso2 = new TextBoxSelect;
$obFonteRecurso2->setRotulo              ( 'Fonte do Recurso 2' );
$obFonteRecurso2->setName                ( 'inCodRecurso2'      );
$obFonteRecurso2->setTitle               ( 'Informe o recurso.' );
$obFonteRecurso2->setMensagem            ( 'Recurso inválido'   );
$obFonteRecurso2->obTextBox->setName     ( 'inCodRecurso2'     );
$obFonteRecurso2->obTextBox->setId       ( 'inCodRecurso2'     );
$obFonteRecurso2->obTextBox->setRotulo   ( 'Recurso'             );
$obFonteRecurso2->obTextBox->setTitle    ( 'Selecione a Recurso' );
$obFonteRecurso2->obTextBox->setInteiro  ( true                  );
$obFonteRecurso2->obTextBox->setNull     ( false                 );
$obFonteRecurso2->obSelect->setName      ( 'stNomRecurso2'       );
$obFonteRecurso2->obSelect->setId        ( 'stNomRecurso2'       );
$obFonteRecurso2->obSelect->setCampoId   ( 'cod_recurso'         );
$obFonteRecurso2->obSelect->setCampoDesc ( 'nom_recurso'         );
$obFonteRecurso2->obSelect->setStyle     ( 'width: 520'          );
$obFonteRecurso2->obSelect->addOption    ( '', 'Selecione'       );
$obFonteRecurso2->obSelect->preencheCombo( $rsRecursos           );
$obFonteRecurso2->obSelect->setNull      ( false                 );
//value
$obFonteRecurso2->obTextBox->setValue( $stNomRecurso2 );
$obFonteRecurso2->obSelect->setValue( $inCodRecurso2 );

$obTxtValorFonte2 = new Moeda;
$obTxtValorFonte2->setName  ( 'vlFonte2'         );
$obTxtValorFonte2->setId    ( 'vlFonte2'         );
$obTxtValorFonte2->setRotulo( 'Valor da Fonte 2' );
$obTxtValorFonte2->setValue ( $vlFonte2          );
$obTxtValorFonte2->setSize  ( 14                 );

$obFonteRecurso3 = new TextBoxSelect;
$obFonteRecurso3->setRotulo              ( 'Fonte do Recurso 3'  );
$obFonteRecurso3->setName                ( 'inCodRecurso3'       );
$obFonteRecurso3->setTitle               ( 'Informe o recurso.'  );
$obFonteRecurso3->setMensagem            ( 'Recurso inválido'    );
$obFonteRecurso3->obTextBox->setName     ( 'inCodRecurso3'       );
$obFonteRecurso3->obTextBox->setId       ( 'inCodRecurso3'       );
$obFonteRecurso3->obTextBox->setRotulo   ( 'Recurso'             );
$obFonteRecurso3->obTextBox->setTitle    ( 'Selecione a Recurso' );
$obFonteRecurso3->obTextBox->setInteiro  ( true                  );
$obFonteRecurso3->obTextBox->setNull     ( false                 );
$obFonteRecurso3->obSelect->setName      ( 'stNomRecurso3'       );
$obFonteRecurso3->obSelect->setId        ( 'stNomRecurso3'       );
$obFonteRecurso3->obSelect->setCampoId   ( 'cod_recurso'         );
$obFonteRecurso3->obSelect->setCampoDesc ( 'nom_recurso'         );
$obFonteRecurso3->obSelect->setStyle     ( 'width: 520'          );
$obFonteRecurso3->obSelect->addOption    ( '', 'Selecione'       );
$obFonteRecurso3->obSelect->preencheCombo( $rsRecursos           );
$obFonteRecurso3->obSelect->setNull      ( false                 );
//value
$obFonteRecurso3->obTextBox->setValue( $stNomRecurso3 );
$obFonteRecurso3->obSelect->setValue( $inCodRecurso3 );

$obTxtValorFonte3 = new Moeda;
$obTxtValorFonte3->setName  ( 'vlFonte3'         );
$obTxtValorFonte3->setId    ( 'vlFonte3'         );
$obTxtValorFonte3->setRotulo( 'Valor da Fonte 3' );
$obTxtValorFonte3->setValue ( $vlFonte3          );
$obTxtValorFonte3->setSize  ( 14                 );

$obTxtOrcamentoBase = new Moeda;
$obTxtOrcamentoBase->setName  ( 'vlOrcamentoBase' );
$obTxtOrcamentoBase->setId    ( 'vlOrcamentoBase' );
$obTxtOrcamentoBase->setRotulo( 'Orçamento Base'  );
$obTxtOrcamentoBase->setValue ( $vlOrcamentoBase  );
$obTxtOrcamentoBase->setSize  ( 14                );
$obTxtOrcamentoBase->setNull  ( false             );

$obTxtProjetoExistente = new TextArea;
$obTxtProjetoExistente->setName  ( 'stProjetoExistente'  );
$obTxtProjetoExistente->setId    ( 'stProjetoExistente'  );
$obTxtProjetoExistente->setRotulo( 'Projetos existentes' );
$obTxtProjetoExistente->setValue ( $stProjetoExistente   );
$obTxtProjetoExistente->setMaxCaracteres ( 255           );
$obTxtProjetoExistente->setStyle ( 'width: 350px'        );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setName  ( 'stObservacao' );
$obTxtObservacao->setId    ( 'stObservacao' );
$obTxtObservacao->setRotulo( 'Observações'  );
$obTxtObservacao->setValue ( $stObservacao  );
$obTxtObservacao->setMaxLength ( 100        );
$obTxtObservacao->setStyle ( 'width: 350px' );

$obTxtLatitude = new Moeda;
$obTxtLatitude->setName  ( 'vlLatitude'   );
$obTxtLatitude->setRotulo( 'Latitude'     );
$obTxtLatitude->setTitle ( ''             );
$obTxtLatitude->setValue ( $vlLatitude    );
$obTxtLatitude->setNull  ( false          );
$obTxtLatitude->setSize  ( 6              );
$obTxtLatitude->setStyle ( 'width: 120px' );

$obTxtLongitude = new Moeda;
$obTxtLongitude->setName  ( 'vlLongitude' );
$obTxtLongitude->setRotulo( 'Longitude'   );
$obTxtLongitude->setTitle ( ''            );
$obTxtLongitude->setValue ( $vlLongitude  );
$obTxtLongitude->setNull  ( false         );
$obTxtLongitude->setSize  ( 6             );
$obTxtLongitude->setStyle ( 'width: 120px' );

$obTxtRdc = new Inteiro;
$obTxtRdc->setName  ( 'inRdc' );
$obTxtRdc->setRotulo( 'RDC'   );
$obTxtRdc->setTitle ( ''      );
$obTxtRdc->setValue ( $inRdc  );
$obTxtRdc->setNull  ( false   );
$obTxtRdc->setSize  ( 1       );
$obTxtRdc->setStyle ( 'width: 120px' );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados' );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obTxtNumObra );
$obFormulario->addComponente( $obEntidadeUsuario );
$obFormulario->addComponente( $obTxtObra );
$obFormulario->addComponente( $obTxtObjetivo );
$obFormulario->addComponente( $obTxtLocalizacao );
$obFormulario->addComponente( $obTxtCodCidade );
$obFormulario->addComponente( $obFonteRecurso1 );
$obFormulario->addComponente( $obTxtValorFonte1 );
$obFormulario->addComponente( $obFonteRecurso2 );
$obFormulario->addComponente( $obTxtValorFonte2 );
$obFormulario->addComponente( $obFonteRecurso3 );
$obFormulario->addComponente( $obTxtValorFonte3 );
$obFormulario->addComponente( $obTxtOrcamentoBase );
$obFormulario->addComponente( $obTxtProjetoExistente );
$obFormulario->addComponente( $obTxtObservacao );
$obFormulario->addComponente( $obTxtLatitude );
$obFormulario->addComponente( $obTxtLongitude );
$obFormulario->addComponente( $obTxtRdc );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
