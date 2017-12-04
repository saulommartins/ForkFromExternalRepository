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
    * Página de Filtro para exportação do TCEAL
    * Data de Criação   : 03/10/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

$stPrograma = "ExportacaoRelacionais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

Sessao::write('arArquivosDownload', array());

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarEntidades( $rsEntidades, $stOrdem );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( 'stCtrl' );
$obHdnCtrl->setValue( '' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

$obCmbBimestre = new Select;
$obCmbBimestre->setRotulo( "Bimestre" );
$obCmbBimestre->setName( "bimestre" );
$obCmbBimestre->addOption( "", "Selecione" );
$obCmbBimestre->addOption( "0", "Orçamento" );
$obCmbBimestre->addOption( "1", "1º Bimestre" );
$obCmbBimestre->addOption( "2", "2º Bimestre" );
$obCmbBimestre->addOption( "3", "3º Bimestre" );
$obCmbBimestre->addOption( "4", "4º Bimestre" );
$obCmbBimestre->addOption( "5", "5º Bimestre" );
$obCmbBimestre->addOption( "6", "6º Bimestre" );
$obCmbBimestre->addOption( "7", "Prestação de Contas" );
$obCmbBimestre->setNull( false );
$obCmbBimestre->setStyle( "width: 220px" );

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

$arArquivos = array(
    array(
        'arquivo' => 'rubricasFolhaServidores',
        'nome' => 'Rubricas da Folha de Servidores'
    ),
    array(
        'arquivo' => 'cargosServidores',
        'nome' => 'Cargos dos Servidores'
    ),
    array(
        'arquivo' => 'funcoesServidores',
        'nome' => 'Funções dos Servidores'
    ),
    array(
        'arquivo' => 'infoRemessa',
        'nome' => 'Informações da Remessa'
    ),
      array(
        'arquivo' => 'lotacoesServidores',
        'nome' => 'Lotações dos Servidores'
    ),
      array(
        'arquivo' => 'credor',
        'nome' => 'Credor'
    ),
      array(
        'arquivo' => 'ProjetoAtividade',
        'nome' => 'Projeto/Atividade'
    ),
      array(
        'arquivo' => 'Programa',
        'nome' => 'Programa'
    ),
      array(
        'arquivo' => 'UndOrcamentaria',
        'nome' => 'Unidade Orcamentária'
    ),
      array(
        'arquivo' => 'funcao',
        'nome' => 'Função'
    ),
      array(
        'arquivo' => 'subFuncao',
        'nome'    => 'Subfunção'
    ),
      array(
        'arquivo' => 'recursoVinculado',
        'nome'    => 'Recurso Vinculado'
    ),
      array(
        'arquivo' => 'rubricasRecDesp',
        'nome'    => 'Rubricas da Receita e da Despesa'
    ),
      array(
        'arquivo' => 'orgao',
        'nome'    => 'Orgão'
    ),
      array(
        'arquivo' => 'atribContContabeis',
        'nome'    => 'Atributo das Contas Contábeis'
    ),
    array(
        'arquivo' => 'relEventosContabeis',
        'nome' => 'Relação dos Eventos Contábeis'
    )
);

$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);

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

// Define o objeto do Formulario
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( 'Dados para Filtro' );
$obFormulario->addComponente( $obCmbBimestre );
$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->addComponente( $obCmbArquivos );

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick("BloqueiaFrames(true,false);Salvar();");

$obFormulario->defineBarra(array($obBtnOK));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
