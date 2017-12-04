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
  * $Id: FLExportarArquivosRelacionais.php 60692 2014-11-10 13:41:36Z evandro $
  * $Date: 2014-11-10 11:41:36 -0200 (Mon, 10 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60692 $
  *
*/
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php');
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
//Define o nome dos arquivos PHP
$stPrograma = "ExportarArquivosRelacionais";
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
        'arquivo' => 'Orgao',
        'nome'    => 'Orgão'
    ),
    array(
        'arquivo' => 'UnidadeOrcamentaria',
        'nome'    => 'Unidade Orçamentária'
    ),
    array(
        'arquivo' => 'Funcao',
        'nome'    => 'Função'
    ),
    array(
        'arquivo' => 'SubFuncao',
        'nome'    => 'SubFunção'
    ),
    array(
        'arquivo' => 'Programa',
        'nome'    => 'Programa'
    ),
    array(
        'arquivo' => 'ProjetoAtividade',
        'nome'    => 'Projeto Atividade'
    ),
    array(
        'arquivo' => 'RecursoVinculado',
        'nome'    => 'Recurso Vinculado'
    ),
    array(
        'arquivo' => 'RubricaDespesa',
        'nome'    => 'Rubrica Despesa'
    ),
    array(
        'arquivo' => 'ContaReceitaOrcamentaria',
        'nome'    => 'Conta Receita Orçamentária'
    ),
    array(
        'arquivo' => 'Credor',
        'nome'    => 'Credor'
    ),
    array(
        'arquivo' => 'InfoRemessa',
        'nome'    => 'Info Remessa'
    ),
);


$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarEntidades( $rsEntidades, $stOrdem );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

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

$obBimestre = new Bimestre;
$obBimestre->setValue( date('m',time())/2 );

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
$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->addComponente ( $obBimestre );
$obFormulario->addComponente( $obCmbArquivos );
$obFormulario->defineBarraAba(array($obBtnOk));
$obFormulario->show();

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');
?>