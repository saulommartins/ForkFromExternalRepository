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
    * Filtro dos Contratos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: FLManterVinculoEmpenhoContrato.php 64087 2015-12-01 16:10:15Z jean $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"  										  );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"			    							  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao 	 = 'selecionar';
$stExercicio = Sessao::getExercicio();

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
    //Paginacao
    Sessao::write('filtro', array());
    Sessao::write('pg', '');
    Sessao::write('pos', '');
    Sessao::write('paginando', false);

    //Busca exercicios
    $obRegra = new REmpenhoEmpenho;
    $obRegra->recuperaExercicios( $rsExercicio, $boTransacao, Sessao::getExercicio());

    //Busca entidades cadastradas no sistema
    $obREntidade = new ROrcamentoEntidade;
    $obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

    //Cria novo RecordSet
    $rsRecordset = new RecordSet;

    // Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
    if ($rsEntidades->getNumLinhas()==1) {
        $rsRecordset = $rsEntidades;
        $rsEntidades = new RecordSet;
    }

    //Instancia o formulario
    $obForm = new Form;
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal" );

    //Componentes
    $obCmbExercicio = new Select;
    $obCmbExercicio->setRotulo              ( "Exercício"                   );
    $obCmbExercicio->setTitle               ( "Selecione o exercício"       );
    $obCmbExercicio->setName                ( "inExercicio"                 );
    $obCmbExercicio->setValue               ( $stExercicio                  );
    $obCmbExercicio->setStyle               ( "width: 100px"                );
    $obCmbExercicio->setCampoID             ( "exercicio"                   );
    $obCmbExercicio->setCampoDesc           ( "exercicio"                   );
    $obCmbExercicio->addOption              ( "", "Selecione"               );
    $obCmbExercicio->preencheCombo          ( $rsExercicio                  );
    $obCmbExercicio->setNull                ( false                         );

    $obCmbEntidades = new SelectMultiplo();
    $obCmbEntidades->setName   ('inCodEntidade'            );
    $obCmbEntidades->setRotulo ( "Entidades"               );
    $obCmbEntidades->setTitle  ( "Selecione as entidades." );
    $obCmbEntidades->setNull   ( false                     );

    $obCmbEntidades->SetNomeLista1 ( 'inCodEntidadeDisponivel');
    $obCmbEntidades->setCampoId1   ( 'cod_entidade'           );
    $obCmbEntidades->setCampoDesc1 ( 'nom_cgm'                );
    $obCmbEntidades->SetRecord1    ( $rsEntidades             );

    $obCmbEntidades->SetNomeLista2 ( 'inCodEntidade' );
    $obCmbEntidades->setCampoId2   ( 'cod_entidade'  );
    $obCmbEntidades->setCampoDesc2 ( 'nom_cgm'       );
    $obCmbEntidades->SetRecord2    ( $rsRecordset    );

    $obTxtContrato = new TextBox;
    $obTxtContrato->setName   ( "inNumeroContrato"    );
    $obTxtContrato->setId     ( "inNumeroContrato"    );
    $obTxtContrato->setValue  ( $inNumeroContrato     );
    $obTxtContrato->setRotulo ( "Contrato"            );
    $obTxtContrato->setTitle  ( "Informe o contrato." );
    $obTxtContrato->setInteiro( true );

    $obCmbTipoBusca = new Select;
    $obCmbTipoBusca->setName    ( "stTipoBusca"             );
    $obCmbTipoBusca->setId      ( "stTipoBusca"             );
    $obCmbTipoBusca->setRotulo  ( "Tipo de Busca"           );
    $obCmbTipoBusca->addOPtion  ( "inicio", "Início", true  );
    $obCmbTipoBusca->addOPtion  ( "final", "Final", false   );
    $obCmbTipoBusca->addOPtion  ( "contem", "Contém", false );
    $obCmbTipoBusca->addOPtion  ( "exata", "Exata", false   );


    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //Monta o formulario
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( "Dados para Filtro"                            );
    $obFormulario->addHidden( $obHdnAcao 		                             );
    $obFormulario->addComponente( $obCmbExercicio                            );
    $obFormulario->addComponente( $obCmbEntidades                            );
    $obFormulario->agrupaComponentes( array($obTxtContrato, $obCmbTipoBusca) );

    $obFormulario->OK();
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
