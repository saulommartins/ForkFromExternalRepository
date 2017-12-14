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
    * Arquivo de filtro de convênios.
    * Data de Criação: 17/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.38

    $Id: FLManterVinculoEmpenhoConvenio.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    //Recupera exercicios
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
    $obTEmpenhoEmpenho->recuperaExercicios( $rsExercicio );
    $stExercicio = Sessao::getExercicio();

    //Paginacao
    Sessao::write('filtro', array());
    Sessao::write('pg', '');
    Sessao::write('pos', '');
    Sessao::write('paginando', false);

    //Instancia o formulario
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //Componentes
    $obCmbExercicio = new Select;
    $obCmbExercicio->setRotulo              ( "Exercício"                    		);
    $obCmbExercicio->setTitle               ( "Selecione o exercício"        		);
    $obCmbExercicio->setName                ( "stExercicio"                  		);
    $obCmbExercicio->setValue               ( $stExercicio                   		);
    $obCmbExercicio->setStyle               ( "width: 100px"                 		);
    $obCmbExercicio->setCampoID             ( "exercicio"                    		);
    $obCmbExercicio->setCampoDesc           ( "exercicio"                    		);
    $obCmbExercicio->addOption              ( "", "Selecione"                		);
    $obCmbExercicio->preencheCombo          ( $rsExercicio                   		);
    $obCmbExercicio->setNull                ( true                          		);

    $obTxtConvenio = new TextBox;
    $obTxtConvenio->setRotulo				( "Convênio"					 		);
    $obTxtConvenio->setTitle				( "Informe o número do convênio"		);
    $obTxtConvenio->setName					( "inConvenio"					 		);
    $obTxtConvenio->setValue				( $inConvenio					 		);
    $obTxtConvenio->setInteiro				( true									);

    $obCgmParticipante =  new IPopUpCGMVinculado($obForm);
    $obCgmParticipante->setTabelaVinculo 	( "licitacao.participante_certificacao" );
    $obCgmParticipante->setCampoVinculo 	( "cgm_fornecedor" 						);
    $obCgmParticipante->setNomeVinculo 		( "Participante" 						);
    $obCgmParticipante->setRotulo			( "Participante"						);
    $obCgmParticipante->setTitle			( "Selecione o CGM do participante"		);
    $obCgmParticipante->setNull 			( true 									);
    $obCgmParticipante->setName   			( "stNomCgmParticipante"				);
    $obCgmParticipante->setId     			( "stNomCgmParticipante"				);
    $obCgmParticipante->obCampoCod->setName ( "inCgmParticipante" 					);
    $obCgmParticipante->obCampoCod->setId   ( "inCgmParticipante" 					);
    $obCgmParticipante->obCampoCod->setNull ( false               					);

    //Monta o formulario
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( "Dados para Filtro"    );
    $obFormulario->addHidden( $obHdnAcao 		     );
    $obFormulario->addComponente( $obCmbExercicio    );
    $obFormulario->addComponente( $obTxtConvenio     );
    $obFormulario->addComponente( $obCgmParticipante );

    $obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
