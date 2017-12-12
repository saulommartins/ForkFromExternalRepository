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
    * Página de Filtro - Exportação Arquivos GF

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: FLExportacaoBalancete.php 65481 2016-05-25 13:12:58Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBalancete";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../../../TCMGO/instancias/exportacao/".$pgOcul );

$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"         );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais" );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"          );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"  );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setChecked(true                    );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport" );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"  );
$obRdbTipoExportArqCompactado->setValue ( "compactados"  );

if ( Sessao::getExercicio() < '2011' ) {
    $arNomeArquivos = array(
                            array('Nome'=>'ALQ'   , 'Arquivo'=>'ALQ.txt'  ),
                            array('Nome'=>'ANL'   , 'Arquivo'=>'ANL.txt'  ),
                            array('Nome'=>'AOC'   , 'Arquivo'=>'AOC.txt'  ),
                            array('Nome'=>'AOP'   , 'Arquivo'=>'AOP.txt'  ),
                            array('Nome'=>'ARE'   , 'Arquivo'=>'ARE.txt'  ),
                            array('Nome'=>'COB'   , 'Arquivo'=>'COB.txt'  ),
                            array('Nome'=>'CON'   , 'Arquivo'=>'CON.txt'  ),
                            array('Nome'=>'CTB'   , 'Arquivo'=>'CTB.txt'  ),
                            array('Nome'=>'CVC'   , 'Arquivo'=>'CVC.txt'  ),
                            array('Nome'=>'IDE'   , 'Arquivo'=>'Ide.txt'  ),
                            array('Nome'=>'DSP'   , 'Arquivo'=>'DSP.txt'  ),
                            array('Nome'=>'ECL'   , 'Arquivo'=>'ECL.txt'  ),
                            array('Nome'=>'EMP'   , 'Arquivo'=>'EMP.txt'  ),
                            array('Nome'=>'EOC'   , 'Arquivo'=>'EOC.txt'  ),
                            array('Nome'=>'EXT'   , 'Arquivo'=>'EXT.txt'  ),
                            array('Nome'=>'LQD'   , 'Arquivo'=>'LQD.txt'  ),
                            array('Nome'=>'NTF'   , 'Arquivo'=>'NTF.txt'  ),
                            array('Nome'=>'OPS'   , 'Arquivo'=>'OPS.txt'  ),
                            array('Nome'=>'ORGAO' , 'Arquivo'=>'Orgao.txt'),
                            array('Nome'=>'REC'   , 'Arquivo'=>'REC.txt'  ),
                            array('Nome'=>'RSP'   , 'Arquivo'=>'RSP.txt'  )
                           );
} elseif (Sessao::getExercicio() < '2012') {
    $arNomeArquivos = array(
                            array('Nome'=>'ALQ'   ,'Arquivo'=>'ALQ.txt'   ),
                            array('Nome'=>'ANL'   ,'Arquivo'=>'ANL.txt'   ),
                            array('Nome'=>'AOC'   ,'Arquivo'=>'AOC.txt'   ),
                            array('Nome'=>'AOP'   ,'Arquivo'=>'AOP.txt'   ),
                            array('Nome'=>'ARE'   ,'Arquivo'=>'ARE.txt'   ),
                            array('Nome'=>'COB'   ,'Arquivo'=>'COB.txt'   ),
                            array('Nome'=>'CON'   ,'Arquivo'=>'CON.txt'   ),
                            array('Nome'=>'CTB'   ,'Arquivo'=>'CTB.txt'   ),
                            array('Nome'=>'CVC'   ,'Arquivo'=>'CVC.txt'   ),
                            array('Nome'=>'IDE'   ,'Arquivo'=>'Ide.txt'   ),
                            array('Nome'=>'ECL'   ,'Arquivo'=>'ECL.txt'   ),
                            array('Nome'=>'EMP'   ,'Arquivo'=>'EMP.txt'   ),
                            array('Nome'=>'EOC'   ,'Arquivo'=>'EOC.txt'   ),
                            array('Nome'=>'EXT'   ,'Arquivo'=>'EXT.txt'   ),
                            array('Nome'=>'LQD'   ,'Arquivo'=>'LQD.txt'   ),
                            array('Nome'=>'NTF'   ,'Arquivo'=>'NTF.txt'   ),
                            array('Nome'=>'OPS'   ,'Arquivo'=>'OPS.txt'   ),
                            array('Nome'=>'ORGAO' ,'Arquivo'=>'Orgao.txt' ),
                            array('Nome'=>'REC'   ,'Arquivo'=>'REC.txt'   ),
                            array('Nome'=>'RSP'   ,'Arquivo'=>'RSP.txt'   ),
                            array('Nome'=>'TRB'   ,'Arquivo'=>'TRB.txt'   )
                           );
} else {
    $arNomeArquivos = array(
                            array('Nome'=>'AEX'   , 'Arquivo'=>'AEX.txt'   ),
                            array('Nome'=>'ALQ'   , 'Arquivo'=>'ALQ.txt'   ),
                            array('Nome'=>'ANL'   , 'Arquivo'=>'ANL.txt'   ),
                            array('Nome'=>'AOC'   , 'Arquivo'=>'AOC.txt'   ),
                            array('Nome'=>'AOP'   , 'Arquivo'=>'AOP.txt'   ),
                            array('Nome'=>'ARE'   , 'Arquivo'=>'ARE.txt'   ),
                            array('Nome'=>'COB'   , 'Arquivo'=>'COB.txt'   ),
                            array('Nome'=>'CON'   , 'Arquivo'=>'CON.txt'   ),
                            array('Nome'=>'CTB'   , 'Arquivo'=>'CTB.txt'   ),
                            array('Nome'=>'CVC'   , 'Arquivo'=>'CVC.txt'   ),
                            array('Nome'=>'IDE'   , 'Arquivo'=>'Ide.txt'   ),
                            array('Nome'=>'ECL'   , 'Arquivo'=>'ECL.txt'   ),
                            array('Nome'=>'EMP'   , 'Arquivo'=>'EMP.txt'   ),
                            array('Nome'=>'EOC'   , 'Arquivo'=>'EOC.txt'   ),
                            array('Nome'=>'EXT'   , 'Arquivo'=>'EXT.txt'   ),
                            array('Nome'=>'LQD'   , 'Arquivo'=>'LQD.txt'   ),
                            array('Nome'=>'OPS'   , 'Arquivo'=>'OPS.txt'   ),
                            array('Nome'=>'ORGAO' , 'Arquivo'=>'Orgao.txt' ),
                            array('Nome'=>'REC'   , 'Arquivo'=>'REC.txt'   ),
                            array('Nome'=>'RSP'   , 'Arquivo'=>'RSP.txt'   ),
                            array('Nome'=>'TRB'   , 'Arquivo'=>'TRB.txt'   )
                           );
}

if (Sessao::getExercicio() > '2011') {
    $arNomeArquivos[] = array('Arquivo'=>'TFR.txt' ,'Nome'=>'TFR' );
    $arNomeArquivos[] = array('Arquivo'=>'UOC.txt' ,'Nome'=>'UOC' );
    $arNomeArquivos[] = array('Arquivo'=>'DIC.txt' ,'Nome'=>'DIC' );
    $arNomeArquivos[] = array('Arquivo'=>'PAR.txt' ,'Nome'=>'PAR' );
    $arNomeArquivos[] = array('Arquivo'=>'DCL.txt' ,'Nome'=>'DCL' );
    $arNomeArquivos[] = array('Arquivo'=>'ISI.txt' ,'Nome'=>'ISI' );
    $arNomeArquivos[] = array('Arquivo'=>'DFR.txt' ,'Nome'=>'DFR' );
}

//apartir de 2012
if (Sessao::getExercicio() > 2012) {
    if (Sessao::getExercicio() < 2014) {
        $arNomeArquivos[] = array('Nome'=>'BLC' , 'Arquivo'=>'BLC.txt' );
    }
    $arNomeArquivos[] = array('Nome'=>'PCT' , 'Arquivo'=>'PCT.txt' );
}

//apartir de 2014
if (Sessao::getExercicio() >= 2014) {
    $arNomeArquivos[] = array('Nome'=>'ARP' ,'Arquivo'=>'ARP.txt' );
    $arNomeArquivos[] = array('Nome'=>'PRL' ,'Arquivo'=>'PRL.txt' );
    $arNomeArquivos[] = array('Nome'=>'HML' ,'Arquivo'=>'HML.txt' );
    $arNomeArquivos[] = array('Nome'=>'JGL' ,'Arquivo'=>'JGL.txt' );
    $arNomeArquivos[] = array('Nome'=>'HBL' ,'Arquivo'=>'HBL.txt' );
    $arNomeArquivos[] = array('Nome'=>'RPL' ,'Arquivo'=>'RPL.txt' );
    $arNomeArquivos[] = array('Nome'=>'ABL' ,'Arquivo'=>'ABL.txt' );
    $arNomeArquivos[] = array('Nome'=>'AAL' ,'Arquivo'=>'AAL.txt' );
    $arNomeArquivos[] = array('Nome'=>'LNC' ,'Arquivo'=>'LNC.txt' );
    $arNomeArquivos[] = array('Nome'=>'DSI' ,'Arquivo'=>'DSI.txt' );
    $arNomeArquivos[] = array('Nome'=>'DMR' ,'Arquivo'=>'DMR.txt' );
}

$obMes = new Mes();
$obMes->setNull( false );

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis = new RecordSet;
$rsArqDisponiveis->preenche($arNomeArquivos);

$rsArqDisponiveis->ordena("Nome","ASC",SORT_STRING);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo( "Arquivos" );
$obCmbArquivos->setNull  ( false );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis' );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis' );
$obCmbArquivos->setCampoId1  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc1( 'Nome' );
$obCmbArquivos->SetRecord1   ( $rsArqDisponiveis );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc2( 'Nome' );
$obCmbArquivos->SetRecord2   ( $rsArqSelecionados );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( "../../../exportacao/instancias/processamento/PRExportador.php" );
$obForm->setTarget ( "telaPrincipal" ); //oculto - telaPrincipal

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addTitulo            ( "Dados para geração de arquivos" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addComponente        ( $obISelectEntidade );
$obFormulario->agrupaComponentes    ( array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado) );
$obFormulario->addComponente        ( $obMes );

$obFormulario->addComponente        ($obCmbArquivos);

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
