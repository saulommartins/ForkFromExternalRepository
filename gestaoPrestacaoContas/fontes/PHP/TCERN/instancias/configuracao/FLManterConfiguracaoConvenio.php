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
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

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

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obREntidade->listarUsuariosEntidade($rsEntidades , ' ORDER BY cod_entidade');
$rsRecordset = new RecordSet;

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

///Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ('inCodEntidade');
$obCmbEntidades->setRotulo('Entidades');
$obCmbEntidades->setTitle ('');

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1  ('cod_entidade');
$obCmbEntidades->setCampoDesc1('nom_cgm');
$obCmbEntidades->SetRecord1   ($rsEntidades);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2('inCodEntidade');
$obCmbEntidades->setCampoId2  ('cod_entidade');
$obCmbEntidades->setCampoDesc2('nom_cgm');
$obCmbEntidades->SetRecord2   ($rsRecordset);

//Define o objeto TEXT para Codigo do Convenio Inicial
$obTxtNumConvenioInicial = new TextBox;
$obTxtNumConvenioInicial->setName     ( "inCodConvenioInicial" );
$obTxtNumConvenioInicial->setValue    ( $inCodConvenioInicial  );
$obTxtNumConvenioInicial->setRotulo   ( "Número do Convênio"   );
$obTxtNumConvenioInicial->setTitle    ( "Informe o número do convênio." );
$obTxtNumConvenioInicial->setInteiro  ( true                  );
$obTxtNumConvenioInicial->setNull     ( true                  );

//Define objeto Label
$obLblConvenio = new Label;
$obLblConvenio->setValue( "a" );

//Define o objeto TEXT para Codigo do Convenio Final
$obTxtNumConvenioFinal = new TextBox;
$obTxtNumConvenioFinal->setName    ( "inCodConvenioFinal" );
$obTxtNumConvenioFinal->setValue   ( $inCodConvenioFinal  );
$obTxtNumConvenioFinal->setRotulo  ( "Número do Convênio" );
$obTxtNumConvenioFinal->setTitle   ( "Informe o número do convênio." );
$obTxtNumConvenioFinal->setInteiro ( true  );
$obTxtNumConvenioFinal->setNull    ( true  );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Filtro"  );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente( $obCmbEntidades  );
$obFormulario->agrupaComponentes( array($obTxtNumConvenioInicial, $obLblConvenio, $obTxtNumConvenioFinal) );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
