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
  * Página de Filtro dos Arquivos Relacionais
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FLExportarArquivosPessoal.php 60674 2014-11-07 15:51:39Z franver $
  * $Date: 2014-11-07 13:51:39 -0200 (Fri, 07 Nov 2014) $
  * $Author: franver $
  * $Rev: 60674 $
  *
*/
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Mes.class.php');
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
//Define o nome dos arquivos PHP
$stPrograma = "ExportarArquivosPessoal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

Sessao::write('arArquivosDownload', array());

$arArquivos = array(
    array(
        'arquivo' => 'Cargo',
        'nome'    => 'Cargo'
    ),
    array(
        'arquivo' => 'LeiEfetivo',
        'nome'    => 'Lei Efetivo'
    ),
    array(
        'arquivo' => 'QuadroEfetivo',
        'nome'    => 'Quadro Efetivo'
    ),
    array(
        'arquivo' => 'LeiContratado',
        'nome'    => 'Lei Contratado'
    ),
    array(
        'arquivo' => 'QuadroContratado',
        'nome'    => 'Quadro Contratado'
    ),
    array(
        'arquivo' => 'LeiComissionado',
        'nome'    => 'Lei Comissionado'
    ),
    array(
        'arquivo' => 'QuadroComissionado',
        'nome'    => 'Quadro Comissionado'
    ),
    array(
        'arquivo' => 'Processo',
        'nome'    => 'Processo'
    ),
    array(
        'arquivo' => 'Edital',
        'nome'    => 'Edital'
    ),
    array(
        'arquivo' => 'Vaga',
        'nome'    => 'Vaga'
    ),
    array(
        'arquivo' => 'Aprovado',
        'nome'    => 'Aprovado'
    ),
    array(
        'arquivo' => 'Homologacao',
        'nome'    => 'Homologação'
    ),
    array(
        'arquivo' => 'Entidade',
        'nome'    => 'Entidade'
    ),
    array(
        'arquivo' => 'Servidor',
        'nome'    => 'Servidor'
    ),
    array(
        'arquivo' => 'Movimentacao',
        'nome'    => 'Movimentação'
    ),
);


$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarEntidades( $rsEntidades, $stOrdem );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_GPC_TCETO_INSTANCIAS."SICAP/PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ( "hdnPaginaExportacao" );
$obHdnPaginaExportacao->setValue( CAM_GPC_TCETO_INSTANCIAS."SICAP/".$pgProc );

// Define Objeto TextBox para Codigo da Entidade
$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName('inCodEntidade');
$obTxtCodEntidade->setId  ('inCodEntidade');
$obTxtCodEntidade->setRotulo ('Entidade');
$obTxtCodEntidade->setTitle  ('Selecione a entidade.');
$obTxtCodEntidade->setInteiro(true);
$obTxtCodEntidade->setNull   (false);

// Define Objeto Select para Nome da Entidade
$obCmbNomEntidade = new Select;
$obCmbNomEntidade->setName      ('stNomEntidade');
$obCmbNomEntidade->setId        ('stNomEntidade');
$obCmbNomEntidade->setValue     ($inCodEntidade);
$obCmbNomEntidade->addOption    ('', 'Selecione');
$obCmbNomEntidade->setCampoId   ('cod_entidade');
$obCmbNomEntidade->setCampoDesc ('nom_cgm');
$obCmbNomEntidade->setStyle     ('width: 520');
$obCmbNomEntidade->preencheCombo($rsEntidades);
$obCmbNomEntidade->setNull      (false);

$obMes = new Mes;
$obMes->setNull(false);
//$obBimestre->setValue( date('m',time())/2 );

// FIM DA ORDENAÇÃO DA LISTA DE ARQUIVOS

$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);
$rsArquivos->ordena('nome', 'ASC', SORT_STRING);
// Define SELECT multiplo para os arquivos
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName( 'arArquivos' );
$obCmbArquivos->setRotulo( 'Arquivos' );
$obCmbArquivos->setTitle( '' );
$obCmbArquivos->setNull( false );

// lista as entidades disponiveis
$obCmbArquivos->SetNomeLista1( 'arArquivoDisponivel' );
$obCmbArquivos->setCampoId1( 'arquivo' );
$obCmbArquivos->setCampoDesc1( 'nome' );
$obCmbArquivos->SetRecord1( $rsArquivos );
// lista as entidades selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivos' );
$obCmbArquivos->setCampoId2( 'arquivo' );
$obCmbArquivos->setCampoDesc2( 'nome' );
$obCmbArquivos->SetRecord2( new RecordSet );

$obBtnOk = new Ok();
$obBtnOk->setName             ( "btOk" );
$obBtnOk->setValue            ( "Ok" );
$obBtnOk->obEvento->setOnClick("BloqueiaFrames(true,false);Salvar();");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm    );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnPaginaExportacao );
$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->addComponente ( $obMes );
$obFormulario->addComponente( $obCmbArquivos );
$obFormulario->defineBarraAba(array($obBtnOk));
$obFormulario->show();

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');
?>