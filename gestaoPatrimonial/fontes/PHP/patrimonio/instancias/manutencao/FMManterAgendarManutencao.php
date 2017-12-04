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
    * Data de Criação: 04/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28251 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 13:43:36 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php");
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

$stPrograma = "ManterAgendarManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioManutencao = new TPatrimonioManutencao();
$obTPatrimonioManutencao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
if ($stAcao == 'alterar') {
    $obTPatrimonioManutencao->setDado( 'dt_agendamento', implode('-',array_reverse(explode('/',$_REQUEST['dtAgendamento']))) );
    $obTPatrimonioManutencao->recuperaBensManutencao( $rsManutencao );
} else {
    $obTPatrimonioManutencao->recuperaDadosBem( $rsManutencao );
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget('oculto');

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//Cria o hidden da pagina acessada
$obHdnPg = new Hidden;
$obHdnPg->setName ("pg");
$obHdnPg->setValue($_REQUEST['pg']);

//Cria o hidden da posicao acessada
$obHdnPos = new Hidden;
$obHdnPos->setName ("pos");
$obHdnPos->setValue($_REQUEST['pos']);

if ($stAcao == 'alterar') {
    $obHdnDtOriginal = new Hidden();
    $obHdnDtOriginal->setName( 'dtAgendamentoOriginal' );
    $obHdnDtOriginal->setValue( $rsManutencao->getCampo( 'dt_agendamento' ) );
}

//instancia label natureza
$obLblNatureza = new Label();
$obLblNatureza->setRotulo( 'Natureza' );
$obLblNatureza->setValue( $rsManutencao->getCampo('cod_natureza').' - '.$rsManutencao->getCampo('nom_natureza') );

//instancia label grupo
$obLblGrupo = new Label();
$obLblGrupo->setRotulo( 'Grupo' );
$obLblGrupo->setValue( $rsManutencao->getCampo('cod_grupo').' - '.$rsManutencao->getCampo('nom_grupo') );

//instancia label especie
$obLblEspecie = new Label();
$obLblEspecie->setRotulo( 'Espécie' );
$obLblEspecie->setValue( $rsManutencao->getCampo('cod_especie').' - '.$rsManutencao->getCampo('nom_especie') );

//instancia label codigo do bem
$obLblCodigoBem = new TextBox();
$obLblCodigoBem->setRotulo( 'Código do Bem' );
$obLblCodigoBem->setName( 'inCodBem' );
$obLblCodigoBem->setLabel( true );
$obLblCodigoBem->setValue( $rsManutencao->getCampo( 'cod_bem' ) );

//instancia label placa
$obLblPlaca = new Label();
$obLblPlaca->setRotulo( 'Número da Placa' );
$obLblPlaca->setValue( $rsManutencao->getCampo( 'num_placa' ) );

//instancia label descricao
$obLblDescricao = new Label();
$obLblDescricao->setRotulo( 'Descrição' );
$obLblDescricao->setValue( $rsManutencao->getCampo( 'descricao' ) );

//instancia data de agendamento
$obDtAgendamento = new Data();
$obDtAgendamento->setRotulo( 'Data do Agendamento' );
$obDtAgendamento->setName( 'dtAgendamento' );
$obDtAgendamento->setTitle( 'Informe a data de agendamento do bem.' );
$obDtAgendamento->setNull( false );
$obDtAgendamento->setValue( $rsManutencao->getCampo( 'dt_agendamento' ) );

//instancia o ipopupcgm
$obIPopUpCGM = new IPopUpCGM( $obForm );
$obIPopUpCGM->setRotulo           ( 'CGM'            );
$obIPopUpCGM->setTitle            ( 'Informe o CGM.' );
$obIPopUpCGM->setName             ( 'stNomCGM'       );
$obIPopUpCGM->setId               ( 'stNomCGM'       );
$obIPopUpCGM->obCampoCod->setName ( 'inNumCGM'       );
$obIPopUpCGM->obCampoCod->setId   ( 'inNumCGM'       );
$obIPopUpCGM->setNull             ( false            );
$obIPopUpCGM->obCampoCod->setValue( $rsManutencao->getCampo( 'numcgm' ) );
$obIPopUpCGM->setValue            ( $rsManutencao->getCampo( 'nom_cgm' ) );

//instancia observacao
$obTxtObservacao = new TextArea();
$obTxtObservacao->setRotulo( 'Observação' );
$obTxtObservacao->setTitle( 'Informa a observação da manutenção.' );
$obTxtObservacao->setName( 'stObservacao' );
$obTxtObservacao->setId( 'stObservacao' );
$obTxtObservacao->setMaxCaracteres(200);
$obTxtObservacao->setValue( $rsManutencao->getCampo( 'observacao' ) );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.07');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addHidden    ( $obHdnPg );
$obFormulario->addHidden    ( $obHdnPos );

if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnDtOriginal );
}

$obFormulario->addTitulo    ( 'Dados do Agendamento' );
$obFormulario->addComponente( $obLblNatureza );
$obFormulario->addComponente( $obLblGrupo );
$obFormulario->addComponente( $obLblEspecie );
$obFormulario->addComponente( $obLblCodigoBem );
$obFormulario->addComponente( $obLblPlaca );
$obFormulario->addComponente( $obLblDescricao );
$obFormulario->addComponente( $obDtAgendamento );
$obFormulario->addComponente( $obIPopUpCGM );
$obFormulario->addComponente( $obTxtObservacao );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&pos=".Sessao::read('pos')."&pg=".Sessao::read('pg'));
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
